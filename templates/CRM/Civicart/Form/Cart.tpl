<fieldset class="civicart-cart-contents">
    <legend>{ts}Cart Contents{/ts}</legend>
{if $items}

    {$form.remove.html}
    {$form.action.html}

    <table class="table">
        <thead>
            <tr>
                <th style="width: 55%;">{ts}Item{/ts}</th>
                <th style="width: 10%;">{ts}Price{/ts}</th>
                <th style="width: 10%;">{ts}Quantity{/ts}</th>
                <th style="width: 10%;">{ts}Item Total{/ts}</th>
                <th style="width: 15%;">{ts}Action{/ts}</th>
            </tr>
        </thead>

        <tbody>
            {foreach from=$items item=cartItem}
                {$cartItem.html}
            {/foreach}
            <tr><td colspan="5"><strong>{ts}Total{/ts}: </strong>{$cartTotal}</td></tr>
        </tbody>
    </table>

    <div class="crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
{else}
    <h3 class="civicart-no-items">{ts}There are no items in your cart{/ts}</h3>
{/if}
</fieldset>