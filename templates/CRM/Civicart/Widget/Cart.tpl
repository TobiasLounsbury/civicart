<tr class="civicart-item">
    <td>
        {if $image}
            <img src="{$image}" class="civicart-thumbnail" />
        {else}
            <div class="civicart-no-thumbnail"></div>
        {/if}
        <strong>{$label}</strong>
    </td>
    <td>
        {$amount}
    </td>
    <td>
        {if $isQty}
            <input name="{$id}_qty" value="{$quantity}" size="2" />
        {else}
            {$quantity}
        {/if}
    </td>
    <td>
        {$lineTotal}
    </td>
    <td>
        <button type="button" class="civicart-remove button" onclick="CiviCart.removeItemFromCart('{$id}_{$option}')">{ts}Remove{/ts}</button>
    </td>

</tr>