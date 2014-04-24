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
class Desk_General_Model_Validation_Token extends Mage_Core_Model_Config_Data
{
  public function save()
  {
    $fields   = $this->groups['general']['fields'];
    $hasValue = true;

    foreach ($fields as $key => $value) {
      if (!isset($value['value']) || trim($value['value']) === '') {
        $hasValue = false;
      }
    }

    if ($hasValue) {
      try {
        $client = new Desk_Client(array(
          'endpoint'        => $fields['endpoint']['value'],
          'consumerKey'     => $fields['consumer_key']['value'],
          'consumerSecret'  => $fields['consumer_secret']['value'],
          'token'           => $fields['token']['value'],
          'tokenSecret'     => $fields['token_secret']['value']
        ));

        if (!isset($client->cases->entries)) {
          throw new Desk_Exception('API credentials are not valid.');
        }
      } catch(Exception $e) {
        Mage::throwException(
          Mage::helper('desk_general')->__('API credentials are not valid.')
        );
      }
    }

    return parent::save();
  }
}
