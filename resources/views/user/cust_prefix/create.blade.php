@extends('template')
@section('title')
Cust Prefix
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New Cust Prefix</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($custPrefix = new \App\CustPrefix, ['action'=>'CustPrefixController@store']) !!}

            @include('user.cust_prefix.form')

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