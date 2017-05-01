<div class="civicart-wrapper">
    <strong class="civicart-title">{$label}</strong>
    {if $description}
        <div class="civicart-description">
            {$description}
        </div>
    {/if}
    <div class="civicart-widget-wrapper">
        {include file="CRM/Civicart/Widget/`$html_type`.tpl"}
    </div>
</div>