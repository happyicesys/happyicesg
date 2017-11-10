@extends('template')
@section('title')
Company Profile
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing Company Profile </strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($profile,['method'=>'PATCH','action'=>['ProfileController@update', $profile->id]]) !!}

            @include('profile.form')

            <div class="col-md-12">
                <div class="pull-right form_button_right">
                    {!! Form::submit('Edit', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="/profile" class="btn btn-default">Cancel</a>
                </div>
                <div class="pull-left form_button_left">
                    @can('delete_user')
                    {!! Form::open(['method'=>'DELETE', 'action'=>['ProfileController@destroy', $profile->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                    @endcan
                </div>
            </div>
    </div>
</div>
</div>

<script>
    $('.select').select2({placeholder : 'Select..'});
</script>
@stop