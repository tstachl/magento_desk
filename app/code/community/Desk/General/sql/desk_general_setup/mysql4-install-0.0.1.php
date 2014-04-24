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
$this->startSetup();

// add column to user table
$this->getConnection()->addColumn(
  $this->getTable('admin/user'),
  'desk_id',
  'VARCHAR(255) DEFAULT NULL'
);

// add key to table for the new field
$this->getConnection()->addKey(
  $this->getTable('admin/user'),
  $this->getIdxName('admin/user', array('desk_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
  'desk_id',
  Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$this->endSetup();
