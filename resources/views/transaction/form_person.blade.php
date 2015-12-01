@inject('people', 'App\Person')

<div class="col-md-8 col-md-offset-2">

<hr size=2>

    <div class="col-md-12">
        <div class="form-group">
        {!! Form::radio('choice', '1', '1') !!}
        {!! Form::label('existing', 'Existing Customer') !!}
        </div>

        <div id="1" class="desc col-md-12">
        {!! Form::select('person', 
            $people::select(
                DB::raw("CONCAT(company,' - ',name,' (',branch_code,')') AS full, id")
            )->lists('full', 'id'), 
            null, 
            ['id'=>'customer_list', 'class'=>'form-control']) 
        !!}
        </div>
    </div>

    <div class="col-md-12" style="padding-top:15px;">
        <div class="form-group">
        {!! Form::radio('choice', '2') !!}
        {!! Form::label('new', 'New Customer') !!}
        </div>
    </div>

    <div id="2" class="desc col-md-12">
        @include('person.form')
    </div>

 </div>

<script>
    $('#customer_list').select2({
        placeholder: 'Choose Customer...'
    });

    $(document).ready(function() {
    $('#2').hide();
        $("input[name$='choice']").click(function() {
            var test = $(this).val();
            $("div.desc").hide();
            $('#'+test).show();
        });
    });
</script>  
