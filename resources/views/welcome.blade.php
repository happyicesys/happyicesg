@inject('profile', 'App\Profile')
@extends('template')
@section('title')
Admin
@stop
@section('content')
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">


    </head>
    <body>

        <style>
            .title {
                font-size: 96px;
                margin: 0;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
                padding-top: 100px;
            }

            @media(min-width:1281px) {
                /*shift content to right and bottom*/
                .title{
                    padding-left: 160px;
                }
            }
        </style>

            <div class="container">
                @if(date("H") < 12)
                    <div class="title">
                        <span class="col-xs-12">Good Morning</span>
                        {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
                    </div>
                @elseif(date("H") > 11 && date("H") < 18)
                    <div class="title">
                        <span class="col-xs-12">Good Afternoon</span>
                        {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
                    </div>
                @elseif(date("H") > 17)
                    <div class="title">
                        <span class="col-xs-12">Good Evening</span>
                        {{-- <span class="col-xs-12 text-center">{{$profile::lists('name')->first()}}</span> --}}
                    </div>
                @endif
            </div>
    </body>
@stop
