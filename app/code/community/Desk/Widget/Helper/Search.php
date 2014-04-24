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
class Desk_Widget_Helper_Search extends Mage_Core_Helper_Abstract
{

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
   * Prepare and return the search terms
   *
   * @return array
   */
  public function getTerms()
  {
    $terms = Mage::app()->getRequest()->getParam('q');
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
