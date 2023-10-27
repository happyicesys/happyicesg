@extends('template')
@section('title')
Racking Config
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Editing {{$rackingConfig->name}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($rackingConfig,['id'=>'edit_racking_config', 'method'=>'PATCH','action'=>['RackingConfigController@update', $rackingConfig->id]]) !!}
            @include('user.racking-config.form')
        {!! Form::close() !!}

            <div class="col-md-12 col-xs-12">
                <div class="row">
                    <div class="input-group-btn">
                        <div class="pull-left">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['RackingConfigController@destroy', $rackingConfig->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="pull-right">
                            {!! Form::submit('Edit', ['class'=> 'btn btn-primary', 'form'=>'edit_racking_config']) !!}
                            <a href="/user" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        Attachment(s)
    </div>
    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-9 text-center">
                    Image
                </th>
                <th class="col-md-2 text-center">
                    Action
                </th>
            </tr>

            <tbody>
                @unless(count($attachments)>0)
                    <td class="text-center" colspan="12">No Records Found</td>
                @else
                    @foreach($attachments as $index => $attachment)

                    @php
                        $ext = pathinfo($attachment->full_url, PATHINFO_EXTENSION);
                    @endphp

                    <tr>
                        <td class="col-md-1 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="col-md-9">
                            @if($ext == 'pdf')
                                <embed src="{{$attachment->full_url}}" type="application/pdf" style="max-width:350px; max-height:500px;">
                            @else
                                <a href="{{$attachment->full_url}}">
                                    <img src="{{$attachment->full_url}}" alt="{{$attachment->full_url}}" style="max-width:350px; max-height:350px;">
                                </a>
                            @endif
                        </td>
                        <td class="col-md-2 text-center">
                            @if(!auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                <button type="submit" form="remove_file" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> <span class="hidden-xs">Delete</span></button>
                                @if($ext == 'pdf')
                                    <a href="{{$attachment->full_url}}" class="btn btn-sm btn-info">Download</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endunless
            </tbody>
        </table>
        </div>

        @if(count($attachments) > 0)
            {!! Form::open(['id'=>'remove_file', 'method'=>'DELETE', 'action'=>['RackingConfigController@removeAttachment', $rackingConfig->id, $attachment->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
            {!! Form::close() !!}
        @endif

        {!! Form::open(['action'=>['RackingConfigController@createAttachment', $rackingConfig->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}

        {!! Form::close() !!}
        <label class="pull-right totalnum" for="totalnum">
            Total of {{count($attachments)}} entries
        </label>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        Dropzone.autoDiscover = false;
        $('.dropzone').dropzone({
            init: function()
            {
                this.on("complete", function()
                {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    // location.reload();
                  }
                });
            }

        });
    });
</script>

@stop