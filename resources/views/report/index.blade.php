@inject('people', 'App\Person')
@inject('custPrefixes', 'App\CustPrefix')
@inject('drivers', 'App\User')
@inject('profiles', 'App\Profile')
@inject('roles', 'App\Role')

@extends('template')
@section('title')
{{ $REPORT_TITLE }}
@stop
@section('content')

    <meta charset="UTF-8">

    <div class="row">
    <a class="title_hyper pull-left" href="/report"><h1>{{ $REPORT_TITLE }} <i class="fa fa-file-text-o"></i></h1></a>
    </div>

            <div class="panel panel-warning" ng-app="app" ng-controller="rptController" ng-cloak>
                <div class="panel-heading">
                        <ul class="nav nav-pills nav-justified" role="tablist">
                            @cannot('transaction_view')
                            <li><a href="#person" role="tab" data-toggle="tab">Customer</a></li>
                            <li><a href="#transaction" role="tab" data-toggle="tab">Transaction</a></li>
                            <li><a href="#byproduct" role="tab" data-toggle="tab">By Product</a></li>
                            <li><a href="#driver" role="tab" data-toggle="tab">Driver</a></li>
                            @endcannot
                            <li class="active"><a href="#dailyrpt" role="tab" data-toggle="tab">Daily Report</a></li>
                        </ul>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        {{-- first content --}}
                        <div class="tab-pane" id="person">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="form-group">
                                        {!! Form::label('cust_choice', 'Select Customer', ['class'=>'control-label']) !!}
                                        {!! Form::open(['id'=>'person_form', 'method'=>'POST','action'=>['RptController@generatePerson']]) !!}
                                        {!! Form::select('cust_choice', [''=>null, 'all'=>'ALL (Info Only)']+
                                            $people::where('cust_id', 'NOT LIKE', 'H%')
                                                    ->whereHas('profile', function($q) {
                                                        $q->filterUserProfile();
                                                    })
                                                    ->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))
                                                    ->pluck('full', 'id')
                                                    ->all(),
                                            null, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                        {!! Form::close() !!}
                                        </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'person_form']) !!}
                                     </div>
                                </div>
                            </div>
                        </div>
                        {{-- second content --}}
                        <div class="tab-pane" id="transaction">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'transaction_form', 'method'=>'POST','action'=>['RptController@generateTransaction']]) !!}

                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::radio('choice_transac', 'tran_specific', 'tran_specific') !!}
                                                {!! Form::label('tran_specific', 'Specific') !!}
                                            </div>

                                            <div class="row">
                                               <div class="desc" id="tran_specific">
                                                   <div class="col-md-4">
                                                        {!! Form::label('transaction_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                        {!! Form::text('transaction_datefrom', null, ['id'=>'transaction_datefrom', 'class'=>'date form-control']) !!}
                                                   </div>

                                                   <div class="col-md-1 text-center">
                                                   <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>

                                                   <div class="col-md-4">
                                                   <br/>
                                                        {!! Form::text('transaction_dateto', null, ['id'=>'transaction_dateto', 'class'=>'date form-control', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>
                                               </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="form-group">
                                                {!! Form::radio('choice_transac', 'tran_all') !!}
                                                {!! Form::label('tran_all', 'By Year') !!}
                                            </div>

                                            <div class="desc col-md-12" id="tran_all">
                                                <select id="transac_year" name="transac_year" class="select">
                                                    <option value="{{Carbon\Carbon::now()->year}}">{{Carbon\Carbon::now()->year}}</option>
                                                    <option value="{{Carbon\Carbon::now()->subYear()->year}}">{{Carbon\Carbon::now()->subYear()->year}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <br/>
{{--
                                        <div class="row">
                                        <div class="form-group">
                                        {!! Form::radio('choice_transac', 'tran_month') !!}
                                        {!! Form::label('tran_month', 'By Month') !!}
                                        </div>

                                       <div class="desc col-md-12" id="tran_month">
                                            <select id="transac_month" name="transac_month" class="select">
                                            @for($i=1; $i<=Carbon\Carbon::now()->month; $i++)
                                                <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}} {{Carbon\Carbon::now()->subYear()->year}}</option>
                                            @endfor
                                            </select>
                                       </div>
                                        </div>
                                        <br/>
 --}}
                                        {!! Form::close() !!}

                                        <div class="col-md-12" style="padding-top: 20px">
                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'transaction_form']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of second --}}
                        {{-- start of third --}}
                        <div class="tab-pane" id="byproduct">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'byproduct_form', 'method'=>'POST','action'=>['RptController@generateByProduct']]) !!}
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="desc">
                                                    <div class="col-md-4">
                                                    {!! Form::label('byproduct_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                    {!! Form::text('byproduct_datefrom', null, ['id'=>'byproduct_datefrom', 'class'=>'date form-control']) !!}
                                                    </div>

                                                    <div class="col-md-1 text-center">
                                                        <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <br/>
                                                        {!! Form::text('byproduct_dateto', null, ['id'=>'byproduct_dateto', 'class'=>'date form-control', 'style'=>'margin-top: 10px;']) !!}
                                                    </div>
                                                    </div>
                                                </div>
                                        {!! Form::close() !!}
                                            </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'byproduct_form']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of third--}}
                        {{-- start of fourth--}}
                        <div class="tab-pane" id="driver">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-8 col-md-offset-2">
                                        {!! Form::open(['id'=>'driver_form', 'method'=>'POST','action'=>['RptController@generateDriver']]) !!}

                                        <div class="form-group">
                                            {!! Form::label('driver', 'Driver', ['class'=>'control-label']) !!}
                                            @can('transaction_view')
                                                {!! Form::select('driver', [''=>null]+$drivers::whereHas('roles', function($q){
                                                        $q->whereName('driver');
                                                })->lists('name', 'name')->all(),
                                                Auth::user()->name, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                            @else
                                                {!! Form::select('driver', [''=>null]+$drivers::whereHas('roles', function($q){
                                                        $q->whereName('driver');
                                                })->lists('name', 'name')->all(),
                                                null, ['class'=>'select form-control', 'id'=>'cust_choice']) !!}
                                            @endcan
                                        </div>


                                        <div class="form-group">
                                            <div class="row">
                                               <div class="desc">
                                                   <div class="col-md-4">
                                                        {!! Form::label('driver_datefrom', 'Dates between', ['class'=>'control-label']) !!}
                                                        {!! Form::text('driver_datefrom', null, ['id'=>'driver_datefrom', 'class'=>'datetoday form-control']) !!}
                                                   </div>

                                                   <div class="col-md-1 text-center">
                                                   <br/>
                                                        {!! Form::label('and', 'To', ['class'=>'control-label', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>

                                                   <div class="col-md-4">
                                                   <br/>
                                                        {!! Form::text('driver_dateto', null, ['id'=>'driver_dateto', 'class'=>'datetoday form-control', 'style'=>'margin-top: 10px;']) !!}
                                                   </div>
                                               </div>
                                            </div>
                                        {!! Form::close() !!}
                                        </div>

                                        {!! Form::submit('Generate', ['class'=> 'btn btn-primary', 'form'=>'driver_form']) !!}
                                     </div>
                                </div>
                            </div>
                        </div>
                        {{-- end of fourth --}}

                        {{-- start of fifth --}}
                        <div class="tab-pane active" id="dailyrpt">
                            @include('report.dailyrpt_template')
                        </div>
                        {{-- end of fifth --}}

                    </div>
                </div>
            </div>

<script src="/js/rpt_index.js"></script>
<script>
    $('.select').select2({
        placeholder :'Select...',
        allowClear: true
    });

    $('.date').datetimepicker({
        format: 'DD MMM YY'
    });

    $('.datetoday').datetimepicker({
        format: 'DD MMM YY',
        defaultDate: new Date()
    });

    $(document).ready(function() {
        $('#tran_all').hide();
        $('#tran_month').hide();
        $("input[name$='choice_transac']").click(function() {
            var test = $(this).val();
            $("div.desc").hide();
            $('#'+test).show();
        });
    });

    $(function() {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });

    $('#checkAll').change(function(){
        var all = this;
        $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
    });

</script>
@stop
