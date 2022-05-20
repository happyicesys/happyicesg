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
            table, tr, td, th, tbody, thead, tfoot {
                page-break-inside: avoid !important;
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
        <div class="container-fluid">
            <div class="avoid" style="padding-top: 10px;">
            <div class="row">
                <div class="col-xs-12" style="padding-top: 10px">
                  @if(count($drivers) == 0)
                    <td class="text-center" colspan="18">No Records Found</td>
                  @else
                    @foreach($drivers as $driver)
                    <table class="table table-bordered table-condensed" style="border:thin solid black;">
                        <tr style="background-color: lightgrey !important;">
                            @php
                                $driverResult = null;
                                if($driver['name']) {
                                    $driverResult = \App\User::where('name', $driver['name'])->first();
                                }
                            @endphp
                            <th colspan="3" class="text-center">
                                {{$request['delivery_to']}}
                            </th>
                            <th colspan="3" class="text-center">
                                {{$driverResult && $driverResult->truck ? $driverResult->truck->name : null}} ({{$driver['name']}})
                            </th>
                            <th colspan="3" class="text-center">
                                点数 Location counts: {{count($driver['transactions'])}}
                            </th>
                            <th colspan="3" class="text-center">
                                冰淇淋补货都要收钱
                            </th>
                        </tr>
                        <tr style="background-color: lightgrey !important;">
                            <th class="col-xs-1 text-center" style="width: 2%;">
                              #
                            </th>
                            <th class="col-xs-1 text-center" style="width: 4%;">
                              INV #
                            </th>
                            <th class="col-xs-1 text-center" style="width: 6%;">
                              ID
                            </th>
                            <th class="col-xs-2 text-center" style="width: 8%;">
                                ID Name
                            </th>
                            <th class="col-xs-1 text-center" style="width: 3%;">
                                Postal
                            </th>
                            <th class="col-xs-1 text-center" style="width: 4%;">
                                Contact
                            </th>
                            <th class="col-xs-2 text-center" style="width: 23%;">
                              T.Remark
                            </th>
                            <th class="col-xs-2 text-center" style="width: 22%;">
                                Ops Note
                            </th>
                            <th class="col-xs-1 text-center" style="width: 4%;">
                                Status
                            </th>
                            <th class="col-xs-1 text-center" style="width: 8%;">
                                收钱
                            </th>
                            <th class="col-xs-1 text-center" style="width: 8%;">
                                欠钱
                            </th>
                            <th class="col-xs-1 text-center" style="width: 8%;">
                                旧单
                            </th>
                        </tr>


                        @if(count($driver['transactions']) == 0)
                            <td class="text-center" colspan="18">No Records Found</td>
                        @else
                            @foreach($driver['transactions'] as $transaction)
                            <tr style="page-break-inside: avoid !important;">
                                <td class="col-xs-1 text-center" style="width: 2%;">
                                    {{$transaction->sequence ? (float)$transaction->sequence : null}}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 4%;">
                                    {{ $transaction->id }}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 6%;">
                                    {{ $transaction->cust_id }}
                                </td>
                                <td class="col-xs-2 text-left" style="width: 8%;">
                                    {{ $transaction->company }}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 3%;">
                                    {{ $transaction->del_postcode }}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 4%;">
                                    {{ $transaction->contact }}
                                </td>
                                <td class="col-xs-2 text-left" style="width: 23%;">
                                    {{ $transaction->transremark }}
                                </td>
                                <td class="col-xs-2 text-left" style="width: 22%;">
                                    {{ $transaction->operation_note }}
                                </td>
                                <td class="col-xs-1 text-center" style="width: 4%;">
                                    @if($transaction->status == 'Pending')
                                        <span style="color: red;">
                                            {{$transaction->status}}
                                        </span>
                                    @elseif($transaction->status == 'Confirmed')
                                        <span style="color: orange;">
                                            {{$transaction->status}}
                                        </span>
                                    @elseif($transaction->status == 'Delivered' or $transaction->status == 'Verified Owe' or $transaction->status == 'Verified Paid')
                                        <span style="color: green;">
                                            {{$transaction->status}}
                                        </span>
                                    @elseif($transaction->status == 'Cancelled')
                                        <span style="color: white; background-color: red;">
                                            {{$transaction->status}}
                                        </span>
                                    @endif
                                </td>
                                <td style="width: 8%;"></td>
                                <td style="width: 8%;"></td>
                                <td style="width: 8%;"></td>
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