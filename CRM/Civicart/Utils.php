<?php


class CRM_Civicart_Utils {



  /*****  [ Cart Functions ]  *****/

  /**
   * Helper function to get the cart contents
   *
   * @return array
   */
  public static function getCartContents() {

    $contents = CRM_Core_Session::singleton()->get("cartContents","CiviCart");

    if(!$contents) {
      $contents = array();
      //I'm not sure if we should do this.
      //If there is some sort of error, we could accidentally empty
      //The user's cart.
      CRM_Core_Session::singleton()->set("cartContents", $contents, "CiviCart");
    }

    return $contents;
  }


  /**
   * Helper function to Empty the Cart
   */
  public static function emptyCart() {
    CRM_Core_Session::singleton()->set("cartContents", array(), "CiviCart");
  }


  /**
   * Helper function to Add an item to the Cart.
   *
   * @param $id
   * @param bool $option
   * @param int $qty
   * @throws Exception
   */
  public static function addToCart($id, $option = false, $qty = 1) {
    $contents = self::getCartContents();
    $key = (is_numeric($option)) ? "{$id}_{$option}" : $id;

    //Fetch the Item Details
    $item = CRM_Civicart_Items::getItemData($id, $option);


    if(array_key_exists($key, $contents)) {
      if($item['html_type'] == "Text") {
        self::updateItemQty($id, "+{$qty}", $option);
        return;
      } else {
        throw new Exception("Already in Cart");
      }
    }


    //Validate quantity
    if(is_numeric($item['quantity']) && $qty > $item['quantity']) {
      throw new Exception("Error: Requested quantity exceeds stock available.");
    }

    //Add the Item to the Cart Contents
    $contents[$key] = array(
      "id" => $id,
      "option" => $option,
      "quantity" => $qty,
    );

    //Update the Stored Contents of the Cart
    CRM_Core_Session::singleton()->set("cartContents", $contents, "CiviCart");
  }


  /**
   * Remove a single item from the cart.
   *
   * @param $id
   * @param bool $option
   */
  public static function removeFromCart($id, $option = false) {
    $key = (is_numeric($option)) ? "{$id}_{$option}" : $id;
    $contents = self::getCartContents();
    unset($contents[$key]);
    CRM_Core_Session::singleton()->set("cartContents", $contents, "CiviCart");
  }


  /**
   * Helper function to update the quantity of an item in the cart.
   *
   * @param $id
   * @param $qty
   * @param bool $option
   * @throws Exception
   */
  public static function updateItemQty($id, $qty, $option = false) {

    if($qty == 0) {
      self::removeFromCart($id, $option);
      return;
    }


    $contents = self::getCartContents();
    $key = (is_numeric($option)) ? "{$id}_{$option}" : $id;
    $item = CRM_Utils_Array::value($key, $contents, false);
    $itemDetails = CRM_Civicart_Items::getItemData($id, $option);

    //Verify that this item allows more than one
    //quantity, eg. that it is a Text type input
    if($itemDetails['html_type'] != "Text") {
      //todo: Should we throw an error here?
      return;
    }

    switch($qty[0]) {
      case "+":
        $qty = str_replacE("+", "", $qty);
        if(!$item) {
          self::addToCart($id, $option, $qty);
        } else {
          $item['quantity'] = $item['quantity'] + $qty;
        }
        break;
      case "-":
        if($item) {
          $qty = str_replacE("-", "", $qty);
          $item['quantity'] = $item['quantity'] - $qty;
          if ($item['quantity'] <= 0) {
            self::removeFromCart($id, $option);
            return;
          }
        }
        break;
      default:
        if(!is_numeric($qty)) {
          throw new Exception("Unknown Quantity");
        }

        if($item) {
          $item['quantity'] = $qty;
        } else {
          self::addToCart($id, $option, $qty);
        }
    }

    if($item) {
      //Validate the quantity with inventory on Hand
      if(is_numeric($itemDetails['quantity']) && $item['quantity'] > $itemDetails['quantity']) {
        throw new Exception("Unable to update Quantity: Requested quantity exceeds stock available.");
      }

      $contents[$key] = $item;
      CRM_Core_Session::singleton()->set("cartContents", $contents, "CiviCart");
    }
  }



  /**
   *  Function to return the count of items in the cart.
   */
  public static function getCartCount() {
    $contents = CRM_Core_Session::singleton()->get("cartContents","CiviCart");

    if($contents && is_array($contents)) {
      $items = 0;
      foreach($contents as $item) {
        $new = (array_key_exists("quantity", $item) && is_numeric($item['quantity'])) ? $item['quantity'] : 1;
        $items = $items + $new;
      }
      return $items;
    }
    return 0;
  }


  /**
   * Helper function to calculate the total cost of items
   * in the user's cart.
   *
   * @return Double
   */
  public static function getCartTotal($moneyFormat = false) {
    $contents = CRM_Core_Session::singleton()->get("cartContents","CiviCart");

    //Fetch the Item Details
    foreach($contents as &$item) {
      $option = CRM_Utils_Array::value("option", $item, false);
      $tmp = CRM_Civicart_Items::getItemData($item['id'], $option);
      $item = array_merge($tmp, $item);
    }

    $total = 0;

    if($contents && is_array($contents)) {
      foreach($contents as $line) {
        $total = $total + ($line['quantity'] * $line['amount']);
      }

    }
    if($moneyFormat) {
      return CRM_Utils_Money::format($total);
    } else {
      return $total;
    }
  }


  /**
   * Returns the URL to view the cart.
   */
  public static function getCartLink() {
    return CRM_Utils_System::url("civicrm/cart");
  }



  /*****  [ Resource Functions ]  *****/


  /**
   * Hook to include Library Resources into a civi page.
   */
  public static function addLibraryResources() {
    global $CiviCartLibraryIncluded;

    if(!$CiviCartLibraryIncluded) {

      $manager = CRM_Core_Resources::singleton();
      $resources = self::getLibraryResources();

      //Add JavaScript Files
      foreach($resources['js'] as $item) {
        $manager->addScriptUrl($item);
      }

      //Add Stylesheets
      foreach($resources['css'] as $item) {
        $manager->addStyleUrl($item);
      }

      //Add Inline JavaScript
      foreach($resources['inline'] as $item) {
        $manager->addScript($item);
      }

      $CiviCartLibraryIncluded = true;
    }
  }



  /**
   * Function to return a list of resource urls so they
   * may be added by a helper function
   */
  public static function getLibraryResources() {

    $resourceManager = CRM_Core_Resources::singleton();
    $resources = array("js" => array(), "css" => array(), "inline" => array());

    $resources['js'][] = $resourceManager->getUrl("com.tobiaslounsbury.civicart", "js/civicart.library.js");
    $resources['css'][] = $resourceManager->getUrl("com.tobiaslounsbury.civicart", "css/civicart.css");

    $url = CRM_Utils_System::url("civicrm/cart");
    $resources['inline'][] = "var CiviCart = CiviCart || {}; CiviCart.addUrl = '{$url}';";

    return $resources;
  }


  /**
   * Helper function to fetch the price-set-id for the
   * contribution page selected for checkout.
   *
   * @return false|int
   */
  public static function getCartPriceSet() {
    //Lookup the setting for which price set we are to use
    $pageId = civicrm_api3('Setting', 'getvalue', array(
      'return' => "civicart_contribution_page",
      'name' => "civicart_contribution_page",
    ));

    return  CRM_Price_BAO_PriceSet::getFor('civicrm_contribution_page', $pageId);
  }

}