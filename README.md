# CiviCart

This extension creates a CiviCRM session based shopping cart and provides hooks and related functions for creating "Add to Cart" buttons for items in CMS content.

This Extension is simplest when used with one of the CMS specific connectors. It can be used without either of these connectors but they are the simplest way to make this functionality work "out of the box"

##Related Extensions
[CiviCart-Drupal](https://github.com/TobiasLounsbury/civicart-drupal):
This module connects to CiviCart and creates drupal tokens for all cart items.

[CiviCart-Wordpress](https://github.com/TobiasLounsbury/civicart-wordpress):
This Plugin creates a civicart shortcode that can be used in Wordpress content to create descriptions, and buttons for cart contents 

[CiviCRM-PriceSet-Inventory](https://github.com/TobiasLounsbury/civicrm-priceset-inventory):
This extension is creates the ability to add Description and Quantity restrictions for Price-Sets items. It works as a stand-alone extension but also contains hooks to interface with CiviCart.


##Hooks

####alterTemplateFile

```php
function hook_civicart_alterTemplateFile(&$item, $context, &$template);
```
Allows an external extension to modify which template is rendered for a given item action combination.
`$item` is a reference to the priceset field or field value that is being rendered
`$context` is the context that is being rendered [full, description, button]

####tokenListAlter

```php
function hook_civicart_tokenListAlter(&$items);
```
Allows an external extension to modify which PriceSet items are used for displaying pre-defined tokens and shortcodes.
`$items` is a list of price fields with their associated options

#### getItemInventory

```php
function hook_civicart_getItemInventory(&$item, $context, &$inventory);
```
Fetches Inventory information for a given item. `$item` contains 3 keys: 
* `quantity` - The quantity of this item that is available. (`false` means unlimited quantity, used for things printed on demand or digital products) 
* `description` - A textual description of this product.
* `image` - either the uri or base64encoded data stream for the image that will be used as the thumbnail for this item in the cart.

