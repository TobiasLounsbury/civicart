var CiviCart = CiviCart || {};


CiviCart.removeItemFromCart = function removeItemFromCart(id) {
  CRM.$("input[name='remove']").val(id);
  CRM.$("input[name='action']").val("remove");
  CRM.$("#Cart").submit();
};