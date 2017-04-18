<fieldset class="civicart-cart-contents">
    <legend>{ts}Cart Contents{/ts}</legend>
{if $items}
    {foreach from=$items item=cartItem}
        {$cartItem.html}
    {/foreach}

    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
{else}
    <h3 class="civicart-no-items">{ts}There are no items in your cart{/ts}</h3>
{/if}
</fieldset>