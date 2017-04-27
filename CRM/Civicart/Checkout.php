<?php

class CRM_Civicart_Checkout {

  /**
   * Make modifications to the checkout form.
   *
   * @param $form
   */
  public static function buildForm(&$form) {

    //Add our theme file to the form.
    CRM_Core_Resources::singleton()->addStyleFile("com.tobiaslounsbury.civicart", "css/civicart.css");

    //add the civicart-checkout class to the form
    $classes = CRM_Utils_Array::value("class", $form->_attributes, "");
    $classes .= " civicart-checkout";
    $form->_attributes['class'] = $classes;

    //Fetch the cart contents
    $contents = CRM_Civicart_Utils::getCartContents();

    //Fetch the Item Details
    foreach($contents as &$item) {
      $option = CRM_Utils_Array::value("option", $item, false);
      $tmp = CRM_Civicart_Items::getItemData($item['id'], $option);
      $item = array_merge($tmp, $item);
    }


    //Fetch the price fields for this price set.
    $result = civicrm_api3('PriceField', 'get', array(
      'return' => array("id"),
      'price_set_id' => $form->_priceSetId,
    ));

    $priceFieldIds = array_keys($result['values']);

    //Remove the price fields from the form that were added
    foreach($priceFieldIds as $pFieldId) {
      $form->removeElement("price_{$pFieldId}");
    }


    //Add hidden elements for each item the user had in their cart.
    foreach ($contents as $lineItem) {
      switch ($lineItem['html_type']) {
        case "Text":
          $form->add("hidden", "price_{$lineItem['id']}", $lineItem['quantity']);
          break;
        case "option":
          $form->add("hidden", "price_{$lineItem['id']}[{$lineItem['option']}]", $lineItem['option']);
          break;
        case "Select":
        case "Radio":
          $form->add("hidden", "price_{$lineItem['id']}", $lineItem['option']);
          break;
      }
    }

    //Fetch the Button Text
    $buttonText = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_checkout_button_text",
      'name' => "civicart_checkout_button_text",
    ));

    //Update the Button Text
    $btnIndex = $form->_elementIndex['buttons'];
    $form->_elements[$btnIndex]->_elements[0]->setAttribute('value', $buttonText);
  }

  public static function buildConfirm(&$form) {
    //Fetch the Button Text
    $buttonText = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_confirm_button_text",
      'name' => "civicart_confirm_button_text",
    ));

    $form->assign("button", $buttonText);
    //Update the Button Text
    $btnIndex = $form->_elementIndex['buttons'];
    $form->_elements[$btnIndex]->_elements[0]->setAttribute('value', $buttonText);
  }

}