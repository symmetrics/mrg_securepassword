<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_SecurePassword
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 
/**
 * Observer model
 *
 * @category  Symmetrics
 * @package   Symmetrics_SecurePassword
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_SecurePassword_Model_Observer
{
    /**
     * Before customer is saved
     * 
     * @param Varien_Event_Observer $observer Event observer object
     * 
     * @return Symmetrics_SecurePassword_Model_Observer
     */
    public function customerSave($observer)
    {
        $customer = $observer->getCustomer();
        if ($customer->getEmail() == $customer->getPassword()) {
            Mage::throwException(Mage::helper('securepassword')->__('Your email and password can not be equal.'));
        }
        
        if ($customer->getUnlockCustomer() == 1) {
            $now = time();
            $customer->setLastUnlockTime($now)
                ->setUnlockCustomer(0)
                ->setFailedLogins(0)
                ->setLastFailedLogin(0);
        }
        
        return $this;
    }
    
    /**
     * When customer tries to login
     * 
     * @param Varien_Event_Observer $observer Event observer object
     * 
     * @return Symmetrics_SecurePassword_Model_Observer
     */
    public function customerPostLogin($observer)
    {
        if (!$this->_getSession()->isLoggedIn()) {
            //login failed
            $loginParams = $observer->getControllerAction()->getRequest()->getParams();
            if (isset($loginParams['login']) && isset($loginParams['login']['username'])) {
                $loginParams = $loginParams['login'];            
                $validator = new Zend_Validate_EmailAddress();
                if ($validator->isValid($loginParams['username'])) {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setStore($this->_getStore())
                        ->loadByEmail($loginParams['username']);
                    if ($customer->getId()) {
                        $attempts = $customer->getFailedLogins();
                        $lastAttempt = $customer->getLastFailedLogin();
                        $now = time();
                        if (!is_numeric($attempts)) {
                            $attempts = 1;
                        } else {
                            if ($now - $lastAttempt > $this->_getStoreConfig('attemptSpan')) {
                                $attempts = 0;
                            }
                            $attempts++;
                        }
                        $customer->setFailedLogins($attempts);
                        $customer->setLastFailedLogin($now);
                        $customer->save();
                    }
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Check for customer lock
     * 
     * @param Varien_Event_Observer $observer Event observer object
     * 
     * @return Symmetrics_SecurePassword_Model_Observer
     */
    public function customerPreLogin($observer)
    {
        $controllerAction = $observer->getControllerAction();
        try {
            $loginParams = $controllerAction->getRequest()->getParams();
            if (isset($loginParams['login'])) {
                $loginParams = $loginParams['login'];
                $validator = new Zend_Validate_EmailAddress();
                if ($validator->isValid($loginParams['username'])) {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setStore($this->_getStore())
                        ->loadByEmail($loginParams['username']);
                    if (!$customer->getId()) {
                        throw new Exception('Login failed.');
                    }
                    
                    $lastAttempt = $customer->getLastFailedLogin();
                    $lastUnlock = $customer->getLastUnlockTime();
                    
                    $unlocked = ($lastUnlock > 0 && $lastUnlock > $lastAttempt);
                    
                    if ($unlocked) {
                        $customer->setFailedLogins(0)
                            ->setLastFailedLogin(0)
                            ->save();
                    }
                    
                    $attempts = $customer->getFailedLogins();
                    $lastAttempt = $customer->getLastFailedLogin();
                    $now = time();
                    $attemptLock = $attempts >= $this->_getStoreConfig('loginAttempts');
                    $timeLock = ($now - $lastAttempt < $this->_getStoreConfig('lockTime'));
                    if ($attemptLock && $timeLock && !$unlocked) {
                        throw new Exception(
                            'Your account is locked due to too many failed login attempts.'
                        );
                    }
                } else {
                    throw new Exception(
                        'The email address you entered is invalid.'
                    );
                }
            }
        } catch (Exception $e) {
            $this->_getSession()
                ->addError(Mage::helper('securepassword')->__($e->getMessage()));
            $response = $controllerAction->getResponse();
            $response->setRedirect(Mage::helper('customer')->getLoginUrl());
            $response->sendResponse();
            die();
        }
        
        return $this;
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Get currently selected store
     * 
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }
    
    /**
     * Get id of current store
     * 
     * @return int
     */
    protected function _getStoreId()
    {
        return $this->_getStore()->getId();
    }
    
    /**
     * Get password settings from system configuration
     * 
     * @param string $parameter parameter to get
     *
     * @return mixed
     */
    protected function _getStoreConfig($parameter)
    {
        return Mage::getStoreConfig('customer/password/' . $parameter, $this->_getStore());
    }
}
