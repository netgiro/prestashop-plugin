{if $status == 'ok'}
	<p>{l s='Pöntun þín hjá' mod='netgiro'} <span class="bold">{$shop_name}</span> {l s='hefur verið móttekin.' mod='netgiro'}
		<br /><br />
		{l s='Reikningur sedur í netbanka ' mod='netgiro'}
		<br /><br />- {l s='Upphæð' mod='netgiro'} <span class="price">{$total_to_pay}</span>
		<br /><br />- {l s='Pöntun nr.: ' mod='netgiro'} <span class="bold">{$id_order}</span>
		<br /><br />- {l s='Netgíró staðfestingar kóði: ' mod='netgiro'} <span class="bold">{$netgiro_invoiceNumber}</span>
		<br /><br />- {l s='Netgíró kvittun nr.: ' mod='netgiro'} <span class="bold">{$netgiro_confirmationcode}</span>
    </p>
{else}
    <p class="warning">
        {l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='netgiro'}
        <a href="{$base_dir_ssl}contact-form.php">">{l s='customer support' mod='netgiro'}</a>.
    </p>
{/if}