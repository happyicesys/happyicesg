@extends('template')

@section('title')

    Deals

@stop

@section('content')

<div class="create_edit">

    <div class="panel panel-primary">

        <div class="panel-heading">

            <div class="col-md-3">

                <h3 class="panel-title">

                    <strong>Log History for Running # {{$transaction->id}}</strong>

                </h3>

            </div>
            <div class="col-md-3 col-md-offset-6">
            {!! Form::open(['method'=>'GET', 'action'=>['MarketingController@editDeal', $transaction->id]]) !!}

                {!! Form::submit('Back', ['class'=>'btn btn-sm btn-default pull-right']) !!}

            {!! Form::close() !!}
            </div>
        </div>

        <div class="col-md-10 col-md-offset-1" style="padding-top:10px">
            <ul class="list-group">
            @foreach($transHistory as $history)
                @if($history->key == 'created_at' && !$history->old_value)
                    <li class="list-group-item row">
                        <span class="col-md-9">
                        <strong>{{ $history->userResponsible()->name }}</strong> created this customer at <strong>{{ $history->newValue() }}</strong>
                        </span>
                        <span class="col-md-3">
                        {{$history->created_at->format('d-M-y (h:i a)')}}
                        </span>
                    </li>
                @else
                    <li class="list-group-item row">
                        <span class="col-md-9">
                            <strong>{{ $history->userResponsible()->name }}</strong>
                            @if(! $history->oldValue())
                                set <strong>{{ $history->fieldName() }}</strong>
                            @else
                                changed <strong>{{ $history->fieldName() }}</strong> from <strong>{{ $history->oldValue() }}</strong>
                            @endif
                            to <strong>{{ $history->newValue() }}</strong>
                        </span>
                        <span class="col-md-3">
                            {{ $history->updated_at->format('d-M-y (h:i a)')}}
                        </span>
                    </li>
                @endif
            @endforeach
            </ul>
        </div>

        <div class="panel-body">

        </div>
    </div>
</div>

@stop