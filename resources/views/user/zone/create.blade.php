@extends('template')
@section('title')
Zone
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New Zone</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($zone = new \App\Zone, ['action'=>'ZoneController@store']) !!}

            @include('user.zone.form')

            <div class="col-md-12">
                <div class="row">
                    <div class="input-group-btn">
                        <div class="pull-right">
                            {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                            <a href="/user" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

@stop