<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civicart_Form_Settings extends CRM_Core_Form {


  /**
   * All settings, as fetched via API, keyed by setting name.
   *
   * @var array
   */
  protected $_settings = array();
  protected $_priceSets = array();
  protected $_settingsMetadata = array();

  function preProcess() {
    parent::preProcess();

    //Preload the Settings
    $result = civicrm_api3('Setting', 'getfields');
    $this->_settingsMetadata = ($result['count'] > 0) ? $result['values'] : array();
    $currentDomainId = civicrm_api3('Domain', 'getvalue', array(
      'return' => 'id',
      'current_domain' => 1,
    ));
    $setting = civicrm_api3('Setting', 'get');
    $this->_settings = $setting['values'][$currentDomainId];


    //Load our fields.
    $this->settingsFields = include("settings/civicart.setting.php");


    $result = civicrm_api3('PriceSet', 'get', array(
      'return' => array("title", "id"),
      //In my tests there are price-sets that have no domain.
      // I'm not sure if this is because I am on a single site instance,
      // or if there is data corruption on my development site,
      // or if price-sets are never really for a specific domain (least likely imo)
      // For now I'm going to leave this commented out and re-address it when needed.
      //todo: This should be properly addressed on a multisite system and appropriate logic added
      //'domain_id' => $currentDomainId,
      'options' => array('limit' => 0),
      'extends' => "CiviContribute",
      'is_reserved' => 0,
      'is_active' => 1,
    ));


    $this->_priceSets = array();

    foreach($result['values'] as $priceSet) {
      $this->_priceSets[$priceSet['id']] = $priceSet['title'];
    }

  }


  function buildQuickForm() {

    // add form elements
    $this->add(
      'select', // field type
      'civicart_priceset', // field name
      ts('Inventory Price-Set'), // field label
      $this->_priceSets, // list of options
      true // is required
    );

    $manualFields = array("civicart_priceset");
    $groups = array(ts("CiviCart Settings") => array("civicart_priceset"));
    $helpText = array();

    foreach($this->settingsFields as $field => $info) {

      $helpText[$field] = array("title" => $info['description'], "message" => $info['help_text']);

      if (!in_array($field, $manualFields)) {
        $groups[$info['group_name']][] = $field;

        $type = (array_key_exists("widget", $info)) ? $info['widget'] : false;
        if (!$type) {
          $type = ($info['type'] == "String") ? 'textarea' : "text";
        }

        $this->add(
          $type, // field type
          $field, // field name
          $info['description'], // field label
          array(), // list of options
          (array_key_exists("required", $info)) ? $info['required'] : true, // is required
          array() //extra
        );

      }
    }

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save Settings'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign("groups", $groups);
    $this->assign("metadata", $this->settingsFields);

    CRM_Core_Resources::singleton()->addSetting(array('Civicart' => array('Help' => $helpText)));

    parent::buildQuickForm();
  }


  function setDefaultValues() {
    $defaults = array();

    foreach($this->settingsFields as $field => $info) {
      $defaults[$field] = CRM_Utils_Array::value($field, $this->_settings, $info['default']);
    }

    return $defaults;
  }

  function postProcess() {
    $values = $this->exportValues();


    foreach($this->settingsFields as $field => $info) {

      civicrm_api3('Setting', 'create', array(
        "{$field}" => CRM_Utils_Array::value($field, $values, $info['default'])
      ));

    }

    CRM_Core_Session::setStatus(ts("Changes Saved", array('domain' => 'com.tobiaslounsbury.civicart')), "Saved", "success");

    parent::postProcess();
  }

}
