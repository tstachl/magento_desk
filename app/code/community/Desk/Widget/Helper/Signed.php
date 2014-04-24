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
class Desk_Widget_Helper_Signed extends Mage_Core_Helper_Abstract
{

  /**
   * returns the signed request param
   *
   * @return string
   */
  public function getSignedRequest()
  {
    return Mage::app()->getRequest()->getParam('signed_request');
  }

  /**
   * returns true if we have a signed request
   *
   * @return boolean
   */
  public function isSignedRequest()
  {
    $signedRequest = Mage::app()->getRequest()->getParam('signed_request');
    return isset($signedRequest);
  }

  /**
   * returns true if the user can be authenticated
   *
   * @return boolean
   */
  public function isAuthenticated()
  {
    list($encoded_sig, $payload) = explode('.', $this->getSignedRequest());

    // decode the data
    $signature  = self::base64Decode($encoded_sig);
    $data       = json_decode(self::base64Decode($payload), true);

    // confirm signature
    $token = Mage::helper('desk_widget')->getAccessToken();
    $expected_sig = hash_hmac('sha256', $payload, $token, true);

    // signature matches
    if ($signature === $expected_sig) {
      // check the timestamp
      if (isset($data['expires']) && time() < $data['expires']) {
        $user = $this->getUserFromData($data);

        // TODO copied from app/code/core/Mage/Admin/Model/User.php:339
        // any better idea?
        if ($user->getIsActive() != '1' || !$user->hasAssigned2Role($user->getId())) {
          return false;
        }

        $this->createAdminSession($user);
        return true;
      }
    }

    return false;
  }

  /**
   * Creates an admin session for the user.
   *
   * @param Mage_Admin_Model_User
   * @return void
   */
  public function createAdminSession($user)
  {
    Mage::getSingleton(
      'core/session',
      array('name' => Mage_Adminhtml_Controller_Action::SESSION_NAMESPACE)
    )->start();

    $session = Mage::getSingleton('admin/session');
    $session->setUser($user);
    $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
  }

  /**
   * Looks up the user by the provided data
   *
   * @param array
   * @return Mage_Admin_Model_User
   */
  public function getUserFromData($data = array())
  {
    $user = Mage::getModel('admin/user');

    // check for facebooks standard
    if (isset($data['user_id']) && trim($data['user_id']) !== '') {
      $userId = $data['user_id'];
    // check for salesforces standard
    } elseif (isset($data['userId']) && trim($data['userId']) !== '') {
      $userId = $data['userId'];
    }

    if ($userId) {
      // are we only getting the id?
      if (strpos($userId, '/api/v2') === false) {
        $userId = "/api/v2/users/$userId";
      }

      // load the user
      $user->load($userId, 'desk_id');
    }

    return $user;
  }

  /**
   * returns the redirect params
   *
   * @return array
   */
  public function getParams()
  {
    $params = Mage::app()->getRequest()->getParams();
    unset($params['signed_request']);
    return $params;
  }

  /**
   * base64 decode the string
   *
   * @param string
   * @return string
   */
  public function base64Decode($str)
  {
    return base64_decode(strtr($str, '-_', '+/'));
  }

}
