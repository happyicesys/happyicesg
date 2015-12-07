@extends('template')
@section('title')
{{ $PERSON_TITLE }}
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Profile for {{$person->cust_id}} : {{$person->company}} </strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($person,['method'=>'PATCH','action'=>['PersonController@update', $person->id]]) !!}            

            @include('person.form')

            <div class="col-md-12">
                <div class="pull-right">
                    {!! Form::submit('Edit Profile', ['class'=> 'btn btn-primary']) !!}
        {!! Form::close() !!}

                    <a href="/person" class="btn btn-default">Cancel</a>            
                </div>
                <div class="pull-left">
                    {!! Form::open(['method'=>'DELETE', 'action'=>['PersonController@destroy', $person->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </div>                
            </div>
    </div>
</div>

{{-- divider --}}
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">         
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Price Management : {{$person->company}}</strong></h3>
            </div>
        </div>      
    </div>

    <div class="panel-body">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>                    
                <th class="col-md-5 text-center">
                    Item                           
                </th>
                <th class="col-md-2 text-center">
                    Retail Price ($)                       
                </th>
                <th class="col-md-2 text-center">
                    Quote Price ($)
                </th>
                 <th class="col-md-2 text-center">
                    Action
                </th>                                                                                                
            </tr>

            <tbody>

                <?php $index = $prices->firstItem(); ?>
                @unless(count($prices)>0)
                <td class="text-center" colspan="7">No Records Found</td>
                @else
                @foreach($prices as $price)
                <tr>
                    <td class="col-md-1 text-center">{{ $index++ }} </td>
                    <td class="col-md-5">
                        {{$price->item->product_id}} - {{$price->item->name}} - {{$price->item->remark}}
                    </td>
                    <td class="col-md-2 text-right">
                        @if($price->retail_price != 0)
                            {{$price->retail_price}}
                        @else
                            -
                        @endif
                    </td>
                    <td class="col-md-2 text-right">
                        @if($price->quote_price != 0)
                            <strong>{{$price->quote_price}}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="col-md-2 text-right">
                        <a href="/price/{{ $price->id }}/edit" class="btn btn-sm btn-primary col-md-4 col-md-offset-2" style="margin-right:5px;">Edit</a>
                        {!! Form::open(['method'=>'DELETE', 'action'=>['PriceController@destroy', $price->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm col-md-5']) !!}
                        {!! Form::close() !!}  
                    </td>
                </tr>
                @endforeach
                @endunless                        

            </tbody>
        </table>     
        {!! $prices->render() !!}
                <div class="pull-right" style="padding: 30px 10px 0px 0px;">
                    <strong>Total of {{$prices->total()}} entries</strong>            
                </div>        
        
    </div>

    <div class="panel-footer">

        @if($person->cost_rate)
            <div class="col-md-9 col-md-offset-1">
                Cost Rate : <strong>{{$person->cost_rate}} % </strong>
            <em style="padding-left:10px">
                (**Edit the Cost Rate in Customer Profile**) 
            </em>
            </div>

        {!! Form::model($price = new \App\Price, ['action'=>'PriceController@store']) !!}
        {!! Form::hidden('person_id', $person->id, ['id'=>'person_id']) !!}

            @include('person.price.form', ['disabled'=>''])

            <div class="col-md-12">
                <div class="form-group pull-right" style="padding-right: 80px">
                    {!! Form::submit('Add Price', ['class'=> 'btn btn-success']) !!}           
                </div>

            </div>
        {!! Form::close() !!}            
        @else
            <p>**Please Set the Cost Rate in Customer Profile**</p>
        @endif

    </div>
</div>
{{-- divider --}}

<div class="panel panel-primary" ng-app="app" ng-controller="personEditController">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>Transaction History for {{$person->cust_id}} : {{$person->company}} </strong></h3>
    </div>

    <div class="panel-body">

    <div class="panel panel-default">
        <div class="panel-heading">
                <div class="panel-title">

                    <div class="pull-left display_num">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                          <option>10</option>
                          <option>20</option>
                          <option>30</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div>

                    {{-- <div class="pull-right">
                        <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>                        
                    </div> --}}
                </div>
            </div>

            <div class="panel-body">
                <div style="padding-bottom: 10px">
                    <label for="search_name" class="search">Search Invoice:</label>
                    <input type="text" ng-model="search.id">
                    {{-- <label for="search_company" class="search" style="padding-left: 10px">Company:</label>
                    <input type="text" ng-model="search.person.company"> --}}
                    <label for="search_status" class="search" style="padding-left: 10px">Status:</label>
                    <input type="text" ng-model="search.status">
                    <label for="search_payment" class="search" style="padding-left: 10px">Payment:</label>
                    <input type="text" ng-model="search.pay_status">                    
                </div>
                <table class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center">
                            #
                        </th>                    
                        <th class="col-md-1 text-center">
                            <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                            Invoice No
                            <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                                                       
                        </th>
                        {{-- <th class="col-md-2 text-center">
                            Company   
                        </th> --}}
                         <th class="col-md-1 text-center">
                            Payment
                        </th>                         
                        <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                            Delivery Date
                            <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </th>
                        <th class="col-md-1 text-center">
                            <a href="#" ng-click="sortType = 'status'; sortReverse = !sortReverse">
                            Status
                            <span ng-show="sortType == 'status' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'status' && sortReverse" class="fa fa-caret-up"></span>                            
                        </th>                                                
                         <th class="col-md-2 text-center">
                            <a href="#" ng-click="sortType = 'created_at'; sortReverse = !sortReverse">                         
                            Created On
                            <span ng-show="sortType == 'created_at' && !sortReverse" class="fa fa-caret-down"></span>
                            <span ng-show="sortType == 'created_at' && sortReverse" class="fa fa-caret-up"></span>                          
                        </th>
                         <th class="col-md-1 text-center">
                            Created By
                        </th>                        
                         <th class="col-md-2 text-center">
                            Action
                        </th>                                                                                                
                    </tr>

                    <tbody>
                        <tr dir-paginate="transaction in transactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                            <td class="col-md-1 text-center">@{{ number }} </td>
                            <td class="col-md-1 text-center">@{{ transaction.id }} </td>
                            {{-- <td class="col-md-2 text-center">
                            <a href="/person/@{{ transaction.person.id }}">
                            @{{ transaction.person.company }}
                            </a>
                            </td> --}}
                            <td class="col-md-1 text-center">@{{ transaction.pay_status }}</td>
                            <td class="col-md-2 text-center">@{{ transaction.delivery_date }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.status }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.created_at }}</td>
                            <td class="col-md-1 text-center">@{{ transaction.user.name }}</td>
                            <td class="col-md-2 text-center">
                                    <a href="/transaction/@{{ transaction.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                    <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(transaction.id)">Delete</button>  
                            </td>
                        </tr>
                        <tr ng-show="(transactions | filter:search).length == 0 || ! transactions.length">
                            <td colspan="9" class="text-center">No Records Found</td>
                        </tr>                         

                    </tbody>
                </table>            
            </div>
                <div class="panel-footer">
                      <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                      <label class="pull-right totalnum" for="totalnum">Showing @{{(transactions | filter:search).length}} of @{{transactions.length}} entries</label> 
                </div>
        </div>
    </div>     
</div>

{{-- divider --}}

<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>File : {{$person->company}}</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>                    
                <th class="col-md-7 text-center">
                    Path                           
                </th>
                <th class="col-md-2 text-center">
                    Upload On                      
                </th>
                <th class="col-md-2 text-center">
                    Action
                </th>                                                                                                
            </tr>

            <tbody>

                <?php $index = $files->firstItem(); ?>
                @unless(count($files)>0)
                <td class="text-center" colspan="7">No Records Found</td>
                @else
                @foreach($files as $file)
                <tr>
                    <td class="col-md-1 text-center">{{ $index++ }} </td>
                    <td class="col-md-7">
                        <a href="{{$file->path}}">
                        {!! str_replace("/person_asset/file/", "", "$file->path"); !!}
                        </a>                            
                    </td>
                    <td class="col-md-2 text-center">{{$file->created_at}}</td>
                    <td class="col-md-2 text-center">
                        {!! Form::open(['method'=>'DELETE', 'action'=>['PersonController@removeFile', $file->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!} 
                    </td>
                </tr>
                @endforeach
                @endunless                        

            </tbody>
        </table>      
        {!! $files->render() !!}
    </div>

    <div class="panel-footer">
        {!! Form::open(['action'=>['PersonController@addFile', $person->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
        {!! Form::close() !!}
        <label class="pull-right totalnum" for="totalnum">
            Total of {{$files->total()}} entries
        </label>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
    Dropzone.autoDiscover = false;
    $('.dropzone').dropzone({
        init: function() 
        {
            this.on("complete", function()
            {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                location.reload();
              }                
            });
        }
    });
});
</script>
<script src="/js/person_edit.js"></script>  

@stop