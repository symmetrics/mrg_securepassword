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
 * Setup model
 *
 * @category  Symmetrics
 * @package   Symmetrics_SecurePassword
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_SecurePassword_Model_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Add attribute to an entity type
     *
     * @param string|integer $entityTypeId EAV entity type (e.g. customer)
     * @param string         $code         Attribute code
     * @param array          $attr         Attribute data
     * 
     * @return Symmetrics_SecurePassword_Model_Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        parent::addAttribute($entityTypeId, $code, $attr);
        
        foreach ($attr as $entity => $value) {
            $this->updateAttribute($entityTypeId, $code, $entity, $value);
        }
        
        return $this;
    }
}
