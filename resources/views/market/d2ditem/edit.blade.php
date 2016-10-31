@extends('template')
@section('title')
    Setup
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Edit Dtd Item: {{$salesitem->item->product_id}} - {{$salesitem->item->name}}</a></strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($salesitem, ['id'=>'update_form', 'action'=>['MarketingController@updateDtdOnlineItem', $salesitem->id]]) !!}

            @include('market.d2ditem.form')

            <div class="col-md-12" style="padding-top:15px;">
                <div class="form-group pull-left" style="padding: 20px 0px 0px 95px">
                    @if(Auth::user()->hasRole('admin'))
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger', 'form'=>'delete_form']) !!}
                    @endif
                </div>
                <div class="form-group pull-right" style="padding: 20px 95px 0px 0px">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-success', 'form'=>'update_form']) !!}
                    <a href="/market/setup" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}

        {!! Form::open(['id'=>'delete_form', 'method'=>'DELETE', 'action'=>['MarketingController@destroyDtdOnlineItem', $salesitem->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}

        {!! Form::close() !!}
    </div>
</div>
</div>

<script>
    function clicked(e){
        if(!confirm('Are you sure?'))e.preventDefault();
    }
</script>
@stop