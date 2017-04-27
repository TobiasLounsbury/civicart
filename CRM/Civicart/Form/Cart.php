<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civicart_Form_Cart extends CRM_Core_Form {

  private $contents;

  public function __construct($state = NULL, $action = CRM_Core_Action::NONE, $method = 'post', $name = NULL) {
    //Add an update handler
    $updateAction = new CRM_Civicart_UpdateCartAction();
    $this->addAction("update", $updateAction);

    parent::__construct($state, $action, $method, $name);
  }

  function preProcess() {
    parent::preProcess();
    $this->preloadContents();
  }

  function buildQuickForm() {

    //Add the resources needed to render the cart
    $res = CRM_Core_Resources::singleton();
    //Add the CiviCart styles
    $res->addStyleFile("com.tobiaslounsbury.civicart", "css/civicart.css");
    $res->addScriptFile("com.tobiaslounsbury.civicart", "js/civicart.cart.js");


    $this->add("hidden", "action", "proceed");
    $this->add("hidden", "remove", "");


    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Proceed to Checkout'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'update',
        'name' => ts('Update Cart'),
        'isDefault' => false,
      ),
    ));


    //Add the Items as a javascript setting
    $res->addSetting(array("CiviCart" => array("items" => $this->contents)));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Set Default Values
   *
   * @return array|NULL
   */
  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    //$defaults['action'] = "proceed";
    return $defaults;
  }


  /**
   * Validate the Form
   *
   * @return bool
   */
  function validate() {
    $values = $this->exportValues();

    $action = CRM_Utils_Array::value("action", $values, "proceed");

    switch($action) {
      case 'proceed':
        break;
      case 'remove':
        break;
      case 'update':
        break;
    }

    return parent::validate();
  }


  function updateAction() {
    $reload = false;
    foreach ($this->contents as $key => $item) {
      $value = CRM_Utils_Array::value("{$key}_qty", $_POST, false);
      if($value && $value != $item['quantity']) {
        $reload = true;
        CRM_Civicart_Utils::updateItemQty($key, $value);
      }
    }
    if($reload) {
      $this->preloadContents();
    }
  }


  function postProcess() {
    $values = $this->exportValues();

    $action = CRM_Utils_Array::value("action", $values, "proceed");

    switch($action) {
      case 'proceed':

        //Fetch the form ID
        $checkoutId = civicrm_api3('Setting', 'getvalue', array(
          'return' => "civicart_contribution_page",
          'name' => "civicart_contribution_page",
        ));

        CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/contribute/transact", array("reset" => 1, "id" => $checkoutId)));

        break;
      case 'remove':
        list($id, $option) = explode("_", $values['remove']);
        CRM_Civicart_Utils::removeFromCart($id, $option);
        $this->preloadContents();
        //CRM_Core_Session::setStatus("Item Removed");
        break;
      case 'update':
        $this->updateAction();
        break;
    }

    parent::postProcess();
  }


  /**
   * Helper function to preload the contents
   */
  function preloadContents() {
    //Load the contents of the cart.
    $this->contents = CRM_Civicart_Utils::getCartContents();

    //Fetch the Item Details
    foreach($this->contents as &$item) {
      $option = CRM_Utils_Array::value("option", $item, false);
      $tmp = CRM_Civicart_Items::getItemData($item['id'], $option);
      $item = array_merge($tmp, $item);
      $item['lineTotal'] = $item['quantity'] * $item['amount'];
      $item['lineTotal'] = CRM_Utils_Money::format($item['lineTotal']);
      $item['amount'] = CRM_Utils_Money::format($item['amount']);
      $item['html'] = CRM_Civicart_Items::renderItemContent($item,"cart");
    }

    $this->assign("cartTotal", CRM_Civicart_Utils::getCartTotal(true));

    $this->assign("items", $this->contents);
  }


  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
