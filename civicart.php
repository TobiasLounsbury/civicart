<?php

require_once 'civicart.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicart_civicrm_config(&$config) {
  _civicart_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicart_civicrm_xmlMenu(&$files) {
  _civicart_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicart_civicrm_install() {
  _civicart_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicart_civicrm_uninstall() {
  _civicart_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicart_civicrm_enable() {
  _civicart_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicart_civicrm_disable() {
  _civicart_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicart_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicart_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civicart_civicrm_managed(&$entities) {
  _civicart_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicart_civicrm_caseTypes(&$caseTypes) {
  _civicart_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicart_civicrm_angularModules(&$angularModules) {
_civicart_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicart_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicart_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function civicart_civicrm_preProcess($formName, &$form) {

}

*/


/**
 * Implementation of hook_civicrm_navigationMenu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 * Add Cart Settings link to the Administer->CiviContribute menu.
 *
 * @param $params
 */
function civicart_civicrm_navigationMenu( &$params ) {
  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
  $contributeMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'CiviContribute', 'id', 'name');

  $newKey = _civicart_getMenuKeyMax($params) + 1;

  // skip adding menu if there is no administer menu
  if ($administerMenuId && $contributeMenuId) {
    $params[$administerMenuId]['child'][$contributeMenuId]['child'][$newKey] =  array (
      'attributes' => array (
        'label'      => 'CiviCart Settings',
        'name'       => 'civicart_settings',
        'url'        => 'civicrm/cart/settings',
        'permission' => 'administer CiviCRM',
        'operator'   => NULL,
        'separator'  => false,
        'parentID'   => $contributeMenuId,
        'navID'      => $newKey,
        'active'     => 1
      )
    );
  }
}

/**
 * Helper function for getting the highest key in the navigation menu.
 *
 * Taken from http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu.
 *
 * @param array $menuArray
 * @return int
 */
function _civicart_getMenuKeyMax($menuArray) {
  $max = array(max(array_keys($menuArray)));
  foreach($menuArray as $v) {
    if (!empty($v['child'])) {
      $max[] = _civicart_getMenuKeyMax($v['child']);
    }
  }
  return max($max);
}


/**
 * Implementation of hook_civicrm_tokens
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokens
 *
 *  Adds the shopping cart tokens to the list of available tokens.
 *
 * @param $tokens
 */
function civicart_civicrm_tokens( &$tokens ) {
  $tokens['cart'] = CRM_Civicart_Tokens::getCartTokens();
}


/**
 * Implementation of hook_civicrm_tokenValues
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tokenValues
 *
 * Used to replace a token with a cart item's html either a button or a description
 *
 * @param $values
 * @param $cids
 * @param null $job
 * @param array $tokens
 * @param null $context
 */
function civicart_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {

}