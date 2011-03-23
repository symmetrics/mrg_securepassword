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
        } else {
            // Login succeeded
            $customer = $this->_getSession()->getCustomer();
            $customer->setFailedLogins(0)
                ->save();
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
                    
                    $this->setCustomerLockStatus($customer);
                    
                } else {
                    throw new Exception(
                        'The email address you entered is invalid.'
                    );
                }
            }
        } catch (Exception $e) {
            $this->_getSession()
                ->addError(Mage::helper('securepassword')->__($e->getMessage()));

            /**
             * Please note: It is not the best way to just change the content of
             * this server variable, but there is no way to force a redirect
             * from an Event Observer the Magento way.
             * It is either this, overriding a controller or doing a
             * header();die();
             * This way works, because the loginpost action - which is executed
             * directly after this event - checks explicitly for the request
             * method and goes straigt to the post dispatch event if it isn't
             * POST.
             */
            $loginUrl = Mage::helper('customer')->getLoginUrl();
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $this->_getSession()->setBeforeAuthUrl($loginUrl);
            $response = $controllerAction->getResponse();
            $response->setRedirect($loginUrl);
            $response->sendResponse();
        }
        
        return $this;
    }
    
    /**
     * Check if customer can be unlocked
     *
     * @param Mage_Customer_Model_Customer $customer Customer model
     *
     * @return void
     */
    public function setCustomerLockStatus($customer)
    {
        $now = time();
        $lockTime = $this->_getStoreConfig('lockTime');
        
        $lastAttempt = $customer->getLastFailedLogin();
        $lastUnlock = $customer->getLastUnlockTime();
        
        $unlockedAdmin = ($lastUnlock > 0 && $lastUnlock > $lastAttempt);
        $unlockedTime = ($now - $lastAttempt > $lockTime);
        $unlocked = ($unlockedAdmin || $unlockedTime);
        
        if ($unlocked) {
            $customer->setFailedLogins(0)
                ->setLastFailedLogin(0)
                ->save();
        }
        
        $attempts = $customer->getFailedLogins();
        $lastAttempt = $customer->getLastFailedLogin();
        $attemptLock = $attempts >= $this->_getStoreConfig('loginAttempts');
        $timeLock = ($now - $lastAttempt < $lockTime);
        
        if ($attemptLock && $timeLock && !$unlocked) {
            throw new Exception(
                'Your account is locked due to too many failed login attempts.'
            );
        }
    }
   
    /**
     * Check the customer password, i.e. it should not be equal to user's email
     * 
     * @param Varien_Event_Observer $observer Event observer object
     * 
     * @return Symmetrics_SecurePassword_Model_Observer
     */
    public function checkCustomerPassword($observer)
    {
        $controllerAction = $observer->getControllerAction();
        /* @var Mage_Checkout_Model_Type_Onepage $onepageCheckout */
        $onepageCheckout = $controllerAction->getOnepage();

        //  Magento version 1.4.0.1 has a typo in method name
        if (version_compare(Mage::getVersion(), '1.4.0.1', '=')) {
            $method = $onepageCheckout->getCheckoutMehod();
        } else {
            $method = $onepageCheckout->getCheckoutMethod();
        }
        
        // check if chekout is done in 'register user' mode
        if ($method != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            return $this;
        }

        // obtain email and password
        $params = $observer->getControllerAction()->getRequest()->getParams();
        $email = $params['billing']['email'];
        $password = $params['billing']['customer_password'];

        // assert that both are not equal
        if ($email == $password) {
            $error = array(
                'error'   => -1,
                'message' => Mage::helper('securepassword')->__('Your email and password can not be equal.'),
            );
            $controllerAction->getResponse()
                ->setBody(Mage::helper('core')->jsonEncode($error));
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
