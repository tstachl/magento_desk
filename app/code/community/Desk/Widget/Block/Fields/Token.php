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
class Desk_Widget_Block_Fields_Token extends Mage_Adminhtml_Block_System_Config_Form_Field
{

  /**
   * Get element html
   *
   * @return string
   */
  protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
  {
    $element->setStyle('width: 205px;');

    $html  = parent::_getElementHtml($element);
    $label = Mage::helper('adminhtml')->__('Generate');

    $onclick = array(
      "document.getElementById('".$element->getHtmlId()."').value=",
      "'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'.replace(/x/g, function() { return (Math.random()*16|0).toString(36) });",
      "return false"
    );

    $html .= '<button style="margin-right: 2px;max-width: 69px;" onclick="'.join($onclick).'">'.$label.'</button>';

    return $html;
  }

}
