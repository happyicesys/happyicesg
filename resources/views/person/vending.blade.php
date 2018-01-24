<div class="panel panel-primary">
    <div class="panel-heading">
        Vending Machine
    </div>

    <div class="panel-body">
        <div class="row">
{{--             <div class="col-md-12 col-sm-12 col-xs-12">
                <assignVending id="assignVendingController" :person_id={{json_encode($person->id)}} inline-template>
                    @include('person._assignVending')
                </assignVending>
            </div> --}}
            @if(!$person->is_dvm)
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_piece_price', 'Price Per Piece ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_piece_price', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            @endif
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_monthly_rental', 'Monthly Rental ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_monthly_rental', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            @php
                $profit_sharing_unit = '';
                if($person->is_vending) {
                    $profit_sharing_unit = '$';
                }else {
                    $profit_sharing_unit = '%';
                }
            @endphp
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_profit_sharing', 'Profit Sharing ('.$profit_sharing_unit.')', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_profit_sharing', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_monthly_utilities', 'Monthly Utilities ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_monthly_utilities', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            @if(!$person->is_dvm)
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('vending_clocker_adjustment', 'Clocker Adjustment (%)', ['class'=>'control-label']) !!}
                    {!! Form::text('vending_clocker_adjustment', null, ['class'=>'form-control']) !!}
                </div>
            </div>
            @endif
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="form-group" style="padding-top: 25px;">
                    {!! Form::checkbox('is_profit_sharing_report', $person->is_profit_sharing_report) !!}
                    {!! Form::label('is_profit_sharing_report', 'Profit Sharing Report', ['class'=>'control-label', 'style'=>'padding-left:5px;']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    {!! Form::label('serial_number', 'Serial Number', ['class'=>'control-label']) !!}
                    {!! Form::text('serial_number', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
</div>