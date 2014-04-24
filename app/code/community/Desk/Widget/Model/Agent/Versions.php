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
class Desk_Widget_Model_Agent_Versions
{

  /**
   * Agent version option array
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
      array('value' => 0, 'label' => Mage::helper('desk_widget')->__('Auto-Detect')),
      array('value' => 1, 'label' => Mage::helper('desk_widget')->__('v2')),
      array('value' => 2, 'label' => Mage::helper('desk_widget')->__('v3')),
    );
  }
}
