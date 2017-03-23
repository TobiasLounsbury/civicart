<?php


class CRM_Civicart_Tokens {

  /**
   * Looks up all the items we have available to add to the cart
   * and return a list that each context can turn into tokens
   * or short-codes.
   *
   * @return array
   */
  public static function getCartItems() {

    //Lookup the setting for which price set we are to use
    $priceSetId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_priceset",
      'name' => "civicart_priceset",
    ));



    //Get the list of items available to the cart
    $result = civicrm_api3('PriceField', 'get', array(
      'return' => array("id", "label", "name", "html_type"),
      'price_set_id' => $priceSetId,
      'is_active' => 1,
      'options' => array('limit' => 0),
      'api.PriceFieldValue.get' => array(
        'is_active' => 1,
        'return' => array("id", "name", "label"),
        'options' => array('limit' => 0),
      ),
    ));

    $priceFields = array();

    foreach($result['values'] as $priceField) {

      $priceFields[$priceField['id']] = array(
        "id" => $priceField['id'],
        "name" => $priceField['name'],
        "label" => $priceField['label'],
        "html_type" => $priceField['html_type'],
        "options" => array()
      );

      if($priceField['html_type'] == "CheckBox") {
        foreach ($priceField['api.PriceFieldValue.get'] as $value) {
          $priceFields[$priceField['id']]['options'][$value['id']] = array(
            "id" => $value['id'],
            "name" => $value['name'],
            "label" => $value['label']
          );
        }
      }
    }


    //Allow other extensions to alter the list of items
    CRM_Civicart_Hooks::tokenListHook($priceFields);

    return $priceFields;
  }


  /**
   * This function takes parameters either called from a civi BuildForm directly
   * or parsed from CMS tokens/shortcodes and returns the relevant item content
   *
   * Types:
   * * item - Renders a PriceField. This is any PriceField in the selected cart priceset
   * And will for checkboxes render all of the options with a single add to cart button.
   * * option - Used to add a single checkbox option out of the list.
   *
   * Available Contexts:
   * * full - Returns both a description (if available) and an "Add to Cart" button
   * * button - Returns an "Add to Cart" button.
   * * descriptions - Returns the Items description if available.
   *
   * @param $itemId
   * @param string $type
   * @param string $context
   * @return string
   */
  public static function getItemContent($itemId, $type = "item", $context = "full") {
    //Lookup the setting for which price set we are to use
    $priceSetId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_priceset",
      'name' => "civicart_priceset",
    ));

    $itemValues = array();

    if($type == "item") {
      //Get the PriceField
      try {
        $priceField = civicrm_api3('PriceField', 'getsingle', array(
          'return' => array("id", "label", "name", "html_type", "is_active", "price_set_id", "is_display_amounts"),
          'id' => $itemId,
          'is_active' => 1,
          'api.PriceFieldValue.get' => array(
            'is_active' => 1,
            'return' => array("id", "name", "label", "amount", "is_default"),
            'options' => array(
              'limit' => 0,
              'sort' => "weight asc",
              ),
          ),
        ));

        if($priceField['price_set_id'] != $priceSetId ||
          $priceField['is_active'] == 0
        ) {
          return "";
        }
      } catch (Exception $e) {
        return "";
      }

      $itemValues['id'] = $priceField['id'];
      $itemValues['label'] = $priceField['label'];
      $itemValues['name'] = $priceField['name'];
      $itemValues['html_type'] = $priceField['html_type'];
      $itemValues['is_display_amounts'] = $priceField['is_display_amounts'];

      //Set the Price for text items
      if($priceField['html_type'] == "Text") {
        $itemValues['amount'] = $priceField['api.PriceFieldValue.get']['values'][0]['amount'];
      } else {
        $itemValues['options'] = $priceField['api.PriceFieldValue.get']['values'];
      }

      //Set the Type
      $itemValues['type'] = "item";


    } elseif ($type == "option") {

      //Get the PriceFieldValue
      try {
        $priceFieldValue = civicrm_api3('PriceFieldValue', 'getsingle', array(
          'id' => $itemId,
          'api.PriceField.getsingle' => array(),
        ));
      } catch (Exception $e) {
        return "";
      }

      //Return an empty string if this item isn't enable, or isn't part of our priceset
      if($priceFieldValue['api.PriceField.getsingle']['price_set_id'] != $priceSetId ||
        $priceFieldValue['is_active'] == 0 ||
        $priceFieldValue['api.PriceField.getsingle']['is_active'] == 0
      ) {
        return "";
      }

      $itemValues['id'] = $priceFieldValue['id'];
      $itemValues['label'] = $priceFieldValue['label'];
      $itemValues['name'] = $priceFieldValue['name'];
      $itemValues['amount'] = $priceFieldValue['amount'];
      $itemValues['html_type'] = 'option';
      $itemValues['is_display_amounts'] = $priceFieldValue['api.PriceField.getsingle']['is_display_amounts'];
      $itemValues['type'] = "option";

    } else {
      return "";
    }

    //Create a template object
    $template = CRM_Core_Smarty::singleton();

    //Compose the initial template name
    $contextName = ucfirst(strtolower($context));
    $templateFile = "CRM/Civicart/Widget/{$contextName}.tpl";

    //Hook to alter the template widget name
    CRM_Civicart_Hooks::tokenTemplateHook($itemValues, $type, $templateFile);

    //Allow other extensions to load additional data into the item
    //Such as Quantity and Description.
    CRM_Civicart_Hooks::inventoryHook($itemValues, $context);

    //Assign data to the template
    foreach($itemValues as $key => $value) {
      $template->assign($key, $value);
    }

    $html = trim($template->fetch($templateFile));


    if($html) {
      CRM_Civicart_Utils::addLibraryResources();
    }

    return $html;
  }

}