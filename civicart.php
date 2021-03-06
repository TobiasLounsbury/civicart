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
 * Implements hook_civicrm_permission().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_permission
 *
 * @param $permissions
 */
function civicart_civicrm_permission(&$permissions) {

  $prefix = ts('CiviCart') . ': ';

  $permissions['access CiviCart'] = array(
    $prefix . ts('access CiviCart'),                     // label
    ts('allow access to use the CiviCart shopping Cart.'),  // description
  );
}


/**
 * Implementation of hook_civicrm_alterTemplateFile
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterTemplateFile
 *
 * Used to change which template file is loaded for the checkout page.
 *
 * @param $formName
 * @param $form
 * @param $context
 * @param $tplName
 */
function civicart_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  if($formName == "CRM_Contribute_Form_Contribution_Main") {
    $formId = $form->getVar("_id");

    //Fetch the form ID
    $checkoutId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_contribution_page",
      'name' => "civicart_contribution_page",
    ));

    if ($formId == $checkoutId) {
      $tplName = "CRM/Civicart/Form/Checkout.tpl";
    }
  }
}


/**
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 *
 * Add Cart Settings link to the Administer->CiviContribute menu.
 *
 * @param $formName
 * @param $form
 */
function civicart_civicrm_buildForm($formName, &$form) {

  $types = array(
    "CRM_Contribute_Form_Contribution_Main",
    "CRM_Contribute_Form_Contribution_Confirm",
  );

  if(in_array($formName, $types)) {
    $formId = $form->getVar("_id");

    //Fetch the form ID
    $checkoutId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_contribution_page",
      'name' => "civicart_contribution_page",
    ));

    if ($formId == $checkoutId) {
      if($formName == "CRM_Contribute_Form_Contribution_Main") {
        CRM_Civicart_Checkout::buildForm($form);
      } else if ($formName == "CRM_Contribute_Form_Contribution_Confirm") {
        CRM_Civicart_Checkout::buildConfirm($form);
      }
    }
  }
}


/**
 * Implementation of hook_civicrm_postProcess
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 *
 * Empties the cart when the contribution page configured as the "cart"
 * is processed.
 *
 * @param $formName
 * @param $form
 */
function civicart_civicrm_postProcess($formName, &$form) {
  //todo: Check to see if the checkout page is configured to use a
  //confirmation page, and if not, clear the cart when the Main
  //form is processed

  if($formName == "CRM_Contribute_Form_Contribution_Confirm") {
    $formId = $form->getVar("_id");

    //Fetch the form ID
    $checkoutId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_contribution_page",
      'name' => "civicart_contribution_page",
    ));

    if ($formId == $checkoutId) {
      CRM_Civicart_Utils::emptyCart();
    }
  }
}
