@inject('profile', 'App\Profile')
@extends('template')
@section('title')
Item
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
                padding-top: 60px;               
            }
        </style>

            <div class="container">
                <div class="col-xs-10 col-xs-offset-1">
                    <div class="title">Welcome {{$profile::lists('name')->first()}}</div>
                </div>
            </div>                
    </body>
@stop
