<?php

class CRM_Civicart_Hooks {

  static $_nullObject = NULL;

  /**
   * This hook allows other extensions to modify the
   * template that gets rendered for a given token.
   *
   * @param $item
   * @param $context
   * @param $template
   * @return mixed
   */
  public static function tokenTemplateHook(&$item, $context, &$template) {
    return CRM_Utils_Hook::singleton()->invoke(3, $item, $context, $template,
      self::$_nullObject, self::$_nullObject, self::$_nullObject,
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
  public static function tokenListHook(&$priceFields) {
    return CRM_Utils_Hook::singleton()->invoke(1, $priceFields, self::$_nullObject,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicart_tokenListAlter'
    );
  }


  /**
   * This hook allows other extensions (such as PriceSetInventory)
   * to indicate how many of this item are available.
   *
   * @param $item
   * @param $type
   * @return mixed
   */
  public static function inventoryHook(&$item, $type) {

    $item['quantity'] = CRM_Utils_Array::value("quantity", $item, false);
    $item['description'] = CRM_Utils_Array::value("description", $item, false);
    $item['image'] = CRM_Utils_Array::value("image", $item, false);

    return CRM_Utils_Hook::singleton()->invoke(2, $item, $type,
      self::$_nullObject, self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'civicart_getItemInventory'
    );
  }

}