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
class Desk_Widget_Block_Widget_List extends Mage_Adminhtml_Block_Template
{

  /**
   * Widget Collection
   *
   * @var array
   */
  protected $_widgetCollection = array();

  /**
   * Adds a widget to this list view.
   *
   * @return Desk_Widget_Block_Widget_List
   */
  public function addWidget(Desk_Widget_Block_Widget $widget, $sort = 10)
  {
    array_push($this->_widgetCollection, array(
      'widget' => $widget,
      'sort' => $sort
    ));

    return $this;
  }

  /**
   * Returns the widget collection sorted.
   *
   * @return array
   */
  public function getWidgetCollection()
  {
    $widgets   = array();
    $sortOrder = array();

    foreach ($this->_widgetCollection as $item) {
      array_push($widgets, $item['widget']);
      array_push($sortOrder, $item['sort']);
    }

    array_multisort($widgets, $sortOrder);

    return $widgets;
  }

  /**
   * Returns the data origin for post message
   *
   * @return string
   */
  public function getOrigin()
  {
    return Mage::helper('desk_widget')->getOrigin();
  }
}
