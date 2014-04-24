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
 * @package    Desk_General
 * @copyright  Copyright (c) 2013-2014 Salesforce.com Inc. (http://www.salesforce.com)
 */

/**
 * @category    Desk
 * @package     Desk_General
 * @copyright   Copyright (c) 2013-2014 Salesforce.com Inc. (http://www.salesforce.com)
 */
class Desk_General_Model_Observer
{
  /**
   * Add a new button next to the existing "Save and Continue Edit" button
   *
   * @return void
   */
  public function addSyncButton()
  {
    if (Mage::app()->getRequest()->getParam('section') === 'desk') {
      $layout  = Mage::app()->getLayout();
      $content = $layout->getBlock('content.child1');
      $button  = $content->getChild('save_button');
      $onclick = 'deskSync.call(this, \'' .
                    Mage::getModel('adminhtml/url')->getUrl('*/desk/sync') .
                 '\')';

      // add button
      $content->setChild('save_button', $layout->createBlock(
        'core/text_list', 'button_container'
      )->append(
        $layout->createBlock('adminhtml/widget_button')
               ->setData(array(
                 'label'    => Mage::helper('desk_general')->__('Sync Users'),
                 'onclick'  => $onclick
               ))
      )->append($button));

      // add javascript
      $layout->getBlock('js')->append($layout->createBlock('core/text')->setText('
        <script type="text/javascript">
          function deskSync(url) {
            // send request
            new Ajax.Request(url, {
              onComplete: function(r) {
                try {
                  var cls = r.status !== 200 ? "error-msg" : "success-msg"
                    , msg = [
                        "<ul class=\"messages\">",
                        "  <li class=\"" + cls + "\">",
                        "    <ul>",
                        "      <li>" + r.responseJSON.message + "</li>",
                        "    </ul>",
                        "  </li>",
                        "</ul>"
                      ].join("\n")

                  $("messages").update(msg);
                  setTimeout(function() { $("messages").update(""); }, 5000);
                } catch(err) { console.error(err); }
              }
            });
          }
        </script>
      '));
    }
  }
}
