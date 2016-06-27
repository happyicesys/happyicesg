@extends('template_client')
@section('title')
Door To Door
@stop
@section('content')
<div ng-app="app" ng-controller="d2dorderController">

    <section id="portfolio" style="padding:60px 20px 10px 0px;">
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

                            <div id="itemInterface">
                                <div class="col-md-12 col-xs-12">
                                    <input type="button" class="btn btn-warning" onclick="addItem();" value="+ More">
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
                                            <th class="col-md-1 text-center">
                                            </th>
                                        </tr>
                                            <tr class="txtMult">
                                                <td class="col-md-1 text-center rowCount">
                                                    {{-- <input type="text" class="form-control input-sm text-center rowNum" value="1" disabled/> --}}
                                                    1
                                                </td>
                                                <td class="col-md-6 text-center">
                                                    <select name="itemArr[1]" class="select itemClass">
                                                        <option value="1">Red Bean Jelly (5pcs/box) - $7.90</option>
                                                        <option value="2">Chocolate Pie with Mango (5pcs/box) - $7.90</option>
                                                        <option value="3">QQ Pudding (5pcs/box) - $7.90</option>
                                                        <option value="4">Green Mango & Lime (5pcs/box) - $7.90</option>
                                                        <option value="5">Chocolate Roll (5pcs/flavor) - $8.50</option>
                                                        <option value="6">Vanilla Roll (5pcs/flavor) - $8.50</option>
                                                        <option value="7">Matcha Roll (5pcs/flavor) - $8.50</option>
                                                        <option value="8">Strawberry (6pcs/set) - $7.90</option>
                                                        <option value="9">Mint Chocolate (6pcs/set) - $9.50</option>
                                                    </select>
                                                </td>
                                                <td class="col-md-1 text-center">
                                                    <select name="qtyArr[1]" class="select qtyClass">
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
                                                <td class="col-md-1 text-center">
                                                    <input type="text" name="amountArr[1]" class="input-sm form-control amountClass text-right" readonly="readonly" />
                                                </td>
                                                <td class="col-md-1 text-center" style="color:red;">
                                                    <span class="removeClass">&#10006;</span>
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
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group pull-right">
                                        <button class="btn btn-lg btn-success" id="nextButton">Next</button>
                                    </div>
                                </div>
                            </div>

                            <div id="detailInterface">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('name', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('contact', 'Contact Number', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('contact', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                                        {!! Form::email('email', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6">
                                    <div class="form-group">
                                        {!! Form::label('postcode', 'PostCode', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('postcode', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6">
                                    <div class="form-group">
                                        {!! Form::label('block', 'Block', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('block', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6">
                                    <div class="form-group">
                                        {!! Form::label('floor', 'Floor', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('floor', null, ['class'=>'form-control']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6">
                                    <div class="form-group">
                                        {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                                        {!! Form::label('art', '*', ['class'=>'control-label', 'style'=>'color:red;']) !!}
                                        {!! Form::text('unit', null, ['class'=>'form-control']) !!}
                                    </div>
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

<script>

    $('.select').select2({
        placeholder: 'Please Select...',
    });

    $('#detailInterface').hide();

    var division = $('.add_item');

    function addItem(){

        $('.grandTotal').parent().parent().remove();

        var tablerow = $('#tabledata tbody tr').length;

        $(division).append('<tr class="txtMult"><td class="col-md-1 text-center rowCount">'+tablerow+'</td><td class="col-md-6 text-center"><select name="itemArr['+tablerow+']" class="select itemClass"><option value="1">Red Bean Jelly (5pcs/box) - $7.90</option><option value="2">Chocolate Pie with Mango (5pcs/box) - $7.90</option><option value="3">QQ Pudding (5pcs/box) - $7.90</option><option value="4">Green Mango & Lime (5pcs/box) - $7.90</option><option value="5">Chocolate Roll (5pcs/flavor) - $8.50</option><option value="6">Vanilla Roll (5pcs/flavor) - $8.50</option><option value="7">Matcha Roll (5pcs/flavor) - $8.50</option><option value="8">Strawberry (6pcs/set) - $7.90</option><option value="9">Mint Chocolate (6pcs/set) - $9.50</option></select></td><td class="col-md-1 text-center"><select name="qtyArr['+tablerow+']" class="select qtyClass"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></td><td class="col-md-1 text-center"><input type="text" name="amountArr['+tablerow+']" class="input-sm form-control amountClass text-right" readonly="readonly" /></td><td class="col-md-1 text-center" style="color:red;"><span class="removeClass">&#10006;</span></td></tr><tr><td></td><td class="text-center"><strong>Total</strong></td><td></td><td><input type="text" name="total" class="input-sm form-control grandTotal text-right" readonly="readonly" /></td></tr>');

        $('.select').select2();
    }

    $('#nextButton').on('click', function(event){

        event.preventDefault();

        $('#detailInterface').show();

        $('#nextButton').hide();

    });

</script>
<script src="/js/d2dorder.js"></script>
@stop