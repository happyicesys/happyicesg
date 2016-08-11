@extends('template')
@section('title')
Members
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Edit Member: {{$person->cust_id}} - <a href="/person/{{$person->id}}/edit" style="color: white;">{{$person->name}}</a></strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($person, ['id'=>'update_person', 'action'=>['MarketingController@updateMember', $person->id]]) !!}

            @include('market.member.form')

            <div class="col-md-12" style="padding-top:15px;">
                <div class="form-group pull-left">

                    @if($person->active == 'Yes')
                        {!! Form::submit('Deactivate', ['name'=>'deactive', 'class'=> 'btn btn-warning', 'form'=>'update_person']) !!}
                    @else
                        @if(Auth::user()->hasRole('admin'))
                            {!! Form::submit('Delete Member', ['class'=> 'btn btn-danger', 'form'=>'delete_person']) !!}
                        @endif
                        {!! Form::submit('Activate', ['name'=>'active', 'class'=> 'btn btn-success', 'form'=>'update_person']) !!}
                    @endif

                    @if(Auth::user()->hasRole('admin'))
                        {!! Form::submit('Reset Password', ['name'=>'reset', 'class'=> 'btn btn-primary', 'form'=>'update_person', 'onclick'=>'clicked(event)']) !!}
                    @endif
                </div>
                <div class="form-group pull-right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-success', 'form'=>'update_person']) !!}
                    <a href="/market/member" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}

        {!! Form::open(['id'=>'delete_person', 'method'=>'DELETE', 'action'=>['MarketingController@destroyMember', $person->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}

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