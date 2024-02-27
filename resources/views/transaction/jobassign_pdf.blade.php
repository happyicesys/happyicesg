@inject('users', 'App\User')
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
                <h4 class="text-center"><strong>Job Assign</strong></h4>
            </div>
            <div class="col-xs-6" style="padding-top: 15px; border: thin black solid;">
              <div class="row">
                <div class="col-xs-6">
                  <strong>Delivery Date</strong>
                </div>
                <div class="col-xs-6 text-right">
                  {{$request['delivery_to']}}
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <strong>Total Amount</strong>
                </div>
                <div class="col-xs-6 text-right">
                  {{$grand_delivered_total.' / '.$grand_total}}
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <strong>Total Qty</strong>
                </div>
                <div class="col-xs-6 text-right">
                  {{$grand_delivered_qty.' / '.$grand_qty}}
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <strong>Total Count</strong>
                </div>
                <div class="col-xs-6 text-right">
                  {{$grand_delivered_count.' / '.$grand_count}}
                </div>
              </div>
            </div>

            <div class="avoid" style="padding-top: 10px;">
            <div class="row">
                <div class="col-xs-12" style="padding-top: 10px">

                  @if(count($drivers) == 0)
                    <td class="text-center" colspan="20">No Records Found</td>
                  @else
                    @foreach($drivers as $driver)
                    <table class="table table-bordered table-condensed" style="border:thin solid black;">
                        <tr style="background-color: lightgrey !important;">
                          <th colspan="9">
                            {{$driver['name']}}
                          </th>
                          <th class="col-xs-1 text-right">
                            (Inv#)<br>
                            {{$driver['delivered_count']}} /
                            {{$driver['total_count']}}
                          </th>
                          <th class="col-xs-1 text-right">
                            (S$)<br>
                            {{$driver['delivered_amount']}} /
                            {{$driver['total_amount']}}
                          </th>
                          <th class="col-xs-1 text-right">
                            (Qty)<br>
                            {{$driver['delivered_qty']}} /
                            {{$driver['total_qty']}}
                          </th>
                        </tr>
                        <tr style="background-color: lightgrey !important;">
                            <th class="col-xs-1 text-center"  style="width: 2%;">
                              #
                            </th>
                            <th class="col-xs-1 text-center" style="width: 4%;">
                              INV #
                            </th>
                            <th class="col-xs-1 text-center">
                              Prefix Code
                            </th>
                            <th class="col-xs-1 text-center">
                              ID Name
                            </th>
                            <th class="col-xs-1 text-center">
                              Vend ID
                            </th>
                            <th class="col-xs-1 text-center">
                              Postcode
                            </th>
                            <th class="col-xs-1 text-center">
                              Address
                            </th>
                            <th class="col-xs-1 text-center">
                              Contact
                            </th>
                            <th class="col-xs-1 text-center">
                              注释<br>
                              T.Remark
                            </th>
                            <th class="col-xs-1 text-center">
                              客户属性<br>
                              Ops
                            </th>
                            <th class="col-xs-1 text-center">
                              Total Amount
                            </th>
                            <th class="col-xs-1 text-center">
                              Total Qty
                            </th>
                        </tr>

                        @if(count($driver['transactions']) == 0)
                            <td class="text-center" colspan="18">No Records Found</td>
                        @else
                            @foreach($driver['transactions'] as $transaction)
                            <tr>
                                <td class="col-xs-1 text-center" style="width: 2%;">
                                  {{$transaction->sequence ? (float)$transaction->sequence : null}}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 4%;">
                                    {{ $transaction->id }}
                                </td>
                                <td class="col-xs-1 text-center">
                                  {{ $transaction->cust_prefix_code }}-{{ $transaction->code }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->company }}
                                </td>
                                <td class="col-xs-1 text-center">
                                  {{ $transaction->vend_code }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->del_postcode }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->del_address }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->contact }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->transremark }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->operation_note }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ number_format($transaction->total, 2, '.', ',') }}
                                </td>
                                <td class="col-xs-1 text-center">
                                    {{ $transaction->total_qty }}
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </table>
                    @endforeach
                  @endif
                </div>
            </div>
            </div>

        </div>

    </body>
</html>