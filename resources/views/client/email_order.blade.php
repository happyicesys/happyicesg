<!DOCTYPE html>
<html>
    <body>

        <p><span style="font-weight:bold">Name:</span>&nbsp;{{ $name }}</p>
        <p><span style="font-weight:bold">Contact:</span>&nbsp;{{ $contact }}</p>
        <p><span style="font-weight:bold">Email:</span>&nbsp;{{ $email }}</p>
        <p><span style="font-weight:bold">PostCode:</span>&nbsp;{{ $postcode }}</p>
        <p><span style="font-weight:bold">Block:</span>&nbsp;{{ $block }}</p>
        <p><span style="font-weight:bold">Floor:</span>&nbsp;{{ $floor }}</p>
        <p><span style="font-weight:bold">Unit:</span>&nbsp;{{ $unit }}</p>
        <p></p>
        <p>Item Ordered</p>
        @foreach($amountArr as $index => $amount)
            @if($amount != null or $amount != 0 or $amount != '')
            <p>{{$itemArr[$index]}} - {{$qtyArr[$index]}} - ${{$amount}}</p>
            @endif
        @endforeach
        <p></p>
        <p><span style="font-weight:bold">Total:</span>&nbsp;{{ $total }}</p>
    </body>
</html>