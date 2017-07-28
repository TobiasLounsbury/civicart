<select name="civicart_{$name}" id="civicart_{$name}">
{foreach from=$options item=item}
    <option data-quantity="{$item.quantity}" value="{$item.id}" {if $item.quantity === '0'}disabled="disabled"{/if}>{$item.label}{if $item.formattedAmount} - {$item.formattedAmount}{/if}{if $item.quantity === '0'} - {ts}Sold Out{/ts}{/if}</option>
{/foreach}</select><a href="javascript:CiviCart.addToCart('Select', {$id}, '#civicart_{$name}')" class="civicart-add-button btn btn-primary button crm-button">{$buttonText}</a>