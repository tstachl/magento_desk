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
 * @package    Desk_Client
 * @copyright  Copyright (c) 2013 Salesforce.com Inc. (http://www.salesforce.com)
 */

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * @see Zend_Oauth_Client
 */
require_once 'Zend/Oauth/Client.php';

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @see Zend_Oauth_Token_Access
 */
require_once 'Zend/Oauth/Token/Access.php';

/**
 * @see Desk_Resource
 */
require_once 'Desk/Resource.php';

/**
 * @category    Desk
 * @package     Desk_Client
 * @copyright   Copyright (c) 2013 Salesforce.com Inc. (http://www.salesforce.com)
 */
class Desk_Client extends Desk_Resource
{

  /**
   * Retry flag
   *
   * @var boolean
   */
  protected $_retry = null;

  /**
   * Max retry limit.
   *
   * @var integer
   */
  protected $_maxRetry = null;

  /**
   * Desk.com API endpoint
   *
   * @var Zend_Uri_Http
   */
  protected $_endpoint;

  /**
   * HTTP Client to be used
   *
   * @var Zend_Http_Client|Zend_Oauth_Client
   */
  protected $_client;

  /**
   * Constructor
   *
   * @param  array $options options array
   * @return void
   * @throws Desk_Exception
   */
  public function __construct($options)
  {
    if ($options instanceof Zend_Config) {
      $options = $options->toArray();
    }

    if (!is_array($options)) {
      $options = array();
    }

    $this->setRetry(self::arrayRemove($options, 'retry'));
    $this->setMaxRetry(self::arrayRemove($options, 'maxRetry'));

    $endpoint   = self::arrayRemove($options, 'endpoint');
    $subdomain  = self::arrayRemove($options, 'subdomain');

    if (!$endpoint && !$subdomain) {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'No endpoint or subdomain specified.'
      );
    }

    $this->_endpoint = Zend_Uri::factory(
      $endpoint ? $endpoint : 'https://' . $subdomain . '.desk.com'
    );

    $options['siteUrl'] = $this->_endpoint->getUri();

    $username     = self::arrayRemove($options, 'username');
    $password     = self::arrayRemove($options, 'password');
    $token        = self::arrayRemove($options, 'token');
    $tokenSecret  = self::arrayRemove($options, 'tokenSecret');

    if ($username && $password) {
      $this->_client = new Zend_Http_Client();
      $this->_client->setAuth($username, $password);
    } elseif (isset($options['consumerKey']) && isset($options['consumerSecret'])
            && $token && $tokenSecret) {
      $tokenAccess   = new Zend_Oauth_Token_Access();
      $this->_client = $tokenAccess->setToken($token)
                                  ->setTokenSecret($tokenSecret)
                                  ->getHttpClient($options);
    } else {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'No authentication specified, use either Basic Authentication or OAuth.'
      );
    }

    parent::__construct(
      Zend_Json::decode(file_get_contents(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources.json'
      )),
      $this,
      true
    );
  }

  /**
   * Set the URI path to use in the request
   *
   * @param string|Zend_Uri_Http $uri URI for the web service
   * @return Zend_Rest_Client
   */
  public function setPath($path)
  {
    if ($path[0] !== '/') {
      $path = '/' . $path;
    }

    $this->_endpoint->setPath($path);
    $this->_client->setUri($this->_endpoint);

    return $this;
  }

  /**
   * Retrieve the current request URI path
   *
   * @return Zend_Uri_Http
   */
  public function getPath()
  {
    return $this->_client->getUri()->getPath();
  }

  /**
   * Set the retry flag.
   *
   * @param boolean $flag
   * @return self
   */
  public function setRetry($flag = true)
  {
    $this->_retry = $flag;
    return $this;
  }

  /**
   * Get the retry flag.
   *
   * @return boolean
   */
  public function getRetry()
  {
    return $this->_retry;
  }

  /**
   * Set the max retry limit.
   *
   * @param integer $limit
   * @return self
   */
  public function setMaxRetry($limit = 3)
  {
    $this->_maxRetry = $limit;
    return $this;
  }

  /**
   * Get the max retry limit.
   *
   * @return integer
   */
  public function getMaxRetry()
  {
    return $this->_maxRetry;
  }

  /**
   * Send off the actual request to the API
   * @param  string $method  GET|POST|PATCH|DELETE
   * @param  string $path    the api path (/api/v2/cases)
   * @param  mixed  $payload an array or string to be sent as payload
   * @throws Desk_Exception
   * @return Desk_Response
   */
  protected function _request($method, $path, $payload = null, $tries = 0)
  {
    $this->_client->resetParameters();
    $this->_client->setMethod($method);
    $this->setPath($path);

    if ($payload && $method == 'GET') {
      $this->_client->setParameterGet($payload);
    } elseif ($payload && ($method == 'POST' || $method == 'PATCH')) {
      if (!is_string($payload)) {
        $payload = Zend_Json::encode($payload);
      }
      $this->_client->setRawData($payload, 'application/json');
    }

    try {
      $tries += 1;
      $response = $this->_client->request();
    } catch(Exception $e) {}

    if ($response && $response->getStatus() == 429) {
      $reset = $response->getHeader('X-Rate-Limit-Reset');
      if (is_array($reset)) $reset = $reset[0];
      $sleep = intval($reset);
    } elseif ($response && $response->getStatus() > 499) {
      $sleep = 5;
    } elseif (!$response || $response->isError()) {
      include_once 'Desk/Exception.php';
      throw new Desk_Exception(
        'An unknown error occured, please check your configuration.'
      );
    }

    if ($this->getRetry() && $tries <= $this->getMaxRetry() && $sleep) {
      sleep($sleep);
      return $this->_request($method, $path, $payload, $tries);
    }

    return $response;
  }

  /**
   * Send a GET request to the API.
   *
   * @param  string $path  the path to call
   * @param  array  $query an optional array of query parameters
   * @return Zend_Http_Response
   */
  public function get($path, array $query = null)
  {
    return $this->_request('GET', $path, $query);
  }

  /**
   * Send a POST request to the API.
   *
   * @param  string         $path     the path to call
   * @param  array|string   $payload  the payload either as array or json string
   * @return Zend_Http_Response
   */
  public function post($path, $payload)
  {
    return $this->_request('POST', $path, $payload);
  }

  /**
   * Send a PATCH request to the API.
   *
   * @param  string         $path     the path to call
   * @param  array|string   $payload  the payload either as array or json string
   * @return Zend_Http_Response
   */
  public function patch($path, $payload)
  {
    return $this->_request('PATCH', $path, $payload);
  }

  /**
   * Send a DELETE request to the API.
   *
   * @param  string         $path     the path to call
   * @return Zend_Http_Response
   */
  public function delete($path)
  {
    return $this->_request('DELETE', $path);
  }

}
