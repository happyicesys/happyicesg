@inject('profile', 'App\Profile')
@extends('template')
@section('title')
Item
@stop
@section('content')
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            .title {
                font-size: 96px;
                margin: 0;
                padding: 100px 0px 0px 150px;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';                
            }
        </style>
    </head>
    <body>

                <div class="title">Welcome {{$profile::lists('name')->first()}}</div>

    </body>
@stop
