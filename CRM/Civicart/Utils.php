<?php


class CRM_Civicart_Utils {


  public static function addLibraryResources() {
    global $CiviCartLibraryIncluded;

    if(!$CiviCartLibraryIncluded) {

      $resources = CRM_Core_Resources::singleton();
      $resources->addScriptFile("com.tobiaslounsbury.civicart", "js/civicart.library.js");
      $resources->addStyleFile("com.tobiaslounsbury.civicart", "css/civicart.css");

      $CiviCartLibraryIncluded = true;
    }
  }

}