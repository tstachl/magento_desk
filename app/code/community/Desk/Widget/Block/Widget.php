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
class Desk_Widget_Block_Widget extends Mage_Adminhtml_Block_Template
{

  /**
   * Header text
   *
   * @var string
   */
  protected $_headerText;

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
  protected $_template;

  /**
   * Links
   *
   * @var array
   */
  protected $_links = array();

  /**
   * returns the header text
   *
   * @return string
   */
  public function getHeaderText()
  {
    return $this->_headerText;
  }

  /**
   * returns collapsed flag
   *
   * @return boolean
   */
  public function isCollapsed()
  {
    return $this->_isCollapsed;
  }

  /**
   * returns collapsed class
   *
   * @return string
   */
  public function getCollapsedClass()
  {
    return $this->isCollapsed() ? ' collapsed' : '';
  }

  /**
   * Prepare layout and add some css goodness
   *
   * @return something
   */
  protected function _prepareLayout()
  {
    // set the template
    if (is_null($this->_template)) {
      $this->getLayout()->setTemplate($this->_template);
    }

    return parent::_prepareLayout();
  }

  /**
   * Prepare html output
   *
   * @return string
   */
  protected function _toHtml()
  {
    Mage::dispatchEvent('desk_widget_html_before', array('block' => $this));
    return parent::_toHtml();
  }

  /**
   * Adds a widget to this list view.
   *
   * @return Desk_Widget_Block_Widget
   */
  public function addLink(Desk_Widget_Block_Widget_Link $link, $sort = 10)
  {
    array_push($this->_links, array(
      'link' => $link,
      'sort' => $sort
    ));

    return $this;
  }

  /**
   * Returns the widget collection sorted.
   *
   * @return array
   */
  public function getLinks()
  {
    $links     = array();
    $sortOrder = array();

    foreach ($this->_links as $item) {
      array_push($links, $item['link']);
      array_push($sortOrder, $item['sort']);
    }

    array_multisort($links, $sortOrder);

    return $links;
  }
}
