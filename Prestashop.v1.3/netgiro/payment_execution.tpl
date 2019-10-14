{capture name=path}{l s='Netgíró' mod='netgiro'}{/capture}
{include file=$tpl_dir./breadcrumb.tpl}

<h2>{l s='Order summary' mod='netgiro'}</h2>

{assign var='current_step' value='payment'}
{include file=$tpl_dir./order-steps.tpl}

{if $nbProducts <= 0}
	<p class="warning">{l s='Karfan þín er tóm.'}</p>
{else}

<h3>{l s='Netgíró' mod='netgiro'}</h3>
<form action="{$this_path_ssl}iframe.php?PaymentOption={$PaymentOption}" method="post">
<p>
	<img src="{$this_path}logo.gif" alt="{l s='bank wire' mod='netgiro'}" width="100" height="33" style="float:left; margin: 0px 10px 5px 0px;" />
	{l s='Þú hefur valið að greiða með Netgíró.' mod='netgiro'}
</p>
<p style="margin-top:20px;">
	{l s='Heildar upphæð til greiðslu er ' mod='netgiro'}
	<span id="amount" class="price">{displayPrice price=$total}</span>
	{if $use_taxes == 1}
    	{l s='(m/VSK)' mod='netgiro'}
    {/if}
</p>
<p>
	{l s='Netgíró satðfesting verður sýnd á næstu síðu.' mod='netgiro'}
</p>
	{if !$iframe}
	<p class="cart_navigation" id="cart_navigation">    
	    <input type="submit" value="{l s='Greiða með NetGíró' mod='netgiro'}" class="exclusive_large" />
	</p>
	</form>
	{else}
	<iframe id="NetgiroFrame" name="NetgiroFrame" src="{$this_path_ssl}iframe.php?PaymentOption={$PaymentOption}" frameBorder="0" style="display: block; margin: 0 auto 0px auto; width: 940px; height: 1024px;" scrolling="no" ></iframe>
	{/if}
{/if}

