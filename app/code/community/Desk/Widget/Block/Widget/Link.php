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
class Desk_Widget_Block_Widget_Link extends Mage_Adminhtml_Block_Widget
{

  /**
   * generate the html for this link
   *
   * @return string
   */
  protected function _toHtml()
  {
    $html = $this->getBeforeHtml() . '<a '
        . ($this->getId() ? ' id="' . $this->getId() . '"' : '')
        . ($this->getHref() ? ' href="' . $this->getHref() . '"' : '')
        . ' title="'
        . Mage::helper('core')->quoteEscape($this->getTitle() ? $this->getTitle() : $this->getLabel())
        . '"'
        . ($this->getDataAction() ? ' data-action="' . $this->getDataAction() . '"' : '');

    // add all the data arg
    foreach ($this->getData() as $key => $value) {
      if (strpos($key, 'data_arg') !== false) {
        $html .= ' ' . str_replace('_', '-', $key) . '="' . $value . '"';
      }
    }

    $html .= ($this->getClass() ? ' class="' . $this->getClass() . '"' : '')
        . ($this->getOnClick() ? ' onclick="' . $this->getOnClick() . '"' : '')
        . ($this->getStyle() ? ' style="' . $this->getStyle() . '"' : '')
        . '>' . $this->getLabel() . '</a>' . $this->getAfterHtml();

    return $html;
  }
}
