<?php


class CRM_Civicart_Utils {

  /**
   * Hook to include Library Resources into a civi page.
   */
  public static function addLibraryResources() {
    global $CiviCartLibraryIncluded;

    if(!$CiviCartLibraryIncluded) {

      $resources = CRM_Core_Resources::singleton();
      $resources->addScriptFile("com.tobiaslounsbury.civicart", "js/civicart.library.js");
      $resources->addStyleFile("com.tobiaslounsbury.civicart", "css/civicart.css");

      $CiviCartLibraryIncluded = true;
    }
  }



  /**
   * Function to return a list of resource urls so they
   * may be added by a helper function
   */
  public static function getLibraryResources() {

    $resourceManager = CRM_Core_Resources::singleton();
    $resources = array("js" => array(), "css" => array());

    $resources['js'][] = $resourceManager->getUrl("com.tobiaslounsbury.civicart", "js/civicart.library.js");
    $resources['css'][] = $resourceManager->getUrl("com.tobiaslounsbury.civicart", "css/civicart.css");

    return $resources;
  }


  /**
   * Helper function to get the cart contents
   *
   * @return array
   */
  public static function getCartContents() {
    $contents = array();



    return $contents;
  }


  /**
   *  Function to return the count of items in the cart.
   */
  public static function getCartCount() {
    if(array_key_exists("civicart_items", $_SESSION)) {
      $items = 0;
      foreach($_SESSION['civicart_items'] as $item) {
        $items = $items + (array_key_exists("quantity", $item) && is_numeric($item['quantity'])) ? $item['quantity'] : 1;
      }
      return $items;
    }
    return 0;
  }



  /**
   * Returns the URL to view the cart.
   */
  public static function getCartLink() {
    return CRM_Utils_System::url("civicrm/cart");
  }


}