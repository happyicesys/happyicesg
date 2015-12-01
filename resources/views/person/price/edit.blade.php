@extends('template')
@section('title')
Price
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title">
            <strong>
                Editing Price for {{$price->person->company}} : 
                {{$price->item->product_id}} - {{$price->item->name}} - {{$price->item->remark}} 
            </strong>
        </h3>
    </div>

    <div class="panel-body">
        {!! Form::model($price,['method'=>'PATCH','action'=>['PriceController@update', $price->id]]) !!}            

            @include('person.price.form', ['disabled'=>'disabled'])

            <div class="col-md-12">
                <div class="pull-right" style="padding: 10px 80px 0px 0px">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="javascript: history.go(-1)" class="btn btn-default">Cancel</a>            
                </div>
                <div class="pull-left" style="padding: 10px 0px 0px 80px">
                    {!! Form::open(['method'=>'DELETE', 'action'=>['PriceController@destroy', $price->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>                
            </div>
    </div>
</div>
</div>

@stop