{foreach from=$options key=groupTitle item=item}
    <input type="checkbox" name="civicart_{$name}" id="civicart_{$name}_{$item.id}" data-quantity="{$item.quantity}" value="{$item.id}" {if $item.quantity === '0'}disabled="disabled"{/if} /> - <label for="civicart_{$name}_{$item.id}">{$item.label}{if $item.formattedAmount} - {$item.formattedAmount}{/if}{if $item.quantity === '0'} - {ts}Sold Out{/ts}{/if}</label>
{/foreach}
<a href="javascript:CiviCart.addToCart('CheckBox', {$id}, 'civicart_{$name}')" class="civicart-add-button btn btn-primary button crm-button">{$buttonText}</a>