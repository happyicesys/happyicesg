@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/transaction"><h1>{{ $TRANS_TITLE }} <i class="fa fa-credit-card"></i></h1></a>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="/transaction/create" class="btn btn-success">+ New {{ $TRANS_TITLE }}</a>                          
            </div>
        </div>

        <div class="panel-body">

        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    Invoice No                           
                </th> 
                <th class="col-md-2 text-center">
                    {!! sort_person('company', 'Company') !!}                       
                </th>
                <th class="col-md-2 text-center">
                    {!! sort_person('delivery_date', 'Delivery Date') !!}                       
                </th>                                                                    
                <th class="col-md-1 text-center">
                    {!! sort_person('status', 'Status') !!}                       
                </th>
                <th class="col-md-1 text-center">
                    {!! sort_person('created_at', 'Created On') !!}                       
                </th>         
                <th class="col-md-1 text-center">
                    Created By                      
                </th>                                                               
                <th class="col-md-2 text-center">
                    Action
                </th>                                                                                                
            </tr>

            <tbody>

                <?php $index = $transactions->firstItem(); ?>
                @unless(count($transactions)>0)
                <td class="text-center" colspan="8">No Records Found</td>
                @else
                @foreach($transactions as $transaction)
                <tr>
                    <td class="col-md-1 text-center">{{ $index++ }} </td>
                    <td class="col-md-1 text-center">{{ $transaction->id }} </td>
                    @if($transaction->person_id)
                        <td class="col-md-2 text-center">
                            <a href="/person/{{$transaction->person->id}}">
                            {{$transaction->person->company}} ({{$transaction->person->name}})
                            </a>
                        </td>
                    @else                    
                        <td class="col-md-2 text-center">
                        -
                        </td>
                    @endif

                    @if($transaction->delivery_date)
                        <td class="col-md-1 text-center">
                            {{$transaction->delivery_date}}
                        </td> 
                    @else
                        <td class="col-md-1 text-center">
                            -
                        </td>
                    @endif                    

                    <td class="col-md-1 text-center">
                        {{$transaction->status}}
                    </td>

                    <td class="col-md-1 text-center">
                        {{$transaction->created_at}}
                    </td>

                    <td class="col-md-1 text-center">
                        {{$transaction->user->name}}
                    </td>                                         

                    <td class="col-md-2 text-center">
                        <div class="col-md-4 col-md-offset-1">
                            <a href="/transaction/{{ $transaction->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                        </div>
                        <div class="col-md-3">
                        {!! Form::open(['method'=>'DELETE', 'action'=>['TransactionController@destroy', $transaction->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                            {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}  
                        </div>
                    </td>
                </tr>
                @endforeach
                @endunless                        

            </tbody>
        </table>  
        </div>

        <div class="panel-footer">
            {!! $transactions->render() !!}

            <label class="pull-right totalnum" for="totalnum">
                Total of {{$transactions->total()}} entries
            </label>
        </div>
    </div>
 
@stop