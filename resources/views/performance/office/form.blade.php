@inject('people', 'App\Person')
@inject('simcards', 'App\Simcard')
@inject('cashlessTerminals', 'App\CashlessTerminal')
@inject('rackingConfigs', 'App\RackingConfig')

<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                {!! Form::text('name', null, ['class'=>'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="form-group">
              {!! Form::label('desc', 'Desc', ['class'=>'control-label']) !!}
              {!! Form::textarea('desc', null, ['class'=>'form-control', 'rows'=>'5']) !!}
          </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
          {!! Form::label('date_from', 'Start Date', ['class'=>'control-label']) !!}
          <div class="input-group date">
              {!! Form::text('date_from', null, ['class'=>'form-control', 'id'=>'date_from', 'placeholder'=>'Leave Blank Default as Today']) !!}
              <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
          </div>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12 form-group">
        {!! Form::label('date_to', 'Due Date', ['class'=>'control-label']) !!}
        <div class="input-group date">
            {!! Form::text('date_to', null, ['class'=>'form-control', 'id'=>'date_to']) !!}
            <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
        </div>
      </div>
    </div>

</div>

@section('footer')
<script>
  $('.date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
</script>
@stop
