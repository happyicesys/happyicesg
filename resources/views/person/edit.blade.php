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
        {!! Form::hidden('person_id', $person->id) !!}

            @include('person.price.form', ['disabled'=>''])

            <div class="col-md-12">
                <div class="form-group pull-right">
                    {!! Form::submit('Add Price', ['class'=> 'btn btn-success']) !!}
                    <a href="/price" class="btn btn-default">Cancel</a>            
                </div>

            </div>
        {!! Form::close() !!}            
        @else
            <p>**Please Set the Cost Rate in Customer Profile**</p>
        @endif

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

@stop