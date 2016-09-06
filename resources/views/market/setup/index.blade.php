@inject('items', 'App\Item')
@inject('price', 'App\DtdPrice')

@extends('template')
@section('title')
Setup
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/market/setup"><h1>Setup <i class="fa fa-cog"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app" ng-controller="setupController">
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="active"><a href="#price" role="tab" data-toggle="tab"> Item Price List</a></li>
            {{-- <li><a href="#postal" role="tab" data-toggle="tab">Postal Data</a></li> --}}
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane active" id="price">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="pull-left ">
                                <h4><strong>Price Management for DTD</strong></h4>
                            </div>
                            <div class="pull-right ">
                                {!! Form::submit('Done', ['class'=> 'btn btn-success', 'form'=>'done_price']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        {!! Form::model($price = new \App\DtdPrice, ['action'=>'MarketingController@storeSetupPrice', 'id'=>'done_price']) !!}
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
{{--                                 <tr ng-repeat="item in items" class="form-group">
                                    <td class="col-md-8">
                                        @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                                    </td>
                                    <td class="col-md-2">
                                        <strong>
                                            <input type="text" name="retail[@{{item.id}}]" class="text-right form-control" ng-init="retailModel=getRetailInit(item.id)" ng-model="retailModel" />
                                        </strong>
                                    </td>
                                    <td class="col-md-2">
                                        <strong>
                                            <input type="text" name="quote[@{{item.id}}]" class="text-right form-control" ng-init="quoteModel=getQuoteInit(item.id)" ng-model="quoteModel" />
                                        </strong>
                                    </td>
                                </tr>
                                <tr ng-if="items.length == 0 || ! items.length">
                                    <td colspan="4" class="text-center">No Records Found!</td>
                                </tr> --}}

                                    @unless(count($items)>0)
                                        <td class="text-center" colspan="7">No Records Found</td>
                                    @else

                                        @foreach($items::orderBy('product_id')->get() as $item)
                                        <tr class="form-group">
                                            <td class="col-md-8">
                                                {{$item->product_id}} - {{$item->name}} - {{$item->remark}}
                                            </td>
                                            <td class="col-md-2">
                                                <strong>
                                                    <input type="text" name="retail[{{$item->id}}]" value="{{$price::whereItemId($item->id)->first() ? $price::whereItemId($item->id)->first()->retail_price : '0'}}" class="text-right form-control"/>
                                                </strong>
                                            </td>
                                            <td class="col-md-2">
                                                <strong>
                                                    <input type="text" name="quote[{{$item->id}}]" value="{{$price::whereItemId($item->id)->first() ? $price::whereItemId($item->id)->first()->quote_price : '0'}}" class="text-right form-control"/>
                                                </strong>
                                            </td>
                                        </tr>
                                        @endforeach

                                    @endunless
                                </tbody>
                            </table>
                            <label ng-if="prices" class="pull-left totalnum" for="totalnum">@{{prices.length}} price(s) created/ @{{items.length}} items</label>

                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
            {{-- end of first element--}}
            {{-- second element --}}
            <div class="tab-pane" id="postal">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="pull-left">
                                <strong>
                                    Postal Data
                                </strong>
                            </div>
                            <div class="pull-right">
                                <a href="" class="btn btn-success">+ Upload Database (Excel)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end of second element --}}
        </div>
    </div>
</div>

<script src="/js/dtdprice.js"></script>
<script>
    $(function() {
        // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // save the latest tab; use cookies if you like 'em better:
            localStorage.setItem('lastTab', $(this).attr('href'));
        });

        // go to the latest tab, if it exists:
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>
@stop