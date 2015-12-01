<div class="col-md-8 col-md-offset-2">
    <div class="form-group">
        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
        {!! Form::text('name', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('roc_no', 'ROC No.', ['class'=>'control-label']) !!}
        {!! Form::text('roc_no', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('address', 'Address', ['class'=>'control-label']) !!}
        {!! Form::textarea('address', null, ['class'=>'form-control', 'rows'=>'3']) !!}
    </div>
                                                   

    <div class="form-group">
        {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('alt_contact', 'Alt. Contact', ['class'=>'control-label']) !!}
        {!! Form::text('alt_contact', null, ['class'=>'form-control']) !!}
    </div>    

    <div class="form-group">
        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
        {!! Form::email('email', null, ['class'=>'form-control']) !!}
    </div>    

    <div class="form-group">
        {!! Form::label('logo', 'Logo', ['class'=>'control-label']) !!}
        {!! Form::file('logo', ['class'=>'form-control', 'name'=>'logo']) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('header', 'Header', ['class'=>'control-label']) !!}
        {!! Form::file('header', ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('footer', 'Footer', ['class'=>'control-label']) !!}
        {!! Form::file('footer', ['class'=>'form-control']) !!}
    </div>         

   
</div>
