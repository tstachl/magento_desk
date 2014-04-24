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
class Desk_Widget_Block_Customer_Orders extends Desk_Widget_Block_Widget
{

  /**
   * Currency helper
   *
   * @var Mage_Directory_Model_Currency
   */
  protected $_currency;

  /**
   * Holds the order collection
   *
   * @var Mage_Sales_Model_Order_Grid_Collection
   */
  protected $_collection;

  /**
   * Template
   *
   * @var string
   */
  protected $_template = 'desk/customer/orders.phtml';

  /**
   * Search link
   *
   * @var Desk_Widget_Block_Widget_Link
   */
  protected $_searchLink;

  /**
   * returns the header text
   *
   * @return string
   */
  public function getHeaderText()
  {
    if (is_null($this->_headerText)) {
      $this->_headerText = Mage::helper('desk_widget')->__(
        'Recent Orders (%d)',
        $this->getCollection()->getSize()
      );
    }
    return $this->_headerText;
  }

  /**
   * returns the currency helper
   *
   * @return Mage_Directory_Model_Currency
   */
  public function getCurrency()
  {
    if (is_null($this->_currency)) {
      $this->_currency = Mage::getModel('directory/currency')
        ->load(Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE));
    }

    return $this->_currency;
  }

  /**
   * returns the collection
   *
   * @return Mage_Sales_Model_Order_Grid_Collection
   */
  public function getCollection()
  {
    if (is_null($this->_collection)) {
      $this->_collection = Mage::getResourceModel('sales/order_grid_collection')
        ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
        ->setIsCustomerMode(true);
    }
    return $this->_collection;
  }

  public function getRowUrl($order)
  {
    return $this->getUrl(
      '*/sales_order/view', array(
        'order_id' => $order->getId(),
        '_query' => array('template' => 'lightbox')
      )
    );
  }

  /**
   * Currency format helper
   *
   * @return string
   */
  public function getPriceFormatted($price)
  {
    return $this->getCurrency()->format($price);
  }

  /**
   * returns the search link
   *
   * @return Desk_Widget_Block_Widget_Link
   */
  public function getSearchLink()
  {
    if (is_null($this->_searchLink)) {
      $this->_searchLink = $this->getLayout()->createBlock(
        'desk_widget/widget_link', 'search_link'
      );
      $this->_searchLink
        ->setLabel(Mage::helper('desk_widget')->__('Search'))
        ->setTitle(Mage::helper('desk_widget')->__('Search Orders'))
        ->setHref($this->getUrl('*/*/search', array(
          'type' => 'order',
          'customer_id' => Mage::registry('current_customer')->getId()
        )))
        ->setDataAction('modal');
    }

    return $this->_searchLink;
  }


  /**
   * Prepare layout and add some css goodness
   *
   * @return something
   */
  protected function _prepareLayout()
  {
    // add a search link
    $this->addLink($this->getSearchLink());
    parent::_prepareLayout();
  }
}
