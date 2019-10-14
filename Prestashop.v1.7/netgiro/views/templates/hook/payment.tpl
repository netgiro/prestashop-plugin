<form name="PaymentOptionForm" action="{$link->getModuleLink('netgiro', 'payment', [], true)}" method="POST">
{if $netgiro_method1}
<p class="payment_module">
  <a class="netiro-payment-type" style="cursor: pointer" title="{l s='Pay with Netgiro' mod='netgiro'}">
    <img id="netgiro-branding-p1-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="1"/>
    <span id="netgiro-branding-p1-title" class="netgiro_title">Netgíró reikningur Allt að 14 daga vaxtalaus greiðslufrestur eða raðgreiðslur.</span></br>
  <span id="netgiro-branding-p1-text" class="netgiro_desc"></span>
  </a>
</p>
{/if}
{if $netgiro_method2}
<p class="payment_module">
  <a class="netiro-payment-type" style="cursor: pointer" title="{l s='Pay with Netgiro' mod='netgiro'}">
    <img id="netgiro-branding-p2-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="2"/>
    <span id="netgiro-branding-p2-title" class="netgiro_title">Netgíró raðgreiðslur Greiðsla fer fram á öruggu vefsvæði Netgíró.</span></br>
  <span id="netgiro-branding-p2-text" class="netgiro_desc"> </span>
  </a>
</p>
{/if}
{if $netgiro_method3}
<p class="payment_module">
  <a class="netiro-payment-type" style="cursor: pointer" title="{l s='Pay with Netgiro' mod='netgiro'}">
    <img id="netgiro-branding-p3-image" src="{$this_path}logo.gif" alt="{l s='Pay with Netgiro' mod='netgiro'}"/>
    <input type="hidden" id="netgiro_payment_type" name="netgiro_payment_type" value="3"/>
    <span id="netgiro-branding-p3-title" class="netgiro_title">Netgíró vaxtalausar raðgreiðslur Greiðsla fer fram á öruggu vefsvæði Netgíró.</span></br>
  <span id="netgiro-branding-p3-text" class="netgiro_desc"> </span>
  </a>
</p>
{/if}
</form>
{block name='javascript_bottom'}
  {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{literal}
  <script>
    jQuery(document).ready(function () {
      jQuery('.netiro-payment-type').unbind('click').on('click', function(e) {
          e.preventDefault();
          document.PaymentOptionForm.submit();
      });
    });
  </script>
{/literal}
{/block}