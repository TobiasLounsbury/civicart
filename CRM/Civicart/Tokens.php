<?php


class CRM_Civicart_Tokens {

  /**
   * Looks up all the items we have available to add to the cart
   * And create a list of tokens
   *
   * @return array
   */
  public static function getCartTokens() {
    $tokens = array();

    //todo: Lookup the setting for which contribution page or price set we are to use
    //todo: Get the list of items available to the cart
    //todo: Craft tokens for each Priceset item
    // : One for The Description
    // : One for An Add Button
    // : One for a whole html content "block" describing a cart item.

    return $tokens;
  }


  public static function replaceItemToken($token) {
    //todo: Parse which type of token we are replacing
    //todo: Invoke a hook to see if we should do a regular render
    //todo: Render the token
  }

}