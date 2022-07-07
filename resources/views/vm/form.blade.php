@inject('people', 'App\Person')
@inject('simcards', 'App\Simcard')
@inject('cashlessTerminals', 'App\CashlessTerminal')

<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('serial_no', 'Serial Num', ['class'=>'control-label']) !!}
                {!! Form::text('serial_no', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('type', 'Type', ['class'=>'control-label']) !!}
                {!! Form::select('type', ['FVM'=>'FVM', 'DVM'=>'DVM', 'COMBI'=>'COMBI', 'Model-E'=>'Model-E', 'F'=>'F'], null, ['class'=>'select form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('simcard_id', 'Simcard Num', ['class'=>'control-label']) !!}
                {!! Form::select('simcard_id', [''=>null] + $simcards::select(DB::raw("CONCAT(simcard_no,' - ',telco_name,' - ',phone_no) AS full, id"))->orderBy('telco_name')->lists('full', 'id')->all(),
                            null,
                            ['class'=>'select form-control']) !!}
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cashless_terminal_id', 'Cashless Terminal', ['class'=>'control-label']) !!}
                {!! Form::select('cashless_terminal_id', [''=>null] + $cashlessTerminals::select(DB::raw("CONCAT(terminal_id,' - ',provider_name) AS full, id"))->orderBy('provider_name')->lists('full', 'id')->all(),
                            null,
                            ['class'=>'select form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('desc', 'Remarks', ['class'=>'control-label']) !!}
                {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'3']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('person_id', 'Current Customer', ['class'=>'control-label']) !!}
                {!! Form::select('person_id', [''=>null,
                    $people::whereHas('profile', function($q){
                        $q->filterUserProfile();
                    })->where(function ($query) use ($vending){
                        $query->whereDoesntHave('vending')->orWhereHas('vending', function($query) {
                            $query->where('person_id', 0);
                        })->orWhereHas('vending', function($query) use ($vending) {
                            $query->where('person_id', $vending->person->id);
                        });
                    })->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all()],
                    null,
                    [
                        'class'=>'select form-control',
                    ])
                !!}
            </div>
        </div>
    </div>

    <div class="panel panel-info" style="margin-top: 20px;">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Revision History</strong></h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <ul class="list-group">
                            @foreach($revisionHistories as $history)
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
                </div>
            </div>
        </div>
    </div>

</div>

@section('footer')
<script>
    $('.select').select2({
        placeholder: 'Select..',
        allowClear: true
    });
</script>
@stop
