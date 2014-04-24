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
class Desk_Widget_Model_Search_Customer extends Varien_Object
{

  /**
   * Attributes to search
   *
   * @var array
   */
  protected $_attributes = array(
    'firstname', 'lastname', 'email', 'billing_postcode',
    'billing_street', 'billing_city', 'billing_telephone',
    'billing_region', 'billing_country_id', 'company'
  );

  /**
   * Override the setData to allow for different handeling of query
   *
   * @param string/array
   * @param mixed
   * @return Desk_Widget_Model_Search_Customer
   */
  public function setData($key, $value = null)
  {
    if (strtolower($key) === 'query') {
      $value = $this->_splitQuery($value);
    }

    return parent::setData($key, $value);
  }

  /**
   * Load search results
   *
   * @return Mage_Adminhtml_Model_Search_Customer
   */
  public function load()
  {
    $customers = Mage::getResourceModel('customer/customer_collection');
    $customers->addNameToSelect()
              ->joinAttribute('company', 'customer_address/company', 'default_billing', null, 'left')
              ->joinAttribute('billing_street', 'customer_address/street', 'default_billing', null, 'left')
              ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
              ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
              ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
              ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
              ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

    if (count($this->getQuery()) < 1) {
      $customers->addAttributeToFilter('id', 0)->load();
    } else {
      // add filter
      foreach ($this->getQuery() as $term) {
        $filter = array();
        foreach ($this->_attributes as $attrib) {
          $filter[] = array('attribute' => $attrib, 'like' => '%' . $term . '%');
        }
        $customers->addAttributeToFilter($filter);
      }
      $customers->load();

      // weigh result
      foreach ($customers as $customer) {
        $score = 0;
        foreach ($this->_regEscape() as $term) {
          $content = '';
          foreach ($this->_attributes as $attrib) {
            $content = ' ' . $customer->getData($attrib);
          }
          $score += preg_match_all("/$term/i", $content, $null);
        }
        $customer->setScore($score);
      }

      // sort the result
      uasort($customers, "$this->_sortResults");
    }

    $this->setResult($customers);
    return $this;
  }

  /**
   * Sort customers by the score param
   *
   * @param Mage_Customer_Model_Customer
   */
  protected function _sortResults($a, $b)
  {
    if ($a->getScore() == $b->getScore()) { return 0; }
    return ($a->getScore() > $b->getScore()) ? -1 : 1;
  }

  /**
   * transforms terms
   *   1. Convert whitespace between brackets into something non-whitespacey.
   *   2. Split on whitespace.
   *   3. Convert the 'something' back to the whitespace it was, for each token.
   *
   * @param string
   * @return string
   */
  protected function _transformTerm($term = '')
  {
    $term = preg_replace("/(\s)/e", "'{WHITESPACE-'.ord('\$1').'}'", $term);
    $term = preg_replace("/,/", "{COMMA}", $term);
    return $term;
  }

  /**
   * Escape terms into regular expressions to weigh result.
   *
   * @param array
   * @return array
   */
  protected function _regEscape()
  {
    $out = array();
    foreach ($this->getQuery() as $term) {
      $out[] = '\b' . preg_quote($term, '/') . '\b';
    }
    return $out;
  }

  /**
   * Prepare and return the search terms
   *
   * @return array
   */
  protected function _splitQuery($terms = '')
  {
    $terms = preg_replace_callback("/\"(.*?)\"/e", "$this->_transformTerm('\$1')", $terms);
    $terms = preg_split("/\s+|,/", $terms);

    $out = array();
    foreach ($terms as $term) {
      $term = preg_replace("/\{WHITESPACE-([0-9]+)\}/e", "chr(\$1)", $term);
      $term = preg_replace("/\{COMMA\}/", ",", $term);

      $out[] = $term;
    }

    return $out;
  }
}
