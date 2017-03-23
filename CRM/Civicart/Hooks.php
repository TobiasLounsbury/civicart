<?php

class CRM_Civicart_Hooks {

  static $_nullObject = NULL;

  /**
   * This hook allows other extensions to modify the
   * template that gets rendered for a given token.
   *
   * @param $priceFieldId
   * @param $optionId
   * @param $context
   * @param $template
   * @return mixed
   */
  public static function TokenTemplateHook($priceFieldId, $optionId, $context, &$template) {
    return CRM_Utils_Hook::singleton()->invoke(4, $priceFieldId, $optionId, $context, $template,
      self::$_nullObject, self::$_nullObject,
      'civicart_alterTemplateFile'
    );
  }


  /**
   * Function to allow other modules to alter the list of
   * items the cart has access to.
   *
   * @param $priceFields
   * @return mixed
   */
  public static function TokenListHook(&$priceFields) {
    return CRM_Utils_Hook::singleton()->invoke(1, $priceFields, self::$_nullObject,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicart_tokenListAlter'
    );
  }


  /**
   * This hook allows other extensions (such as PriceSetInventory)
   * to indicate how many of this item are available.
   *
   * @param $priceFieldId
   * @param $option
   * @return mixed
   */
  public static function InventoryHook($priceFieldId, $option = false) {
    $inventory = array();
    return CRM_Utils_Hook::singleton()->invoke(3, $priceFieldId, $option, $inventory,
      self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicart_getItemInventory'
    );
  }

}