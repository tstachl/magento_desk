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
class Desk_Widget_DeskController extends Desk_General_DeskController
{

  /**
   * Initialize the customer if we have an id
   *
   * @return Desk_Widget_DeskController
   */
  protected function _initCustomer($idField = 'id')
  {
    $customerId = (int) $this->getRequest()->getParam($idField);
    $customer   = Mage::getModel('customer/customer');
    if ($customerId) { $customer->load($customerId); }
    Mage::register('current_customer', $customer);
    return $this;
  }

  /**
   * Initialize the order if we have an id
   *
   * @return Desk_Widget_DeskController
   */
  protected function _initOrder($idField = 'id')
  {
    $orderId = (int) $this->getRequest()->getParam($idField);
    $order = Mage::getModel('sales/order');
    if ($orderId) { $order->load($orderId); }
    Mage::register('current_order', $order);
    return $this;
  }

  /**
   * Override the preDispatch to run signed requests if we have one.
   *
   * @return void
   */
  public function preDispatch()
  {
    // run the s
    if (Mage::helper('desk_widget/signed')->isSignedRequest() &&
        Mage::helper('desk_widget/signed')->isAuthenticated()) {
      $response = Mage::app()->getResponse();
      $response->setRedirect(Mage::helper('adminhtml')->getUrl(
        '*/desk/widget',
        Mage::helper('desk_widget/signed')->getParams()
      ));
      $response->sendResponse();
      exit;
    }

    parent::preDispatch();
  }

  /**
   * Show the widget
   *
   * @return void
   */
  public function widgetAction()
  {
    $type = $this->getRequest()->getParam('type');
    if (isset($type) && trim($type) !== '') {
      $function = strtolower($type) . 'Type';
      if (method_exists($this, $function)) {
        $this->loadLayout();
        $this->$function();
        $this->renderLayout();
      }
    }
  }

  /**
   * Call the customer widget type function
   *
   * @return void
   */
  public function customerType()
  {
    $this->_initCustomer();
    $layout = $this->getLayout();
    $block  = $layout->getBlock('widget_list');

    foreach (Mage::helper('desk_widget')->getWidgets() as $widget) {
      $block->addWidget($layout->createBlock($widget));
    }
  }

  /**
   * Call the order widget type function
   *
   * @return void
   */
  public function orderType()
  {
    $this->_initCustomer('customer_id');
    $this->_initOrder();
  }

  /**
   * The search for customers / orders
   *
   * @return void
   */
  public function searchAction()
  {
    $this->loadLayout();

    $result = Mage::getModel('desk_widget/search_customer');
    $result->setQuery(Mage::app()->getRequest()->getParam('q'))
           ->load();
    $this->getLayout()->getBlock('search')
                      ->setResult($result->getResult())
                      ->setQuery(Mage::app()->getRequest()->getParam('q'))
                      ->setText('%d customer(s)');

    $this->renderLayout();
  }

}
