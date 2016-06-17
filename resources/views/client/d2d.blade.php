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
                            {!! Form::model($customer = new \App\Person, ['action'=>'MarketingController@storeBatchCustomer']) !!}

                                <div class="col-md-12 col-xs-12">
                                    <input type="button" class="btn btn-warning" onclick="addCust();" value="+ More">
                                    <div class="table-responsive">
                                    <table class="table table-list-search table-hover table-bordered add_cust" style="margin-top:10px;" id="tabledata">
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
                                            <tr>
                                                <td class="col-md-1 text-center">
                                                    1
                                                </td>
                                                <td class="col-md-6 text-center">
{{--                                                 {!! Form::select('itemArr[]',
                                                    [
                                                        '1'=>'Red Bean Jelly (box/5pcs) - $7.90',
                                                        '2'=>'Chocolate Pie with Mango (box/5pcs) - $7.90',
                                                        '3'=>'QQ Pudding (box/5pcs) - $7.90',
                                                        '4'=>'Green Mango & Lime (box/5pcs) - $7.90',
                                                        '5'=>'Chocolate Roll (flavor/5pcs) - $8.50',
                                                        '6'=>'Vanilla Roll (flavor/5pcs) - $8.50',
                                                        '7'=>'Matcha Roll (flavor/5pcs) - $8.50',
                                                        '8'=>'Strawberry (set/6pcs) - $7.90',
                                                        '9'=>'Mint Chocolate (set/6pcs) - $9.50'
                                                    ]
                                                    ,
                                                    null,
                                                    [
                                                    'id'=>'person_id',
                                                    'class'=>'select form-control',
                                                    'ng-model'=>'itemModel',
                                                    'ng-change'=>'onItemChanged(itemModel)'
                                                    ])
                                                !!} --}}
                                                    <select name="itemArr[]" class="select" ng-model="itemModel" ng-change="onItemChanged(itemModel)">
                                                        <option value="1">Red Bean Jelly (box/5pcs) - $7.90</option>
                                                        <option value="2">Chocolate Pie with Mango (box/5pcs) - $7.90</option>
                                                        <option value="3">QQ Pudding (box/5pcs) - $7.90</option>
                                                        <option value="4">Green Mango & Lime (box/5pcs) - $7.90</option>
                                                        <option value="5">Chocolate Roll (flavor/5pcs) - $8.50</option>
                                                        <option value="6">Vanilla Roll (flavor/5pcs) - $8.50</option>
                                                        <option value="7">Matcha Roll (flavor/5pcs) - $8.50</option>
                                                        <option value="8">Strawberry (set/6pcs) - $7.90</option>
                                                        <option value="9">Mint Chocolate (set/6pcs) - $9.50</option>
                                                    </select>
                                                </td>
                                                <td class="col-md-1 text-center">
                                                    <select name="qtyArr[]" class="select">
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
                                                    <input type="text" name="floorArr[]" class="form-control"/>
                                                </td>
                                            </tr>
                                    </table>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group pull-right">
                                        {!! Form::submit('Next', ['class'=> 'btn btn-lg btn-success']) !!}
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
<script>
    $('.select').select2();
</script>
@stop