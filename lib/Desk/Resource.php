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
 * @package    Desk_Resource
 * @copyright  Copyright (c) 2013 Salesforce.com Inc. (http://www.salesforce.com)
 */

/**
 * @category    Desk
 * @package     Desk_Client
 * @copyright   Copyright (c) 2013 Salesforce.com Inc. (http://www.salesforce.com)
 */
class Desk_Resource
{

  /**
   * The resource definition.
   *
   * @var array
   */
  private $_definition = null;

  /**
   * The API client to be used.
   *
   * @var Desk_Client
   */
  private $_client = null;

  /**
   * An array holding the changes to the resource.
   *
   * @var array
   */
  private $_changes = array();

  /**
   * Indicator of the load state for this resorce.
   *
   * @var boolean
   */
  private $_loaded = false;

  /**
   * Constructor
   *
   * @param array       $definition the resource definition
   * @param Desk_Client $client     the client to be used
   */
  public function __construct(array $definition = array(), Desk_Client $client, $loaded = false)
  {
    $this->_definition = $definition;
    $this->_client     = $client;
    $this->_loaded     = $loaded;
    $this->_setup();
  }

  /**
   * Return the self link
   *
   * @return string
   */
  public function getSelf()
  {
    return $this->_definition['_links']['self']['href'];
  }

  /**
   * Creates a new resource from the currents resources base.
   *
   * @param  array  $params the payload for the new resource
   * @return Desk_Resource
   */
  public function create(array $params = array())
  {
    $baseUrl  = $this->_cleanBaseUrl();
    $response = $this->_client->post($baseUrl, $params);
    if ($response->isSuccessful()) {
      return new Desk_Resource(
        Zend_Json::decode($response->getBody()), $this->_client, true
      );
    } else {
      return $response;
    }
  }

  /**
   * Allows you to update a resource.
   *
   * @param  array  $params changes that should be made to this resource
   * @return Desk_Resource
   */
  public function update(array $params = array())
  {
    if ($this->_definition['_links']['self']['class'] == 'page') {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'Pages can not be updated.'
      );
    }

    foreach ($params as $key => $value) {
      $this->__set($key, $value);
    }

    $self = $this->_definition['_links']['self']['href'];
    $response = $this->_client->patch($self, $this->_changes);

    if ($response->isSuccessful()) {
      $this->_definition = Zend_Json::decode($response->getBody());
      $this->_changes = array();
      $this->_setup();
      return $this;
    } else {
      return $response;
    }
  }

  /**
   * Allows you to destroy resources you no longer need.
   *
   * @return boolean
   */
  public function destroy()
  {
    if ($this->_definition['_links']['self']['class'] == 'page') {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'Pages can not be updated.'
      );
    }

    $response = $this->_client->delete($this->getSelf());
    return $response->isSuccessful();
  }

  /**
   * Allows you to search resources.
   *
   * @param  array  $params search parameters
   * @return Desk_Resource
   */
  public function search(array $params = array())
  {
    $baseUrl  = $this->_cleanBaseUrl();
    $response = $this->_client->get($baseUrl . '/search', $params);

    if ($response->isSuccessful()) {
      return new Desk_Resource(
        Zend_Json::decode($response->getBody()), $this->_client, true
      );
    } else {
      return $response;
    }
  }

  /**
   * Retrieve a property value, link resource or embedded resource.
   * @param  string $name the name of the property
   * @return mixed
   */
  public function __get($name)
  {
    if (!$this->_loaded) $this->_execute();
    if (array_key_exists($name, $this->_definition)) {
      return array_key_exists($name, $this->_changes) ? $this->_changes[$name] : $this->_definition[$name];
    } elseif (array_key_exists($name, $this->_definition['_links'])) {
      return $this->_definition['_links'][$name];
    } elseif (array_key_exists($name, $this->_definition['_embedded'])) {
      return $this->_definition['_embedded'][$name];
    } else {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'Property with the name `' . $name . '` not found.'
      );
    }
  }

  /**
   * Change a property on this resource.
   *
   * @param string $name  the name of the property
   * @param mixed  $value the new value for the property
   */
  public function __set($name, $value)
  {
    if (!$this->_loaded) $this->_execute();
    if (isset($this->_definition[$name])) {
      $this->_changes[$name] = $value;
    } else {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'Property with the name `' . $name . '` not found.'
      );
    }
  }

  /**
   * Check if the property is defined.
   *
   * @param  string  $name the name of the property
   * @return boolean
   */
  public function __isset($name)
  {
    if (!$this->_loaded) $this->_execute();
    return array_key_exists($name, $this->_definition)
      || array_key_exists($name, $this->_definition['_links'])
      || array_key_exists($name, $this->_definition['_embedded']);
  }

  /**
   * Setup the resource.
   *
   * @return void
   */
  protected function _setup()
  {
    if (isset($this->_definition['_links'])) {
      foreach ($this->_definition['_links'] as $key => $value) {
        if ($key != 'self' && $value) {
          $this->_definition['_links'][$key] = new Desk_Resource(array(
            '_links' => array(
              'self' => $value
            )
          ), $this->_client);
        }
      }
    }

    if (isset($this->_definition['_embedded'])) {
      foreach ($this->_definition['_embedded'] as $key => $value) {
        if ($key == 'entries') {
          foreach ($this->_definition['_embedded'][$key] as $k => $v) {
            $this->_definition['_embedded'][$key][$k] = new Desk_Resource($v, $this->_client, true);
          }
        } else {
          $this->_definition['_embedded'][$key] = new Desk_Resource($value, $this->_client, true);
        }
      }
    }
  }

  /**
   * Cleans the current url to the main object and returns it.
   *
   * @return string base url of the current resource
   */
  private function _cleanBaseUrl()
  {
    $pattern = array('/\/search$/', '/\/\d+$/');
    return preg_replace($pattern, '', explode('?', $this->getSelf())[0]);
  }

  /**
   * Helper function that loads the current resource.
   *
   * @return void
   */
  private function _execute()
  {
    $response = $this->_client->get($this->getSelf());
    $this->_definition = Zend_Json::decode($response->getBody());
    $this->_loaded = true;
    $this->_setup();
  }

  /**
   * Unsets an arrays key after returning the value
   *
   * @param  array  $arr   array that'll be used
   * @param  string $key   the key to unset
   * @return mixed         the value to be returned
   */
  public static function arrayRemove(array &$arr = array(), $key = null)
  {
    if (!isset($arr[$key])) return null;
    $value = $arr[$key];
    unset($arr[$key]);
    return $value;
  }

}
