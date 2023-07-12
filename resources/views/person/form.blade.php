@inject('banks', 'App\Bank')
@inject('countries', 'App\Country')
@inject('payterm', 'App\Payterm')
@inject('profiles', 'App\Profile')
@inject('custcategories', 'App\Custcategory')
@inject('franchisees', 'App\User')
@inject('users', 'App\User')
@inject('people', 'App\Person')
@inject('persontags', 'App\Persontag')
@inject('persontagattaches', 'App\Persontagattach')
@inject('zones', 'App\Zone')
@inject('locationTypes', 'App\LocationType')

@php
    $disabled = false;
    $disabledStr = '';

    if(auth()->user()->hasRole('watcher') or auth()->user()->hasRole('subfranchisee') or auth()->user()->hasRole('hd_user')) {
        $disabled = true;
        $disabledStr = 'disabled';
    }
@endphp
<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('cust_id', 'ID', ['class'=>'control-label']) !!}
            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
            {!! Form::text('cust_id', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('company', 'ID Name', ['class'=>'control-label']) !!}
            {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
            {!! Form::text('company', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('com_remark', 'Company', ['class'=>'control-label']) !!}
            {!! Form::text('com_remark', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('bill_postcode', 'Billing Postcode', ['class'=>'control-label']) !!}
            {!! Form::text('bill_postcode', null, ['class'=>'form-control', 'id'=>'bill_postcode', 'disabled'=>$disabled, 'ng-model'=>'form.bill_postcode']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('bill_address', 'Billing Address', ['class'=>'control-label']) !!}
            {!! Form::textarea('bill_address', null, ['class'=>'form-control', 'rows'=>'2', 'disabled'=>$disabled, 'ng-model'=>'form.bill_address']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('billing_country_id', 'Billing Country', ['class'=>'control-label']) !!}
            {!! Form::select('billing_country_id', $countries::orderBy('name', 'desc')->lists('name', 'id'), null, ['id'=>'billing_country_id', 'class'=>'selectNormal form-control', 'disabled'=>$disabled]) !!}
        </div>
        <div class="form-group" style="padding-top:20px;">
            {!! Form::checkbox('is_same_address', 1, $person->is_same_address ? true : false, ['ng-model'=>'form.is_same_address', 'ng-change'=>'onIsSameAddressChecked()', 'disabled' => $disabled]) !!}
            <label>Delivery Address Same as Billing Address</label>
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('site_name', 'Delivery Location Name', ['class'=>'control-label']) !!}
            {!! Form::text('site_name', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
        <div class="form-group">
            {!! Form::label('del_postcode', 'Delivery Postcode', ['class'=>'control-label']) !!}
            {!! Form::text('del_postcode', null, ['class'=>'form-control', 'id'=>'del_postcode', 'disabled'=>$disabled, 'ng-model'=>'form.del_postcode']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('del_address', 'Delivery Address', ['class'=>'control-label']) !!}
            {!! Form::textarea('del_address', null, ['id'=>'del_address', 'class'=>'form-control', 'rows'=>'2', 'disabled'=>$disabled, 'ng-model'=>'form.del_address']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('delivery_country_id', 'Delivery Country', ['class'=>'control-label']) !!}
            {!! Form::select('delivery_country_id', $countries::orderBy('name', 'desc')->lists('name', 'id'), null, ['id'=>'delivery_country_id', 'class'=>'selectNormal form-control', 'disabled'=>$disabled]) !!}
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-md btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked()">
                Map <i class="fa fa-map-o"></i>
            </button>
        </div>
    </div>
</div>

<hr>

@if(!auth()->user()->hasRole('salesperson') or (auth()->user()->hasRole('salesperson') and auth()->user()->id == $person->account_manager))
<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('name', 'Att To', ['class'=>'control-label']) !!}
            {!! Form::text('name', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
            {!! Form::text('contact', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('alt_contact', 'Alt Contact', ['class'=>'control-label']) !!}
            {!! Form::text('alt_contact', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
            {!! Form::textarea('email', null, ['class'=>'form-control', 'rows'=>'3', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('cost_rate', 'Cost Rate (%)', ['class'=>'control-label']) !!}
            {!! Form::text('cost_rate', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('payterm', 'Terms', ['class'=>'control-label']) !!}
            {!! Form::select('payterm', $payterm::lists('name', 'name'), null, ['id'=>'payterm', 'class'=>'select form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            {!! Form::label('active', 'Status', ['class'=>'control-label']) !!}
            {!! Form::select('active',
                    [
                        'Potential' => 'Potential',
                        'New' => 'New',
                        'Yes' => 'Active',
                        'Pending' => 'Pending',
                        'No' => 'Inactive',

                    ], null, ['id'=>'active', 'class'=>'select form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>

@php
    $franchisee = null;
    if(auth()->user()->hasRole('franchisee')) {
        $franchisee = auth()->user()->id;
    }
@endphp

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            {!! Form::label('franchisee_id', 'Assign Customer to User', ['class'=>'control-label']) !!}
            {!! Form::select('franchisee_id',
                            ['0'=> 'Nil'] + $franchisees::filterUserFranchise()->pluck('name', 'id')->all(),
                            $franchisee,
                            ['id'=>'franchisee_id', 'class'=>'select form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('profile_id', 'Company Profile', ['class'=>'control-label']) !!}
            {!! Form::select('profile_id',
                            $profiles::filterUserProfile()->select(DB::raw("CONCAT(name,' - ',roc_no) AS full, id"))->pluck('full', 'id'),
                            null, ['id'=>'profile_id', 'class'=>'select form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    @if($person->profile)
        @if($person->profile->gst)
            <div class="col-md-4 col-sm-4 col-xs-6">
                {!! Form::label('is_gst_inclusive', 'Pricing Format', ['class'=>'control-label']) !!}
                {!! Form::select('is_gst_inclusive', ['1' => 'Price with GST', '0' => 'Price before GST'], null, ['class'=>'select form-control', 'disabled'=>$disabled]) !!}
{{--
                <div class="form-group" style="padding-top: 25px;">
                    {!! Form::checkbox('is_gst_inclusive', $person->is_gst_inclusive, null, ['disabled'=>$disabled]) !!}
                    {!! Form::label('is_gst_inclusive', 'Already Added GST? (Profile Default: '.($person->profile->is_gst_inclusive ? 'Already Added GST)' : 'To add GST)'), ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
                </div> --}}
            </div>

            <div class="col-md-4 col-sm-4 col-xs-6">
                <div class="form-group">
                    {!! Form::label('gst_rate', 'GST Rate % (Default: '.($person->profile->gst_rate + 0).')', ['class'=>'control-label']) !!}
                    {!! Form::text('gst_rate', $person->gst_rate, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
                </div>
            </div>
        @endif
    @endif
</div>

<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('custcategory_id', 'Customer Category (Group)', ['class'=>'control-label']) !!}
            {!! Form::select('custcategory_id', [null=>''] + $custcategories::leftJoin('custcategory_groups', 'custcategory_groups.id', '=', 'custcategories.custcategory_group_id')->orderBy('custcategories.name')->select(DB::raw("CONCAT(custcategories.name,' (',custcategory_groups.name,')') AS full, id"))->lists('full', 'id')->all(), null, ['class'=>'select form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
            {!! Form::select('account_manager',
                    [''=>null]+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                    null,
                    ['class'=>'select form-control', 'disabled'=> $disabled])
            !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('zone_id', 'Zone', ['class'=>'control-label']) !!}
            {!! Form::select('zone_id',
                    [''=>null]+ $zones::orderBy('priority')->lists('name', 'id')->all(),
                    null,
                    ['class'=>'select form-control', 'disabled'=> $disabled])
            !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="form-group">
            {!! Form::label('location_type_id', 'Location Type', ['class'=>'control-label']) !!}
            {!! Form::select('location_type_id',
                    [''=>null]+ $locationTypes::orderBy('sequence')->lists('name', 'id')->all(),
                    null,
                    ['class'=>'select form-control', 'disabled'=> $disabled])
            !!}
        </div>
    </div>
</div>

<div class="row" style="padding: 15px 0px 15px 0px;">
    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="is_dvm" {{$person->is_dvm ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('is_dvm', 'Direct Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="is_vending" {{$person->is_vending ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('is_vending', 'Fun Vending Machine', ['class'=>'control-label', 'style'=>'padding-left:5px;', 'ng-model'=>'form.is_vending']) !!}
        </div>
    </div>

    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="is_combi" {{$person->is_combi ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('is_combi', 'Combi', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="is_subsidiary" {{$person->is_subsidiary ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('is_subsidiary', 'Freezer Point (Supermarket)', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="is_non_freezer_point" {{$person->is_non_freezer_point ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('is_non_freezer_point', 'Non Freezer Point', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
        <div class="form-group">
            <input type="radio" name="type" value="none" {{!$person->is_dvm && !$person->is_vending && !$person->is_combi && !$person->is_subsidiary && !$person->is_non_freezer_point ? 'checked' : null}} {{$disabled ? 'disabled' : null}}>
            {!! Form::label('none', 'N/A', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-6">
        <div class="form-group">
            {!! Form::checkbox('is_parent', $person->is_parent, null, ['ng-model'=>'form.is_parent', 'ng-true-value'=>1, 'ng-false-value'=>0]) !!}
            {!! Form::label('is_parent', 'Is Leasor?', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
        </div>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12" ng-if="!form.is_parent">
        <div class="form-group">
            {!! Form::label('parent_id', 'Belongs To Leasor', ['class'=>'control-label']) !!}
            <select name="parent_id" class="selectNormal form-control" {{$disabled ? 'disabled' : ''}}>
                <option value="">Select...</option>
                @foreach($people->where('is_parent', true)->orderBy('cust_id')->get() as $personOption)
                    <option value="{{$personOption->id}}" {{$person->parent_id == $personOption->id ? 'selected' : ''}}>
                        {{$personOption->cust_id}} - {{ $personOption->company }}
                    </option>
                @endforeach
            </select>
            {{-- {!! Form::select('parent_id',
                            $people::select(DB::raw("CONCAT(cust_id,' - ',name) AS full, id"))->where('is_parent', true)->pluck('full', 'id'),
                            null, ['id'=>'parent_id', 'class'=>'select form-control', 'disabled'=>$disabled]) !!} --}}
        </div>
    </div>
</div>
<hr>

@if($person->is_vending)
<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12" ng-if="form.is_vending">
        <div class="form-group">
            {!! Form::checkbox('is_stock_balance_count_required', $person->is_stock_balance_count_required) !!}
            {!! Form::label('is_stock_balance_count_required', 'Is Stock Balance Field Compulsory?', ['class'=>'control-label', 'style'=>'padding-left:10px;']) !!}
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('remark', 'Remark', ['class'=>'control-label']) !!}
            {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'3', 'disabled'=>$disabled]) !!}
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('operation_note', 'Ops Note', ['class'=>'control-label']) !!}
            {!! Form::textarea('operation_note', null, ['class'=>'form-control', 'rows'=>'3', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>
<div class="row">
{{--
    @php
        dd($persontagattaches::where('person_id', $person->id)->pluck('id')->all());
    @endphp --}}
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            {!! Form::label('tags', 'Tags', ['class'=>'control-label']) !!}
            {!! Form::select('tags[]', $persontags::pluck('name', 'id')->all(), $person ? $persontagattaches::where('person_id', $person->id)->pluck('persontag_id')->all() : null, ['class'=>'selectmultiple form-control', 'multiple'=>'multiple']) !!}
{{--
            <select name="persontag_ids" class="selectmultiple form-control" multiple>
                <option ng-repeat="persontag in persontags_options" value="@{{persontag.id}}" ng-if="persontag.person_id ? 'selected' : ''">
                    @{{persontag.name}}
                </option>
            </select> --}}

        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('bank_id', 'Bank', ['class'=>'control-label']) !!}
            {!! Form::select('bank_id',
                    ['None'=>'-- None --']+ $banks::lists('name', 'id')->all(),
                    null,
                    ['class'=>'selectnotclear form-control', 'disabled'=> $disabled])
            !!}
        </div>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="form-group">
            {!! Form::label('account_number', 'Bank Account Number', ['class'=>'control-label']) !!}
            {!! Form::text('account_number', null, ['class'=>'form-control', 'disabled'=>$disabled]) !!}
        </div>
    </div>
</div>

<div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Plotted Map</h4>
            </div>
            <div class="modal-body">
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

