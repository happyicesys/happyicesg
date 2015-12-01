@inject('permissions', 'App\Permission')

<div class="col-md-8 col-md-offset-2">
    <div class="form-group">
        {!! Form::label('label', 'Access', ['class'=>'control-label']) !!}   
        {!! Form::text('label', null, ['class'=>'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('remark', 'Description', ['class'=>'control-label']) !!}    
        {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'5']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('permission', 'Permissions', ['class'=>'control-label']) !!}    
        {!! Form::select('permission_list[]', $permissions::lists('name', 'id'), null, ['id'=>'permission', 'class'=>'select form-control', 'multiple']) !!}

        <div class="pull-left row" style="padding-left: 15px">
        <input type="checkbox" id="checkbox" >
        Select All
        </div>
    </div>
</div>

@section('footer')
<script>
    $('#permission').select2({
        tags:false
    });

    $("#checkbox").click(function(){
        if($("#checkbox").is(':checked') ){
            $(".select > option").prop("selected","selected");
            $(".select").trigger("change");
        }else{
            $(".select > option").removeAttr("selected");
             $(".select").trigger("change");
         }
    });
</script>
@stop