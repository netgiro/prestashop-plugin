{literal}
<style>
.netgiro_desc { position: relative; left: 116px; }
</style>
{/literal}
<form name="PaymentOptionForm" action="{$link->getModuleLink('netgiro', 'payment', [], true)}" method="POST">
{if $netgiro_method1}
<p class="payment_module">
  <a title="{l s='Pay with Netgiro' mod='netgiro'}" onclick="document.PaymentOptionForm.submit();return false;">
    <img id="netgiro-branding-p1-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}" width="100" height="33"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="1"/>
    <span id="netgiro-branding-p1-title" class="netgiro_title"> </span></br>
  <span id="netgiro-branding-p1-text" class="netgiro_desc"> </span>
  </a>
</p>
{/if}
{if $netgiro_method2}
<p class="payment_module">
  <a title="{l s='Pay with Netgiro' mod='netgiro'}" onclick="document.PaymentOptionForm.submit();return false;">
    <img id="netgiro-branding-p2-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}" width="100" height="33"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="2"/>
    <span id="netgiro-branding-p2-title" class="netgiro_title"> </span></br>
  <span id="netgiro-branding-p2-text" class="netgiro_desc"> </span>
  </a>
</p>
{/if}
{if $netgiro_method3}
<p class="payment_module">
  <a title="{l s='Pay with Netgiro' mod='netgiro'}" onclick="document.PaymentOptionForm.submit();return false;">
    <img id="netgiro-branding-p3-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}" width="100" height="33"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="3"/>
    <span id="netgiro-branding-p3-title" class="netgiro_title"> </span></br>
  <span id="netgiro-branding-p3-text" class="netgiro_desc"> </span>
  </a>
</p>
{/if}
</form>
{literal}
        <script src="//api.netgiro.is/scripts/netgiro.api.js" type="text/javascript"></script>
        <script>
            jQuery(document).ready(function () {
                  netgiro.branding.options = { 
                    {/literal}{$netgiro_method1}
                    {$netgiro_method2}
                    {$netgiro_method3}{literal} 
                  } 
                  netgiro.branding.init("{/literal}{$netgiro_application_id}{literal}");
            });
        </script>

        <script>
            jQuery(function() {
                jQuery('.method_reikningur').click(function() {
                  jQuery(this).find('input').prop('checked', true);
                });
            });
        </script>
{/literal}