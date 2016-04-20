<!DOCTYPE html>
<html>
    <body>

        <p><span style="font-weight:bold">Product ID:</span>&nbsp;{{ $product_id }}</p>
        <p><span style="font-weight:bold">Name:</span>&nbsp;{{ $name }} - {{ $remark }}</p>
        <p><span style="font-weight:bold">Unit:</span>&nbsp;{{ $unit }}</p>
        <p><span style="font-weight:bold">Current Qty:</span>&nbsp;<strong>{{ $qty_now }}</strong></p>
        <p><span style="font-weight:bold">Lowest Limit:</span>&nbsp; {{ $lowest_limit }}</p>
        <p><span style="font-weight:bold">Email Alert Limit:</span>&nbsp;{{ $email_limit }}</p>

    </body>
</html>