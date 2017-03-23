<?php

return array(
  'civicart_priceset' => array(
    'group_name' => ts('CiviCart Settings'),
    'group' => 'com.tobiaslounsbury.civicart',
    'name' => 'civicart_priceset',
    'type' => 'Int',
    'default' => NULL,
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Inventory Price-Set'),
    'help_text' => ts('The Price-Set used to house all of the items that are for sale.'),
    'required' => true
  ),
);