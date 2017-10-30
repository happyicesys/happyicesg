<div ng-app="app" ng-controller="operationWorksheetController" ng-cloak>
{!! Form::open([
    'id'=>'search',
    'method'=>'POST',
    'action'=>['OperationWorksheetController@getOperationWorksheetIndex']
]) !!}
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('profile_id', 'Profile', ['class'=>'control-label search-title']) !!}
                {!! Form::select('profile_id', [''=>'All']+
                    $profiles::filterUserProfile()
                        ->pluck('name', 'id')
                        ->all(),
                    request('profile_id') ? request('profile_id') : '',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('id_prefix', 'ID Group', ['class'=>'control-label search-title']) !!}
                <select class="select form-group" name="id_prefix">
                    <option value="">All</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                    <option value="H">H</option>
                    <option value="R">R</option>
                    <option value="S">S</option>
                    <option value="V">V</option>
                    <option value="W">W</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 col-xs-6">
            <div class="form-group">
                {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
                {!! Form::select('custcategory', [''=>'All'] + $custcategories::orderBy('name')->pluck('name', 'id')->all(), request('custcategory') ? request('custcategory') : '', ['class'=>'select form-control'])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('cust_id', 'ID', ['class'=>'control-label search-title']) !!}
                {!! Form::text('cust_id', request('cust_id') ? request('cust_id') : '', ['class'=>'form-control input-sm'])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="form-group">
                {!! Form::label('company', 'ID Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('company', request('company') ? request('company') : '', ['class'=>'form-control input-sm'])
                !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class'=>'control-label search-title']) !!}
                {!! Form::select('status', [''=>'All', 'Delivered'=>'Delivered', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'], request('status') ? request('status') : 'Delivered',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('choosen_date', 'Today Date', ['class'=>'control-label search-title']) !!}
                <div class="input-group date">
                    {!! Form::text('choosen_date', request('choosen_date') ? request('choosen_date') : \Carbon\Carbon::today(), ['class'=>'form-control', 'id'=>'choosen_date']) !!}
                    <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('previous', 'Previous', ['class'=>'control-label search-title']) !!}
                {!! Form::select('previous', ['Last 7 days'=>'Last 7 days', ''=>'Nil', 'Last 14 days'=>'Last 14 days'], request('previous') ? request('previous') : 'Last 7 days',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('future', 'Future', ['class'=>'control-label search-title']) !!}
                {!! Form::select('future', [''=>'Nil', '2 days'=>'2 days'], request('future') ? request('future') : 'Last 7 days',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="form-group">
                {!! Form::label('color', 'Show Color', ['class'=>'control-label search-title']) !!}
                {!! Form::select('color', [''=>'All', 'Yellow'=>'Yellow', 'Red'=>'Red'], request('color') ? request('color') : '',
                    ['class'=>'select form-control'])
                !!}
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<div class="row" style="padding-left: 15px;">
    <div class="col-md-8 col-sm-12 col-xs-12" style="padding-top: 20px;">
        <button type="submit" class="btn btn-default" form="search"><i class="fa fa-search"></i><span class="hidden-xs"></span> Search</button>
        <button class="btn btn-primary" ng-click="exportData($event)"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button>
        <button type="submit" class="btn btn-success" form="update" name="submit_generate" value="submit_generate" ><i class="fa fa-download"></i><span class="hidden-xs"></span> Batch Update</button>
    </div>
{{--
    <div class="col-md-4 col-sm-12 col-xs-12 text-right">
        <div class="row">
            <label for="display_num">Display</label>
            <select name="pageNum">
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="All">All</option>
            </select>
            <label for="display_num2" style="padding-right: 20px">per Page</label>
        </div>
        <div class="row">
            <label class="" style="padding-right:18px;" for="totalnum">Showing  of  entries</label>
        </div>
    </div> --}}
        <div class="row pull-right">
            <label class="" style="padding-right:18px;" for="totalnum">Showing {{count($people)}} entries</label>
        </div>
</div>

    {!! Form::open(['id'=>'update', 'method'=>'POST', 'action'=>['OperationWorksheetController@batchConfirmOperationWorksheet']]) !!}
    <div class="table-responsive" id="exportable" style="padding-top: 20px;">
        <table class="table table-list-search table-hover table-bordered">

            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    <input type="checkbox" id="checkAll" />
                </th>
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    Postcode
                </th>
                <th class="col-md-1 text-center">
                    Cust ID
                </th>
                <th class="col-md-1 text-center">
                    ID Name
                </th>
                <th class="col-md-1 text-center">
                    Category
                </th>
                <th class="col-md-2 text-center">
                    Note
                </th>
                @foreach($dates as $date)
                <th class="col-md-1 text-center">
                    {{$date}}
                </th>
                @endforeach
            </tr>
            @php
                // dd($customers->toArray());
            @endphp

            <tbody>
                @foreach($people as $index => $person)
                <tr>
                    <td class="col-md-1 text-center" rowspan="2">
                        {!! Form::checkbox('checkboxes['.$person->id.']') !!}
                    </td>
                    <td class="col-md-1 text-center" rowspan="2">
                        {{$index + 1}}
                    </td>
                    <td class="col-md-1 text-center" rowspan="2">
                        {{$person->del_postcode}}
                    </td>
                    <td class="col-md-1 text-center" rowspan="2">
                        {{$person->cust_id}}
                    </td>
                    <td class="col-md-1 text-left" rowspan="2">
                        <a href="/person/{{ $person->id }}">
                            {{ $person->cust_id[0] == 'D' || $person->cust_id[0] == 'H' ? $person->name : $person->company }}
                        </a>
                    </td>
                    <td class="col-md-1 text-center" rowspan="2">
                        {{ $person->custcategory->name }}
                    </td>
                    <td class="col-md-2" rowspan="2">
                        {!! Form::textarea('operation_notes['.$person->id.']', $person->operation_note, ['class'=>'text-left form-control', 'rows'=>'3', 'style'=>'min-width: 150px; align-content: left;']) !!}
                    </td>
                    @foreach($dates as $date)
                        <td class="text-center
                            {{
                                \DB::table('operationdates')
                                    ->where('person_id', $person->id)
                                    ->whereDate('delivery_date', '=', $date)
                                    ->first()
                                ?
                                \DB::table('operationdates')
                                    ->where('person_id', $person->id)
                                    ->whereDate('delivery_date', '=', $date)
                                    ->first()
                                    ->color
                                :
                                ''
                            }}
                        " style="border-bottom: none">
                            &nbsp;
                                {{
                                    \DB::table('deals')
                                        ->leftJoin('transactions', 'transactions.id', '=', 'deals.transaction_id')
                                        ->whereIn('transaction_id', $transactionsId)
                                        ->where('transactions.person_id', $person->id)
                                        ->where('transactions.delivery_date', $date)
                                        ->sum('deals.qty')
                                }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($dates as $date)
                        <td class="text-center
                            {{
                                \DB::table('operationdates')
                                    ->where('person_id', $person->id)
                                    ->whereDate('delivery_date', '=', $date)
                                    ->first()
                                ?
                                \DB::table('operationdates')
                                    ->where('person_id', $person->id)
                                    ->whereDate('delivery_date', '=', $date)
                                    ->first()
                                    ->color
                                :
                                ''
                            }}"
                        style="border-top: none">
                            {!! Form::select('selectcolors['.$person->id.'='.str_replace('-', '=', $date).']',
                                    [
                                        '' => 'None',
                                        'Yellow' => 'Yellow',
                                        'Red' => 'Red'
                                    ],
                                    \DB::table('operationdates')
                                        ->where('person_id', $person->id)
                                        ->whereDate('delivery_date', '=', $date)
                                        ->first()

                                    ?
                                    \DB::table('operationdates')
                                        ->where('person_id', $person->id)
                                        ->whereDate('delivery_date', '=', $date)
                                        ->first()
                                        ->color
                                    :
                                    ''
                                    ,
                                    ['class'=>'select form-control'])
                            !!}
                        </td>
                    @endforeach
                </tr>
                @endforeach

                @if(!count($people) > 0)
                <tr>
                    <td colspan="18" class="text-center">No Records Found</td>
                </tr>
                @endif

            </tbody>

        </table>
    </div>
    {!! Form::close() !!}
</div>

<script src="/js/operation_worksheet.js"></script>
{{-- <script>
    $('.select').select2();

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $('#checkAll').change(function(){
        var all = this;
        $(this).closest('table').find('input[type="checkbox"]').prop('checked', all.checked);
    });
</script> --}}