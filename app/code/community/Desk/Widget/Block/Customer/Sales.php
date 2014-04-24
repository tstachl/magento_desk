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
class Desk_Widget_Block_Customer_Sales extends Desk_Widget_Block_Widget
{

  /**
   * Mage sales collection
   *
   * @var Mage_Sales_Model_Resource_Sales_Collection
   */
  protected $_collection;

  /**
   * Mage grouped sales collection
   *
   * @var Mage_Sales_Model_Resource_Sales_Collection
   */
  protected $_groupedCollection;

  /**
   * Array counts the orders per website
   *
   * @var array
   */
  protected $_websiteCounts;

  /**
   * Currency helper
   *
   * @var Mage_Directory_Model_Currency
   */
  protected $_currency;

  /**
   * Template
   *
   * @var string
   */
  protected $_template = 'desk/customer/sales.phtml';

  /**
   * returns the header text
   *
   * @return string
   */
  public function getHeaderText()
  {
    if (is_null($this->_headerText)) {
      $this->_headerText = Mage::helper('desk_widget')->__('Sales Stats');
    }
    return $this->_headerText;
  }

  /**
   * Sets up the information
   *
   * @return parent
   */
  public function _beforeToHtml()
  {
    $this->_currency = Mage::getModel('directory/currency')
      ->load(Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE));

    $this->_collection = Mage::getResourceModel('sales/sale_collection')
      ->setCustomerFilter(Mage::registry('current_customer'))
      ->load();

    $this->_groupedCollection = array();

    foreach ($this->_collection as $item) {
      if (!is_null($item->getStoreId())) {
        $store      = Mage::app()->getStore($item->getStoreId());
        $websiteId  = $store->getWebsiteId();
        $groupId    = $store->getGroupId();
        $storeId    = $store->getId();

        $item->setWebsiteId($store->getWebsiteId());
        $item->setWebsiteName($store->getWebsite()->getName());
        $item->setGroupId($store->getGroupId());
        $item->setGroupName($store->getGroup()->getName());
      } else {
        $websiteId  = 0;
        $groupId    = 0;
        $storeId    = 0;

        $item->setStoreName(Mage::helper('customer')->__('Deleted Stores'));
      }

      $this->_groupedCollection[$websiteId][$groupId][$storeId] = $item;
      $this->_websiteCounts[$websiteId] = isset($this->_websiteCounts[$websiteId]) ? $this->_websiteCounts[$websiteId] + 1 : 1;
    }

    return parent::_beforeToHtml();
  }

  /**
   * returns the website count for the given website
   *
   * @param int
   * @return int
   */
  public function getWebsiteCount($websiteId)
  {
    return isset($this->_websiteCounts[$websiteId]) ? $this->_websiteCounts[$websiteId] : 0;
  }

  /**
   * returns grouped collection
   *
   * @return array
   */
  public function getRows()
  {
    return $this->_groupedCollection;
  }

  /**
   * returns the totals
   *
   * @return
   */
  public function getTotals()
  {
    return $this->_collection->getTotals();
  }

  /**
   * Currency format helper
   *
   * @return string
   */
  public function getPriceFormatted($price)
  {
    return $this->_currency->format($price);
  }
}
