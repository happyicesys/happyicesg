<!DOCTYPE html>
<html>
    <body>
      <p>
        Transactions for the incoming week ({{$date_start}} until {{$date_end}}) <br><br>
        <div style="font-family: 'Open Sans'; font-size: 11px;">
            <table style="border: 1px solid black; border-collapse:collapse;">
                <tr style="background-color: #D8BFD8;">
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        #
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        INV #
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        ID
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        ID Name
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Cust Cat
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Del Postcode
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Status
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Delivery Date
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Delivered By
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Total Amount
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Total Qty
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Payment
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Last Modified By
                    </th>
                    <th style="border: 1px solid black; padding:5px 15px 5px 15px;">
                        Last Modified Time
                    </th>
                </tr>

                @foreach($transactions as $index => $transaction)
                    <tr>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$index + 1}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->id}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->cust_id}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->cust_id[0] == 'D' || $transaction->cust_id[0] == 'H' ? $transaction->name : $transaction->company }}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->custcategory}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->del_postcode}}
                        </td>

                        @php
                            $status = $transaction->status;
                            $color = '';
                            $backgroundcolor = '';
                            switch($status) {
                                case 'Pending':
                                    $color = 'red';
                                    $backgroundcolor = '';
                                    break;
                                case 'Confirmed':
                                    $color = '#FFA500';
                                    $backgroundcolor = '';
                                    break;
                                case 'Delivered':
                                    $color = 'green';
                                    $backgroundcolor = '';
                                    break;
                                case 'Verified Owe':
                                    $color = 'black';
                                    $backgroundcolor = '#FFA500';
                                    break;
                                case 'Verified Paid':
                                    $color = 'black';
                                    $backgroundcolor = 'green';
                                    break;
                                case 'Cancelled':
                                    $color = 'white';
                                    $backgroundcolor = 'red';
                                    break;
                            }
                        @endphp
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            <span style="color: {{$color}}; background-color: {{$backgroundcolor}};">
                                {{$status}}
                            </span>
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{\Carbon\Carbon::parse($transaction->delivery_date)->format('Y-m-d')}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->driver}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="right">
                            {{$transaction->total}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="right">
                            {{$transaction->total_qty}}
                        </td>
                        @php
                            $paycolor = '';
                            $pay_status = $transaction->pay_status;

                            switch($pay_status) {
                                case 'Owe':
                                    $paycolor = 'red';
                                    break;
                                case 'Paid':
                                    $paycolor = 'green';
                                    break;
                            }
                        @endphp
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            <span style="color: {{$paycolor}};">
                                {{$pay_status}}
                            </span>
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->updated_by}}
                        </td>
                        <td style="border: 1px solid black; padding:5px 15px 5px 15px;" align="center">
                            {{$transaction->updated_at}}
                        </td>
                    </tr>
                @endforeach
                    @if(count($transactions) == 0)
                    <tr>
                        <td colspan="18" class="text-center">No Records Found</td>
                    </tr>
                    @endif
            </table>
        </div>
    </body>
</html>