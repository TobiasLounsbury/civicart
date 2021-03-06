<?php


class CRM_Civicart_Items {

  /**
   * Looks up all the items we have available to add to the cart
   * and return a list that each context can turn into tokens
   * or short-codes.
   *
   * @return array
   */
  public static function getAllItems() {

    $priceSetId = CRM_Civicart_Utils::getCartPriceSet();
    $priceFields = array();

    if($priceSetId) {

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


      //Do some normalization of the price field so other extensions can modify the data.
      foreach ($result['values'] as $priceField) {

        $priceFields[$priceField['id']] = array(
          "id" => $priceField['id'],
          "name" => $priceField['name'],
          "label" => $priceField['label'],
          "html_type" => $priceField['html_type'],
          "options" => array()
        );

        if ($priceField['html_type'] == "CheckBox") {
          foreach ($priceField['api.PriceFieldValue.get']['values'] as $value) {
            $priceFields[$priceField['id']]['options'][$value['id']] = array(
              "id" => $value['id'],
              "name" => $value['name'],
              "label" => $value['label']
            );
          }
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
   *   And will for checkboxes render all of the options with a single add to cart button.
   * * option - Used to add a single checkbox option out of the list.
   *
   * @param Int $itemId
   * @param Int|Boolean $option
   * @return array|Boolean
   */
  public static function getItemData($itemId, $option = false) {
    //Lookup the setting for which price set we are to use
    $priceSetId = CRM_Civicart_Utils::getCartPriceSet();

    $itemValues = array();


    //We are working with an Item
    if (!$option) {
      //Get the PriceField
      try {
        //Fetch the PriceField
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

        if ($priceField['price_set_id'] != $priceSetId || $priceField['is_active'] == 0) {
          return false;
        }
      } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
      }

      $itemValues['id'] = $priceField['id'];
      $itemValues['label'] = $priceField['label'];
      $itemValues['name'] = $priceField['name'];
      $itemValues['html_type'] = $priceField['html_type'];
      $itemValues['is_display_amounts'] = $priceField['is_display_amounts'];

      //Set the Price for text items
      if ($priceField['html_type'] == "Text") {
        $itemValues['amount'] = $priceField['api.PriceFieldValue.get']['values'][0]['amount'];
      } else {
        //Set the Options if we aren't working with a text field.
        $itemValues['options'] = $priceField['api.PriceFieldValue.get']['values'];
      }

      //Set the Type
      $itemValues['type'] = "item";


    } elseif (is_numeric($option)) {

      //Get the PriceFieldValue
      try {
        $priceFieldValue = civicrm_api3('PriceFieldValue', 'getsingle', array(
          'id' => $option,
          'price_field_id' => $itemId,
          'api.PriceField.getsingle' => array(),
        ));
      } catch (Exception $e) {
        return false;
      }

      //Return an empty string if this item isn't enable, or isn't part of our priceset
      if ($priceFieldValue['api.PriceField.getsingle']['price_set_id'] != $priceSetId ||
        $priceFieldValue['is_active'] == 0 ||
        $priceFieldValue['api.PriceField.getsingle']['is_active'] == 0
      ) {
        return false;
      }

      //Do some normalization
      $itemValues['id'] = $priceFieldValue['price_field_id'];
      $itemValues['option'] = $priceFieldValue['id'];
      $itemValues['label'] = $priceFieldValue['label'];
      $itemValues['name'] = $priceFieldValue['name'];
      $itemValues['amount'] = $priceFieldValue['amount'];
      $itemValues['html_type'] = 'Option';
      $itemValues['is_display_amounts'] = $priceFieldValue['api.PriceField.getsingle']['is_display_amounts'];
      $itemValues['type'] = "Option";

    } else {
      //todo: Should we do something with a hook here that would allow other
      //todo: Extensions to provide their own types?
      return false;
    }

    //Allow other extensions to load additional data into the item
    //Such as Quantity and Description.
    CRM_Civicart_Hooks::inventoryHook($itemValues);

    return $itemValues;
  }

  
  /**
   * Renders an Item for a given context.
   *
   * Available Contexts:
   * * full - Returns both a description (if available) and an "Add to Cart" button
   * * button - Returns an "Add to Cart" button.
   * * description - Returns the Items description if available.
   * * cart - Used to add a row to the Cart
   *
   * @param $itemValues
   * @param string $context
   * @return string
   */
  public static function renderItemContent($itemValues, $context = "full") {
    global $civicartAddButtonText;
    //Create a template object
    $template = CRM_Core_Smarty::singleton();

    //Compose the initial template name
    $contextName = ucfirst(strtolower($context));
    $templateFile = "CRM/Civicart/Widget/{$contextName}.tpl";

    //Hook to alter the template widget name
    CRM_Civicart_Hooks::tokenTemplateHook($itemValues, $context, $templateFile);

    $itemValues['isQty'] = ($itemValues['html_type'] == "Text");

    $itemValues['formattedAmount'] = CRM_Utils_Money::format($itemValues['amount']);

    if(!empty($itemValues['options'])) {
      foreach($itemValues['options'] as &$option) {
        $option['formattedAmount'] = CRM_Utils_Money::format($option['amount']);
      }
    }

    //Assign data to the template
    foreach($itemValues as $key => $value) {
      $template->assign($key, $value);
    }

    //Assign the button text if it is needed
    if($context == "full" || $context == "button") {
      if(!$civicartAddButtonText) {
        $civicartAddButtonText = civicrm_api3('Setting', 'getvalue', array(
          'return' => "civicart_add_button_text",
          'name' => "civicart_add_button_text",
        ));
      }
      $template->assign("buttonText", $civicartAddButtonText);
    }

    //Render the Template
    $html = trim($template->fetch($templateFile));


    if($html) {
      //Add the Library Resources if we have a fleshed out Token.
      CRM_Civicart_Utils::addLibraryResources();
    }

    return $html;
  }

}