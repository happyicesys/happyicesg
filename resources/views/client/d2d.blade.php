@extends('template_client')
@section('title')
Door To Door
@stop
@section('content')
<div ng-app="app" ng-controller="d2dorderController">

<section id="portfolio" style="padding:25px 5px 10px 0px;">
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6 col-xs-12">
                <a href="#" class="portfolio-box">
                    <img src="img/d2d/a5 menu_final.jpg" class="img-responsive" alt="door to door ice cream delivery menu">
                    <div class="portfolio-box-caption-content">
                        <div class="project-category text-faded">
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        {!! Form::open(['action'=>'ClientController@emailOrder']) !!}
                        <div class="col-md-12 col-xs-12">
                            <fieldset ng-if="step1">
                                <div class="form-top">
                                    <div class="form-top-left">
                                        <h3 style="color:#323299;">Step 1 / 2</h3>
                                        <p>Please enter your postcode</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-bottom">
                                            <div class="form-group">
                                                <input type="text" name="postcode" placeholder="Postcode..." class="form-first-name form-control input-lg" ng-model="form.postcode">
                                            </div>
                                            <div id="form-errors"></div>
                                            <button type="button" class="btn btn-success btn-next btn-lg" ng-click="verifyPostcode(form.postcode)">Next <i class="fa fa-spinner fa-spin" ng-if="loading"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div ng-if="step2">
                            <div class="col-md-12 col-xs-12">
                                <h3 style="color:#323299;">Step 2 / 2</h3>
                                <div class="table-responsive">
                                <table class="table table-list-search table-hover table-bordered add_item" style="margin-top:10px;" id="tabledata">
                                    <tr style="background-color: #f7f9f7">
                                        <th class="col-md-1 text-center">
                                            #
                                        </th>
                                        <th class="col-md-6 text-center">
                                            Item
                                        </th>
                                        <th class="col-md-2 text-center">
                                            Qty
                                        </th>
                                        <th class="col-md-2 text-center">
                                            Price
                                        </th>
                                    </tr>
                                    @foreach($lookupArr as $index => $lookup)
                                    <tr class="txtMult">
                                        <td class="col-md-1 text-center rowCount">
                                            {{$index}}
                                        </td>
                                        <td class="col-md-6 text-left">
                                            {{$lookup}}
                                        </td>
                                        <td class="col-md-2 text-center">
                                            <select name="qtyArr[{{$index}}]" class="select qtyClass">
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                            </select>
                                        </td>
                                        <td class="col-md-2 text-center">
                                            <input type="text" name="amountArr[{{$index}}]" class="input-sm form-control amountClass text-right" readonly="readonly" />
                                        </td>
                                        <td class="hidden">
                                            <input type="text" class=" input-sm form-control priceClass text-right" value="{{$priceArr[$index]}}" />
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td class="text-center">Delivery Fees</td>
                                        <td></td>
                                        <td>
                                            <input type="text" name="del_fee" class="input-sm form-control delfeeTotal text-right" readonly="readonly">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="text-center"><strong>Total</strong></td>
                                        <td></td>
                                        <td>
                                            <input type="text" name="total" class="input-sm form-control grandTotal text-right" readonly="readonly">
                                        </td>
                                    </tr>
                                </table>
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-lg btn-success">Next <i class="fa fa-spinner fa-spin" ng-if="loading" ng-click="verifyPostcode(form.postcode)"></i></button>
                                </div>
                            </div>
                        </div>

                        <div ng-if="step3">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('name', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('contact', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::email('email', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('street', 'Street Name', ['class'=>'control-label']) !!}
                                    {!! Form::text('street', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('postcode', 'PostCode', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('postcode', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('block', 'Block', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('block', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('floor', 'Floor', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('floor', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-6">
                                <div class="form-group">
                                    {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                                    {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                    {!! Form::text('unit', null, ['class'=>'form-control']) !!}
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-12 form-group">
                                {!! Form::label('del_date', 'Preferred Delivery Day:', ['class'=>'control-label']) !!}
                                {!! Form::select('del_date',
                                    $dayArr,
                                    null,
                                    ['class'=>'select form-control'])
                                !!}
                            </div>

                            <div class="col-md-6 col-xs-12 form-group">
                                {!! Form::label('del_time', 'Preferred Delivery Timeslot:', ['class'=>'control-label']) !!}
                                {!! Form::select('del_time',
                                    $timeArr,
                                    null,
                                    ['class'=>'select form-control'])
                                !!}
                                <small>{!! Form::label('del', '** Final Timing will be Confirmed via Phone/ SMS', ['class'=>'control-label', 'style'=>'color:red;']) !!}</small>
                            </div>

                            <div class="col-md-12 col-xs-12 form-group">
                                {!! Form::label('remark', 'Remark (Optional)', ['class'=>'control-label']) !!}
                                {!! Form::textarea('remark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
                            </div>

                            <div class="col-md-12 col-xs-12" style="padding-top:20px;">
                                <div class="form-group pull-right">
                                    {!! Form::submit('Confirm', ['class'=> 'btn btn-lg btn-success']) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script src="/js/d2dorder.js"></script>
@stop