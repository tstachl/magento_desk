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
class Desk_Widget_Helper_Data extends Mage_Core_Helper_Abstract
{
  /**
   * Path to store enabled
   *
   * @var string
   */
  const XML_PATH_WIDGET_ENABLED             = 'desk/widget/enabled';

  /**
   * Path to store the access token
   *
   * @var string
   */
  const XML_PATH_WIDGET_ACCESS_TOKEN        = 'desk/widget/token';

  /**
   * Path to store the agent version
   *
   * @var string
   */
  const XML_PATH_WIDGET_AGENT_VERSION      = 'desk/widget/agent_version';

  /**
   * Path to store the origin
   *
   * @var string
   */
  const XML_PATH_WIDGET_ORIGIN             = 'desk/widget/origin';

  /**
   * Returns the enabled
   *
   * @return boolean
   */
  public function isEnabled()
  {
    return !!Mage::getStoreConfig(self::XML_PATH_WIDGET_ENABLED);
  }

  /**
   * Returns the access token
   *
   * @return string
   */
  public function getAccessToken()
  {
    return Mage::getStoreConfig(self::XML_PATH_WIDGET_ACCESS_TOKEN);
  }

  /**
   * Returns the agent version enumeral
   *
   * @return int
   */
  public function getAgentVersion()
  {
    return Mage::getStoreConfig(self::XML_PATH_WIDGET_AGENT_VERSION);
  }

  /**
   * Returns an array of sorted customer widgets.
   *
   * @return array
   */
  public function getOrigin()
  {
    return Mage::getStoreConfig(self::XML_PATH_WIDGET_ORIGIN);
  }

  public function getWidgets($type = 'customer')
  {
    return array(
      'desk_widget/customer_info',
      'desk_widget/customer_sales',
      'desk_widget/customer_orders'
    );
  }
}
