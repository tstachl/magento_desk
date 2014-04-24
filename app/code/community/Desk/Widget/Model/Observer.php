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
class Desk_Widget_Model_Observer
{

  /**
   * Force template if template var is set
   *
   * @return void
   */
  public function forceLayoutEvent()
  {
    $template = Mage::app()->getRequest()->getParam('template');
    if (isset($template) && $template === 'lightbox') {
      $layout = Mage::app()->getLayout();
      $layout->unsetBlock('header')
             ->unsetBlock('menu')
             ->getBlock('root')
               ->setTemplate('popup.phtml')
               ->unsetChild('footer');

      // change setLocation to work as popup
      $layout->getBlock('js')->append($layout->createBlock('core/text')->setText('
        <script type="text/javascript">
          function setLocation(url) {
            // open new tab
            window.open(url);
          }
        </script>
      '));
      $layout->getBlock('js')->append($layout->createBlock('core/text')->setText('
        <style>
          html, body, .middle { background: white; }
        </style>
      '));
    }
  }
}
