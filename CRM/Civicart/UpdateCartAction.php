<?php

class CRM_Civicart_UpdateCartAction extends HTML_QuickForm_Action {

  function perform(&$page, $actionName) {

    $page->isFormBuilt() or $page->buildForm();

    $actionHandler = $actionName. "Action";
    if(method_exists($page, $actionHandler)) {
      $page->preProcess();
      if($page->validate()) {
        $page->$actionHandler();
      }
      return $page->handle('display');
    } else {
      throw new Exception("Unhandled Action Update Cart");
    }
  }


}