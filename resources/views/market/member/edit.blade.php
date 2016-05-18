@extends('template')
@section('title')
Members
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Edit Member: {{$person->cust_id}} - {{$person->name}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($person, ['id'=>'update_person', 'action'=>['MarketingController@updateMember', $person->id]]) !!}

            @include('market.member.form')

            <div class="col-md-12" style="padding-top:15px;">
                <div class="form-group pull-left">

                    @if($person->active == 'Yes')
                        {!! Form::submit('Deactivate', ['name'=>'deactive', 'class'=> 'btn btn-warning', 'form'=>'update_person']) !!}
                    @else
                        {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'update_person']) !!}
                    @endif

                    {!! Form::submit('Reset Password', ['name'=>'reset', 'class'=> 'btn btn-primary', 'form'=>'update_person', 'onclick'=>'clicked(event)']) !!}
                </div>
                <div class="form-group pull-right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-success', 'form'=>'update_person']) !!}
                    <a href="/market/member/index" class="btn btn-default">Cancel</a>
                </div>
            </div>
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