var CiviCart = CiviCart || {};

if(window.CRM && CRM.$) {
  CiviCart.$ = CRM.$;
} else {
  CiviCart.$ = jQuery;
}

if(window.CRM && CRM.ts) {
  CiviCart.ts = CRM.ts;
} else {
  CiviCart.ts = function(text) {return text;};
}

(function($) {

  /**
   * Add an item to the cart.
   *
   * @param type
   * @param id
   * @param selector
   */
  CiviCart.addToCart = function addToCart(type, id, selector) {

    var params = {action: "add", "id": id, quantity: 1, "type": type};

    switch(type) {

      case "Text":
        params.quantity = $(selector).val();
        if(!params.quantity || parseInt(params.quantity) < 1 || isNaN(parseInt(params.quantity))) {
          CiviCart.message( CiviCart.ts("Invalid Quantity Selected"), CiviCart.ts("Error"), "error" );
          return;
        }

        var data = $(selector).data();

        //Check if sold out
        if(parseInt(data.quantity) === 0) {
          CiviCart.message( CiviCart.ts("This item is sold out"), CiviCart.ts("Error"), "error" );
          return;
        }

        //Check sale limit
        if(parseInt(data.limit) < parseInt(params.quantity)) {
          CiviCart.message( CiviCart.ts("This item is limited to a quantity of ") + data.limit, CiviCart.ts("Error"), "error" );
          return;
        }

        //Check Quantity on hand
        if(parseInt(data.quantity) < parseInt(params.quantity)) {
          CiviCart.message( CiviCart.ts("I'm sorry, we only have " + data.quantity + " of this item available"), CiviCart.ts("Error"), "error" );
          return;
        }

        break;

      case "Select":
        if(!$(selector).val()) {
          CiviCart.message( CiviCart.ts("No Option Selected"), CiviCart.ts("Invalid Selection"), "error" );
          return;
        }
        params.option = $(selector).val();
        break;

      case "Option":
        params.option = selector;
        break;

      case "Radio":
        if($("input[name='" + selector + "']:checked").length == 0) {
          CiviCart.message( CiviCart.ts("You must select an option before adding to cart."), CiviCart.ts("Error"), "error" );
          return;
        }
        params.option = $("input[name='" + selector + "']:checked").val();
        break;

      case "CheckBox":
        params.option = [];
        $("input[name='" + selector + "']:checked").each(function(index, object) {
          params.option.push($(object).val());
        });
        if(params.option.length == 0) {
          CiviCart.message( CiviCart.ts("No Options Selected"), CiviCart.ts("Error"), "error" );
          return;
        }
        break;
    }

    CiviCart.postAddData(params);
  };


  // Helper flag to mark when we've added the popup template.
  CiviCart.popupAdded = false;


  /**
   * Shows a popup message
   *
   * @param msg
   * @param title
   * @param type
   * @param options
   */
  CiviCart.message = function(msg, title, type, options) {

    if(window.CRM && CRM.alert) {
      CRM.alert(msg, title, type, options);
    } else {

      options = options || {};
      type = type || "success";

      if (!CiviCart.popupAdded) {
        CiviCart.addPopupTemplate();
      }

      //create the popup object
      var obj = $("<div class='civicart-popup civicart-popup-" + type + "'></div>");
      if (type) {
        msg = "<strong>" + title + ": </strong>" + msg;
      }

      //Set the message
      obj.html(msg);

      //Show the popup
      $("#civicart-popup-wrapper").append(obj);

      //set a timeout to hide the message
      CiviCart.temporaryMessage(obj);

      //Close the message when it is clicked.
      obj.click(function() {
        //Do some animation to hide the error message first
        obj.slideUp( "fast", function() {
          // Animation complete, now remove from the DOM
          obj.remove();
        });
      });
    }
  };


  /**
   * Set a timeout to destroy the message after
   * a fixed amount of time.
   *
   * @param obj
   */
  CiviCart.temporaryMessage = function(obj) {
    setTimeout(function() {
      //Do some animation to hide the error message first
      obj.slideUp( "fast", function() {
        // Animation complete, now remove from the DOM
        obj.remove();
      });
    }, 10000);
  };


  /**
   * Function adds a wrapper to the body for showing popup messages
   */
  CiviCart.addPopupTemplate = function() {
    $("body").append("<div id='civicart-popup-wrapper' class='civicart-popup-wrapper'></div>");
    CiviCart.popupAdded = true;
  };


  /**
   * Update the cart item counter.
   * This assumes that the sounter is wrapped in an element
   * with a specific class .civicart-cart-item-count
   *
   * @param itemCount
   */
  CiviCart.updateCartNumber = function updateCartNumber(itemCount) {
    $(".civicart-cart-item-count").text(itemCount);
  };

})(CiviCart.$);


