<html>
<head>
</head>
<body onload="document.paymentform.submit();">

<div id="webpayment">
    <form name="paymentform" action="{$params.gateway_url}" method="POST" target="_self">
        Vinsamlegast bíðið á meðan greiðslugátt NetGíro opnast
        <input name="ApplicationID" type="hidden" value="{$params.ApplicationID}">
        <input name="Iframe" type="hidden" value="{$params.Iframe}">
        <!--<input name="Iframe" type="hidden" value="false">-->
        <input name="PaymentSuccessfulURL" type="hidden" value="{$params.PaymentSuccessfulURL}">
        <input name="PaymentCancelledURL" type="hidden" value="{$params.PaymentCancelledURL}">
        <input name="MaxNumberOfInstallments" type="hidden" value="{$params.MaxNumberOfInstallments}">
        <input name="OrderId" type="hidden" value="{$params.OrderId}">
        <input name="Signature" type="hidden" value="{$params.Signature}">
        <input name="TotalAmount" type="hidden" value="{$params.TotalAmount}">
        <input name="ShippingAmount" type="hidden" value="{$params.ShippingAmount}">
        <input name="PaymentOption" type="hidden" value="{$params.PaymentOption}" />
        {foreach from=$params.Items key=k item=i}
            <input name="Items[{$k}].ProductNo" type="hidden" value="{$i.ProductNo}">
            <input name="Items[{$k}].Name" type="hidden" value="{$i.Name}">
            <input name="Items[{$k}].Description" type="hidden" value="{$i.Description}">
            <input name="Items[{$k}].UnitPrice" type="hidden" value="{$i.UnitPrice}">
            <input name="Items[{$k}].Amount" type="hidden" value="{$i.Amount}">
            <input name="Items[{$k}].Quantity" type="hidden" value="{$i.Quantity}">
        {/foreach}
    </form>
</div>

</body>
</html>