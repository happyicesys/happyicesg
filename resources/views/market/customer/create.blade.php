@extends('template')
@section('title')
Customers
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New Customer (H)</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($customer = new \App\Person, ['action'=>'MarketingController@storeCustomer']) !!}

            @include('market.customer.form')

            <div class="col-md-12">
                <div class="form-group pull-right">
                    {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                    <a href="/market/customer" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

@stop