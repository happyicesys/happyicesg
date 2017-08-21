@inject('profile', 'App\Profile')

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    {{-- <link href="{{ asset('/css/head_footer.min.css') }}" rel="stylesheet" type="text/css" media="all"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="fonts.googleapis.com/earlyaccess/cwtexkai.css"> --}}
    <style>
        body {
            font-family: "Helvetica";
            line-height: 1.2;
            font-size: 12px;
        }
        h3 {
            font-family: "Georgia";
            margin-top: 5px;
            margin-bottom: 0px;
        }
        .close-topdown {
            margin-top: 0px;
            margin-bottom: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
        }
        .row    {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
    </style>
</head>
<body>
        <div class="row col-xs-12">
            <div class="col-xs-8">

            </div>
            <div class="col-xs-4 text-left">
                <span class="col-xs-12">
                    <strong>{{$issuebillprofile->name}}</strong>
                </span>
                <span class="col-xs-12">
                    {{$issuebillprofile->address}}
                </span>
                <span class="col-xs-12">
                    <span class="col-xs-2">
                        <span class="row">
                            <small>Tel:</small>
                        </span>
                    </span>
                    <span class="col-xs-10">
                        <span class="row">
                            <small>{{$issuebillprofile->contact}}</small>
                        </span>
                    </span>
                </span>
                <span class="col-xs-12">
                    <span class="col-xs-2">
                        <span class="row">
                            <small>Email:</small>
                        </span>
                    </span>
                    <span class="col-xs-10">
                        <span class="row">
                            <small>{{$issuebillprofile->email}}</small>
                        </span>
                    </span>
                </span>
                <span class="col-xs-12">
                    <span class="col-xs-2">
                        <span class="row">
                            <small>ROC:</small>
                        </span>
                    </span>
                    <span class="col-xs-10">
                        <span class="row">
                            <small>{{$issuebillprofile->roc_no}}</small>
                        </span>
                    </span>
                </span>
                <span class="row col-xs-12" style="font-size: 18px; padding-top: 6px; margin-left: 9px;">
                    <strong>
                    DO
                    @if($issuebillprofile->gst)
                        <span>/ TAX</span>
                    @endif
                    INVOICE
                    </strong>
                </span>
                <span style="font-size: 10px; padding-top: 10px;">
                    <span class="col-xs-12">
                        <span class="col-xs-5">
                            <span class="row">
                                <strong>INV No:</strong>
                            </span>
                        </span>
                        <span class="col-xs-7 text-right">
                            <span class="row">
                                {{$running_no}}
                            </span>
                        </span>
                    </span>

                    <span class="col-xs-12">
                        <span class="col-xs-5">
                            <span class="row">
                                <strong>Date:</strong>
                            </span>
                        </span>
                        <span class="col-xs-7 text-right">
                            <span class="row">
                                {{\Carbon\Carbon::today()->format('d-M-y')}}
                            </span>
                        </span>
                    </span>
                    <span class="col-xs-12">
                        <span class="col-xs-5">
                            <span class="row">
                                <strong>Terms:</strong>
                            </span>
                        </span>
                        <span class="col-xs-7 text-right">
                            <span class="row">
                                <small>{{$issuebillprofile->payterm->name}}</small>
                            </span>
                        </span>
                    </span>
                    <span class="col-xs-12">
                        <span class="col-xs-5">
                            <span class="row">
                                <strong>By:</strong>
                            </span>
                        </span>
                        <span class="col-xs-7 text-right">
                            <span class="row">
                                {{auth()->user()->name}}
                            </span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
</body>
</html>