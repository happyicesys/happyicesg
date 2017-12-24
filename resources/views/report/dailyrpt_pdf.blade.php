<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    {{-- <link rel="stylesheet" href="../bootstrap-css/bootstrap.min.css"/>  --}}
        <style type="text/css" media="print">
            .inline {
                display:inline;
            }
            body{
                font-size: 10px;
            }
            table{
                font-size: 10px;
                font-family: 'Times New Roman';
            }
            th{
                font-size: 10px;
            }
            footer{
                position: absolute;
                height: 210px;
                bottom: 5px;
                width: 100%;
            }
            html, body{
                height: 100%;
            }
            pre{
                font-size: 11px;
                font-family: 'Times New Roman';
                background-color: transparent;
            }
            tr {
                page-break-inside: avoid;
            }
        </style>
        <script>
            function subst(){
            var vars={};
            var x=window.location.search.substring(1).split('&');
                for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
            var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
                for (var i in x) {
                    var y = document.getElementsByClassName(x[i]);
                    for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
                }
            }
        </script>
    </head>

    <body style="border:0; margin: 0;" onload="subst()">
        <div class="container">
            <div class="col-xs-12" style="font-size:15px">
                <h4 class="text-center"><strong>Daily Report</strong></h4>
            </div>

            <div class="col-xs-12" style="border: solid thin black;">
                <div class="col-xs-4">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>Invoice:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$transaction_id}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>ID:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$cust_id}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>Company:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$company}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>Status:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$status}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-4">
                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>Payment:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$pay_status}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>Date:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$delivery_date}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline"><strong>User:</strong></span>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <span class="inline">{{$driver}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xs-12" style="padding-top: 15px;">
                <div class="row">

                <div class="col-xs-6" style="border: thin black solid">
                    <div class="row">
                        <div class="col-xs-8">
                            <strong>Total Amount for 'Delivered':</strong>
                        </div>
                        <div class="col-xs-4 text-right">
                            {{ number_format($amt_del, 2, '.', ',') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-8">
                            <strong>Total Qty for 'Delivered':</strong>
                        </div>
                        <div class="col-xs-4 text-right">
                            {{ number_format($qty_del, 4, '.', ',') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-8">
                            <strong>Total Amount for 'Paid':</strong>
                        </div>
                        <div class="col-xs-4 text-right">
                            {{ number_format($amt_mod, 2, '.', ',') }}
                        </div>
                    </div>
                </div>

                @unless(Auth::user()->hasRole('driver'))
                    <div class="col-xs-6" style="border: thin black solid;">
                        <div class="row">
                            <div class="col-xs-8">
                                <strong>Total Amount for 'Paid':</strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                {{ number_format($amt_mod, 2, '.', ',') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-8">
                                <strong>Total Paid 'Cash':</strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                {{ number_format($cash_mod, 2, '.', ',') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-8">
                                <strong>Total Paid 'Cheque In':</strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                {{ number_format($chequein_mod, 2, '.', ',') }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <strong>Total Paid 'Cheque Out':</strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                {{ number_format($chequeout_mod, 2, '.', ',') }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <strong>Total Paid 'TT':</strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                {{ number_format($tt_mod, 2, '.', ',') }}
                            </div>
                        </div>
                    </div>
                @endunless

                </div>
            </div>

            <label for="count" class="col-xs-12 row" style="padding-top: 10px;">Total of {{ count($transactions) }} entries</label>
            <div class="avoid" style="padding-top: 10px;">
            <div class="row">
                <div class="col-xs-12" style="padding-top: 10px">
                    <table class="table table-bordered table-condensed" style="border:thin solid black;">
                        <tr>
                            <th class="col-xs-1 text-center">
                                #
                            </th>
                            <th class="col-xs-1 text-center">
                                INV #
                            </th>
                            <th class="col-xs-1 text-center">
                                ID
                            </th>
                            <th class="col-xs-1 text-center">
                                Company
                            </th>
                            <th class="col-xs-1 text-center">
                                Status
                            </th>
                            <th class="col-xs-1 text-center">
                                Delivery Date
                            </th>
                            <th class="col-xs-1 text-center">
                                Delivery By
                            </th>
                            <th class="col-xs-1 text-center">
                                Total Amount
                            </th>
                            <th class="col-xs-1 text-center">
                                Total Qty
                            </th>
                            <th class="col-xs-1 text-center">
                                Payment
                            </th>
                            <th class="col-xs-1 text-center">
                                Pay Received By
                            </th>
                            <th class="col-xs-1 text-center">
                                Pay Received Dt
                            </th>
                            @unless(Auth::user()->hasRole('driver'))
                                <th class="col-xs-1 text-center">
                                    Payment Method
                                </th>
                                <th class="col-xs-1 text-center">
                                    Note
                                </th>
                            @endunless
                        </tr>

                        <?php $counter = 0; ?>
                        @unless(count($transactions)>0)
                            <td class="text-center" colspan="14">No Records Found</td>
                        @else

                            @foreach($transactions as $index => $transaction)
                            <?php $counter ++ ?>
                            <tr>
                                <td class="col-xs-1 text-center">
                                    {{ $counter }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->id }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->cust_id }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->company }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->status }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{$transaction->delivery_date ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $transaction->delivery_date)->format('Y-m-d') : null}}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->driver }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->gst ? number_format(($transaction->total * 107/100), 2, '.', ',') : number_format($transaction->total, 2, '.', ',') }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->total_qty }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->pay_status }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->paid_by }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->paid_at }}
                                </td>
                                @unless(Auth::user()->hasRole('driver'))
                                    @if($transaction->pay_method)
                                        <td class="col-xs-1 text-center">
                                            {{ $transaction->pay_method == 'cash' ? 'Cash' : 'Cheque/TT' }}
                                        </td>
                                    @else
                                        <td class="col-xs-1 text-center">
                                            -
                                        </td>
                                    @endif

                                    <td class="col-xs-1 text-center">
                                        {{ $transaction->note ? $transaction->note : '-' }}
                                    </td>
                                @endunless
                            </tr>
                            @endforeach
                        @endunless
                    </table>
                </div>
            </div>
            </div>

        </div>
    </body>
</html>