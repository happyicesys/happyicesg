<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('job_title', 'Job Title', ['class'=>'control-label']) !!}
        {!! Form::text('job_title', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('duty', 'Duty and Responsibility', ['class'=>'control-label']) !!}
        {!! Form::textarea('duty', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('work_start', 'Work Start Time', ['class'=>'control-label']) !!}
        <div class="input-group date">
        {!! Form::text('work_start', null, ['class'=>'time form-control', 'id'=>'work_start']) !!}
        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('work_end', 'Work End Time', ['class'=>'control-label']) !!}
        <div class="input-group date">
        {!! Form::text('work_end', null, ['class'=>'time form-control']) !!}
        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('contact', 'Phone', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        {!! Form::label('address', 'Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('address', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('start_date', 'Start Date', ['class'=>'control-label']) !!}
        <div class="input-group date">
        {!! Form::text('start_date', null, ['class'=>'datetimepicker form-control', 'placeholder'=>'Choose a Date']) !!}
        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('end_date', 'End Date', ['class'=>'control-label']) !!}
        <div class="input-group date">
        {!! Form::text('end_date', null, ['class'=>'datetimepicker form-control', 'placeholder'=>'Optional', 'id'=>'end_date']) !!}
        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
        </div>
    </div> 

                                              

</div>

<script>
    $('.date').datetimepicker({
       format: 'DD-MMMM-YYYY'
    });

    $('.time').datetimepicker({
        format: 'hh:mm A'
    })

    $('#pass_expiry').val('');
    $('#end_date').val('');

    $('#role').select2({
        tags:false
    });     
</script>