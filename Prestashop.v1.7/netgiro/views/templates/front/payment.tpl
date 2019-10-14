{extends "$layout"}
{capture name=path}{l s='Payment' mod='netgiro'}{/capture}
{block name="content"}
    <h3>{l s='Order summation' mod='netgiro'}</h3>
    <p>
        <img src="{$this_path}logo.gif" alt="{l s='bank wire' mod='netgiro'}" width="100" />
    </p>
    <p>
        {l s='Þú hefur valið að greiða með Netgíró.' mod='netgiro'}
    </p>
    <p style="margin-top:20px;">
        {l s='Heildar upphæð til greiðslu er ' mod='netgiro'}
        <span id="amount" class="price"><b>{$total}</b></span>
        {if $use_taxes == 1}
            {l s='(m/VSK)' mod='netgiro'}
        {/if}
    </p>
    <p>
        {l s='Netgíró staðfesting verður sýnd á næstu síðu.' mod='netgiro'}
    </p>
    {if !$iframe}
        <form action="{$link->getModuleLink('netgiro', 'iframe', [], true)|escape:'html'}{$iframelink}" method="post">
            <p class="cart_navigation" id="cart_navigation">
                <input type="submit" value="{l s='Pay with Netgíró' mod='netgiro'}" class="btn btn-primary exclusive_large" />
            </p>
        </form>
    {else}
        <iframe id="NetgiroFrame" name="NetgiroFrame" src="{$link->getModuleLink('netgiro', 'iframe', [], true)}{$iframelink}" frameBorder="0" style="display: block; margin: 0 auto 0px auto; width: 940px; height: 1024px;" scrolling="no" ></iframe>
    {/if}
{/block}
