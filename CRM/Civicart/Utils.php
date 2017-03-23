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



}