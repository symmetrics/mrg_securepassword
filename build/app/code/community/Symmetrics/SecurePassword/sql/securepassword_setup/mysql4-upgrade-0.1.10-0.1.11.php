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

$installer = $this;
$installer->startSetup();

$attributeData = array(
    'label' => 'Failed logins',
    'input' => 'int',
    'type'  => 'text',
    'is_visible' => false,
    'required' => false,
    'user_defined' => false,
);
$installer->addAttribute('customer', 'failed_logins', $attributeData);

$attributeData = array(
    'label' => 'Last failed login',
    'input' => 'text',
    'type'  => 'int',
    'is_visible' => false,
    'required' => false,
    'user_defined' => false,
);
$installer->addAttribute('customer', 'last_failed_login', $attributeData);

$attributeData = array(
    'label' => 'Last unlock time',
    'input' => 'text',
    'type'  => 'int',
    'is_visible' => false,
    'required' => false,
    'user_defined' => false,
);
$installer->addAttribute('customer', 'last_unlock_time', $attributeData);

$attributeData = array(
    'label' => 'Unlock customer',
    'input' => 'select',
    'type'  => 'int',
    'frontend_label' => 'Unlock customer',
    'visible' => true,
    'is_visible' => true,
    'required' => false,
    'user_defined' => true,
    'is_user_defined' => true,
    'is_visible_on_front' => false,
    'default' => 0,
    'source' => 'eav/entity_attribute_source_boolean'
);
$installer->addAttribute('customer', 'unlock_customer', $attributeData);

$installer->endSetup();
