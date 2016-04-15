@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="personEditController">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">
                <div class="pull-left display_panel_title">
                    <h3 class="panel-title"><strong>Price Management for Ecommerce</strong></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            {!! Form::model($price = new \App\Price, ['action'=>'PriceController@store']) !!}
            {{-- {!! Form::hidden('person_id', $person->id, ['id'=>'person_id']) !!} --}}

            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered table-condensed">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-8 text-center">
                            Item
                        </th>
                        <th class="col-md-2 text-center">
                            Retail Price ($)
                        </th>
                        <th class="col-md-2 text-center">
                            Quote Price ($)
                        </th>
                    </tr>

                    <tbody>
                    <tr ng-repeat="item in items" class="form-group">
                        <td class="col-md-8">
                            @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                        </td>
                        <td class="col-md-2">
                            <strong>
                                <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel = getRetailInit(item.id)" ng-model="retailModel" />
                            </strong>
                        </td>
                        <td class="col-md-2">
                            <strong>
                                <input type="text" name="quote[@{{item.id}}]" class="text-right form-control" ng-init="quoteModel = getQuoteInit(item.id)" ng-model="quoteModel" ng-value="(+retailModel * personData.cost_rate/100).toFixed(2)"/>
                            </strong>
                        </td>
                    </tr>
                    <tr ng-if="items.length == 0 || ! items.length">
                        <td colspan="4" class="text-center">No Records Found!</td>
                    </tr>
                    </tbody>
                </table>
                <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>
                {!! Form::submit('Done', ['name'=>'done', 'class'=> 'btn btn-success pull-right', 'style'=>'margin-top:17px;']) !!}
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>

<script src="/js/onlineprice.js"></script>
@stop