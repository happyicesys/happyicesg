@inject('items', 'App\Item')

<div class="col-md-12">
    <div class="panel panel-primary row">
        <div class="panel-body">       

            {!! Form::text('transaction_id', $transaction->id, ['class'=>'hidden form-control']) !!}

            <div class="form-group">
                {!! Form::label('item_label', 'Item', ['class'=>'control-label']) !!}
               
                {{-- <select id="item_id" name="item_id" class="item form-control" 
                        ng-model="itemModel" ng-change="onItemSelected(itemModel)">
                    <option value=""></option>
                    @foreach ($items as $item => $id)
                        <option value="{{ $item }}">{{ $id }}</option>
                    @endforeach
                </select> --}}
                @if($transaction->status == 'Pending')
                <select id="item_id" name="item_id" class="item form-control" 
                        ng-model="itemModel" ng-change="onItemSelected(itemModel)">
                        <option ng-repeat="item in items" ng-value="item.id" value="@{{item.id}}">
                            @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                        </option>
                </select> 
                @else
                <select id="item_id" name="item_id" class="item form-control" 
                        ng-model="itemModel" ng-change="onItemSelected(itemModel)" disabled>
                        <option ng-repeat="item in items" ng-value="item.id" value="@{{item.id}}">
                            @{{item.product_id}} - @{{item.name}} - @{{item.remark}}
                        </option>
                </select>  
                @endif               
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('qty', 'Qty', ['class'=>'control-label']) !!}

                    @if($transaction->status == 'Pending')
                        {!! Form::text('qty', null, [
                            'class'=>'qty form-control', 
                            'id'=>'qty', 
                            'ng-model'=>'qtyModel', 
                            'ng-change'=>'onQtyChange()'
                            ]) 
                        !!}
                    @else
                        {!! Form::text('qty', null, [
                            'class'=>'qty form-control', 
                            'id'=>'qty', 
                            'ng-model'=>'qtyModel', 
                            'ng-change'=>'onQtyChange()',
                            'readonly'=>'readonly'
                            ]) 
                        !!}                    
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">  
                    {!! Form::label('unit', 'Unit', ['class'=>'control-label']) !!}
                    {!! Form::text('unit', null, ['class'=>'unit form-control', 
                    'id'=>'unit', 
                    'ng-model'=>'unitModel',
                    'readonly'=>'readonly']) !!}            
                </div>
            </div>      

            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('amount', 'Amount ($)', ['class'=>'control-label']) !!}
                    {!! Form::text('amount', 
                        null, 
                        [
                        'class'=>'amount form-control', 
                        'id'=>'amount', 
                        'ng-model'=>'amountModel', 
                        'readonly'=>'readonly'
                        ]) 
                    !!}
                </div>
            </div>
        @if($transaction->status == 'Pending')
        {!! Form::submit('Add Item', ['class'=> 'btn btn-success', 'form'=>'form_item', 'style'=>'margin-top:10px;']) !!}   
        @else
        {!! Form::submit('Add Item', ['class'=> 'btn btn-success', 'form'=>'form_item', 'style'=>'margin-top:10px;', 'disabled'=>'disabled']) !!} 
        @endif         

        </div>
    </div>
</div>