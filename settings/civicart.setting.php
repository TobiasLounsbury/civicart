<?php

return array(

  'civicart_contribution_page' => array(
    'group_name' => ts('CiviCart Settings'),
    'group' => 'com.tobiaslounsbury.civicart',
    'name' => 'civicart_contribution_page',
    'type' => 'Int',
    'default' => NULL,
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Contribution Page'),
    'help_text' => ts('The Contribution page used for Checkout, and from which to draw the price-set items'),
    'required' => true
  ),

  'civicart_add_button_text' => array(
    'group_name' => ts('CiviCart Settings'),
    'group' => 'com.tobiaslounsbury.civicart',
    'name' => 'civicart_add_button_text',
    'type' => 'String',
    'default' => ts("Add to Cart"),
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Add to Cart Button Text'),
    'help_text' => ts('The text used when rendering an "Add to Cart" button'),
    'required' => true,
    'widget' => "Text"
  ),

  'civicart_checkout_button_text' => array(
    'group_name' => ts('CiviCart Settings'),
    'group' => 'com.tobiaslounsbury.civicart',
    'name' => 'civicart_checkout_button_text',
    'type' => 'String',
    'default' => ts("Proceed"),
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Checkout Form Button Text'),
    'help_text' => ts('The text to use on the submit button for the checkout form.'),
    'required' => true,
    'widget' => "Text"
  ),

  'civicart_confirm_button_text' => array(
    'group_name' => ts('CiviCart Settings'),
    'group' => 'com.tobiaslounsbury.civicart',
    'name' => 'civicart_confirm_button_text',
    'type' => 'String',
    'default' => ts("Complete Purchase"),
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Confirm Form Button Text'),
    'help_text' => ts('The text to use on the submit button for the checkout confirmation form.'),
    'required' => true,
    'widget' => "Text"
  ),

);