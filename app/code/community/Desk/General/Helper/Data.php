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
class Desk_General_Helper_Data extends Mage_Core_Helper_Abstract
{
  /**
   * Path to store the endpoint
   *
   * @var string
   */
  const XML_PATH_OAUTH_ENDPOINT             = 'desk/general/endpoint';

  /**
   * Path to store the consumer key
   *
   * @var string
   */
  const XML_PATH_OAUTH_CONSUMER_KEY         = 'desk/general/consumer_key';

  /**
   * Path to store the consumer secret
   *
   * @var string
   */
  const XML_PATH_OAUTH_CONSUMER_SECRET      = 'desk/general/consumer_secret';

  /**
   * Path to store the token
   *
   * @var string
   */
  const XML_PATH_OAUTH_TOKEN                = 'desk/general/token';

  /**
   * Path to store the token secret
   *
   * @var string
   */
  const XML_PATH_OAUTH_TOKEN_SECRET         = 'desk/general/token_secret';

  /**
   * Returns the endpoint
   *
   * @return string
   */
  public function getEndpoint()
  {
    return Mage::getStoreConfig(self::XML_PATH_OAUTH_ENDPOINT);
  }

  /**
   * Returns the consumer key
   *
   * @return string
   */
  public function getConsumerKey()
  {
    return Mage::getStoreConfig(self::XML_PATH_OAUTH_CONSUMER_KEY);
  }

  /**
   * Returns the consumer secret
   *
   * @return string
   */
  public function getConsumerSecret()
  {
    return Mage::getStoreConfig(self::XML_PATH_OAUTH_CONSUMER_SECRET);
  }

  /**
   * Returns the token
   *
   * @return string
   */
  public function getToken()
  {
    return Mage::getStoreConfig(self::XML_PATH_OAUTH_TOKEN);
  }

  /**
   * Returns the token secret
   *
   * @return string
   */
  public function getTokenSecret()
  {
    return Mage::getStoreConfig(self::XML_PATH_OAUTH_TOKEN_SECRET);
  }

  /**
   * Returns the configuration for the library.
   *
   * @return array
   */
  public function getConfiguration()
  {
    return array(
      'endpoint'        => $this->getEndpoint(),
      'consumerKey'     => $this->getConsumerKey(),
      'consumerSecret'  => $this->getConsumerSecret(),
      'token'           => $this->getToken(),
      'tokenSecret'     => $this->getTokenSecret()
    );
  }

  /**
   * Returns a magento user by desk id
   *
   * @param string
   * @return Mage_Admin_Model_User|null
   */
  public function getUserByDeskId($id)
  {
    $user = Mage::getModel('admin/user')->load($id, 'desk_id');
    if ($user->getId() !== null) { return $user; }
    return null;
  }
}
