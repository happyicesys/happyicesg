@inject('people', 'App\Person')
@inject('simcards', 'App\Simcard')

<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('serial_no', 'Serial Num', ['class'=>'control-label']) !!}
                {!! Form::text('serial_no', null, ['class'=>'form-control']) !!}
            </div>
        </div>        
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('vend_id', 'Vend ID', ['class'=>'control-label']) !!}
                {!! Form::text('vend_id', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('type', 'Type', ['class'=>'control-label']) !!}
                {!! Form::select('type', ['FVM'=>'FVM', 'DVM'=>'DVM', 'COMBI'=>'COMBI'], null, ['class'=>'select form-control']) !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('router', 'Router/ HP', ['class'=>'control-label']) !!}
                {!! Form::text('router', null, ['class'=>'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="form-group">
                {!! Form::label('simcard_id', 'Simcard Num', ['class'=>'control-label']) !!}
                {!! Form::select('simcard_id', [''=>null] + $simcards::select(DB::raw("CONCAT(simcard_no,' - ',telco_name,' - ',phone_no) AS full, id"))->orderBy('telco_name')->lists('full', 'id')->all(),
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
                    })->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all()],
                    null,
                    [
                        'class'=>'select form-control',
                    ])
                !!}
            </div>
        </div>  
    </div>  

</div>

@section('footer')
<script>
    $('.select').select2({
        placeholder: 'Select..'
    });
</script>
@stop
