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
class Desk_General_DeskController extends Mage_Adminhtml_Controller_Action
{

  /**
   * This method is going to sync desk users with magento users
   *
   * @return void
   */
  public function syncAction()
  {
    $this->getResponse()->setHeader('Content-type', 'application/json');
    try {
      $client = new Desk_Client(Mage::helper('desk_general')->getConfiguration());
      $users  = $client->users;

      // make sure the credentials are valid.
      if (!isset($users->entries)) { throw new Desk_Exception('API credentials are not valid.'); }

      // run through the users
      do {
        foreach ($users->entries as $user) {
          // update the magento user
          $mageUser = Mage::getModel('admin/user')->load($user->email, 'email');
          if ($mageUser->getId()) {
            $mageUser->setData('desk_id', $user->getSelf());
            $mageUser->save();
          }
        }
      } while ($users = $users->next);

      // respond sucessful
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
        'success' => true,
        'message' => 'Users have been synced.'
      )));
    } catch(Exception $e) {
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
        'success' => false,
        'message' => $e->getMessage()
      )));
    }
  }
}
