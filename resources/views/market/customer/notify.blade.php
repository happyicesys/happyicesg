@extends('template')
@section('title')
Notify Manager
@stop
@section('content')

<div style="padding-top: 30px;">
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><strong>Notifications : {{$person->cust_id}} - {{$person->name}} </strong>[Manager: {{$person->parent_name}}]</h3>
    </div>

    <div class="panel-body">
        <table class="table table-list-search table-hover table-bordered table-condensed">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-3 text-center">
                    Subject
                </th>
                <th class="col-md-5 text-center">
                    Content
                </th>
                <th class="col-md-1 text-center">
                    Sent On
                </th>
                <th class="col-md-1 text-center">
                    Action
                </th>
            </tr>

            <tbody>

                @unless(count($notifications)>0)
                    <td class="text-center" colspan="6">No Records Found</td>
                @else

                    @foreach($notifications as $index => $notification)
                    <tr class="form-group">
                        <td class="col-md-1 text-center">{{$index+1}}</td>
                        <td class="col-md-3 text-center">{{$notification->title}}</td>
                        <td class="col-md-5 text-center">{{$notification->content}}</td>
                        <td class="col-md-1 text-center">{{$notification->created_at}}</td>
                        <td class="col-md-1 text-center">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['MarketingController@destroyNotification', $notification->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach

                @endunless
            </tbody>
        </table>
    </div>

    <div class="panel-footer">
        {!! Form::model($notification = new \App\NotifyManager, ['id'=>'notify', 'action'=>['MarketingController@storeNotification', $person->id]]) !!}
            {!! Form::hidden('person_id', $person->id, ['class'=>'form-control']) !!}

            <div class="form-group">
                {!! Form::label('title', 'Subject', ['class'=>'control-label']) !!}
                {!! Form::text('title', null, ['class'=>'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('content', 'Content', ['class'=>'control-label']) !!}
                {!! Form::textarea('content', null, ['class'=>'form-control', 'rows'=>'5']) !!}
            </div>

            <div class="pull-right">
                {!! Form::submit('Add', ['name'=>'add', 'class'=> 'btn btn-success', 'form'=>'notify']) !!}
                <a href="/market/customer" class="btn btn-default">Back</a>
            </div>
        {!! Form::close() !!}
    </div>

</div>
</div>
<script>
    $('.date').datetimepicker({
       format: 'DD-MMMM-YYYY'
    });
</script>
@stop

