<?php
/**
 * Desk
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/MIT
 *
 * @category   Desk
 * @package    Desk_Widget
 * @copyright  Copyright (c) 2013-2014 Salesforce.com Inc. (http://www.salesforce.com)
 */

/**
 * @category    Desk
 * @package     Desk_Widget
 * @copyright   Copyright (c) 2013-2014 Salesforce.com Inc. (http://www.salesforce.com)
 */
class Desk_Widget_Block_Customer_Info extends Desk_Widget_Block_Widget
{

  /**
   * Initial collapsed flag
   *
   * @var boolean
   */
  protected $_isCollapsed = true;

  /**
   * Template
   *
   * @var string
   */
  protected $_template = 'desk/customer/info.phtml';

  /**
   * returns the header text
   *
   * @return string
   */
  public function getHeaderText()
  {
    if (is_null($this->_headerText)) {
      $this->_headerText = $this->getCustomer()->getName();
    }
    return $this->_headerText;
  }

  /**
   * returns the current customer
   *
   * @return Mage_Customer_Model_Customer
   */
  public function getCustomer()
  {
    return Mage::registry('current_customer');
  }

  /**
   * returns the current customers default address
   *
   * @return Mage_Customer_Model_Address
   */
  public function getAddress()
  {
    return Mage::getModel('customer/address')
      ->load($this->getCustomer()->getDefaultBilling());
  }

  /**
   * returns the current customers group
   *
   * @return Mage_Customer_Model_Group
   */
  public function getGroup()
  {
    return Mage::getModel('customer/group')
      ->load($this->getCustomer()->getGroupId());
  }

  /**
   * returns the current customers website
   *
   * @return Mage_Core_Model_Website
   */
  public function getWebsite()
  {
    return Mage::getModel('core/website')
      ->load($this->getCustomer()->getWebsiteId());
  }
}
