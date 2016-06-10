@extends('template')
@section('title')
Customers Email Draft
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
    </div>

    <div class="panel-body">
        {!! Form::model($email_draft, ['id'=>'update_email', 'action'=>['MarketingController@updateEmailDraft']]) !!}

            <div class="form-group">
                {!! Form::label('content', 'Email Content', ['class'=>'control-label']) !!}
                {!! Form::textarea('content', $email_draft, ['class'=>'form-control', 'rows'=>'5']) !!}
            </div>

            <div class="col-md-12" style="padding-top:15px;">
                <div class="form-group pull-right">
                    {!! Form::submit('Done', ['class'=> 'btn btn-success', 'form'=>'update_email']) !!}
                    <a href="/market/customer" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

@stop