<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
</div>

{foreach from=$groups key=groupTitle item=fields}
    <fieldset>
        <legend>{$groupTitle}</legend>
        {foreach from=$fields item=elementName}

            <div class="crm-section">
                <div class="label">
                    {$form.$elementName.label}
                    <a onclick="CRM.help(CRM.Civicart.Help.{$elementName}.title, CRM.Civicart.Help.{$elementName}.message); return false;" href="#" title="{$metadata.$elementName.description} Help Information" class="helpicon">&nbsp;</a>
                </div>
                <div class="content">{$form.$elementName.html}</div>
                <div class="clear"></div>
            </div>
        {/foreach}
    </fieldset>
{/foreach}


<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
