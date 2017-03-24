if (!window.civicart) {
  window.civicart = {};
}

if(window.CRM && CRM.$) {
  civicart.$ = CRM.$;
} else {
  civicart.$ = jQuery;
}

(function($) {

  /**
   * Add an item to the cart.
   *
   * @param type
   * @param item
   * @param qtySelector
   */
  civicart.addToCart = function addToCart(type, item, qtySelector) {
    console.log("Add to Cart");
    var qty = $(qtySelector).val() || 1;
    console.log("todo: post to civicrm/cart");
  };


  /**
   * Update the cart item counter.
   * This assumes that the sounter is wrapped in an element
   * with a specific class .civicart-cart-item-count
   *
   * @param itemCount
   */
  civicart.updateCartNumber = function updateCartNumber(itemCount) {
    $(".civicart-cart-item-count").text(itemCount);
  };

})(civicart.$);


