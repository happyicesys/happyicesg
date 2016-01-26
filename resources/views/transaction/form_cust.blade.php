@inject('people', 'App\Person')

    <div class="panel panel-primary">
        <div class="panel-body">

{{--             @if($transaction->status == 'Pending')
            <div class="form-group">
                {!! Form::label('person_id', 'Customer', ['class'=>'control-label']) !!}
                {!! Form::select('person_id', 
                    $people::select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->lists('full', 'id'), 
                    null, 
                    [
                    'id'=>'person_id', 
                    'class'=>'person form-control', 
                    'ng-model'=>'personModel', 
                    'ng-change'=>'onPersonSelected(personModel)'
                    ]) 
                !!}  
            </div>
            @else --}}
                {!! Form::text('person_id', $transaction->person->cust_id.' - '.$transaction->person->company, ['class'=>'form-control', 'id'=>'person_id', 'readonly'=>'readonly']) !!}
 {{--            @endif --}}

                {!! Form::text('person_copyid', '@{{personModel}}', ['class'=>'hidden form-control']) !!}
                {!! Form::text('person_code', '@{{personcodeModel}}', ['class'=>'hidden form-control']) !!}
                {!! Form::text('name', '@{{nameModel}}', ['class'=>'hidden form-control']) !!}

                <div class="row">
                    <div class="col-md-4 form-group">
                        {!! Form::label('bill_address', 'Bill To :', ['class'=>'control-label']) !!}
                        {!! Form::textarea('bill_address', null, ['class'=>'form-control',
                        'ng-model'=>'billModel',  
                        'readonly'=>'readonly',
                        'rows'=>'3']) !!}                
                    </div>

                    @if($transaction->status == 'Cancelled')
                        <div class="col-md-4 form-group">
                            {!! Form::label('del_address', 'Delivery Add :', ['class'=>'control-label']) !!}
                            {!! Form::textarea('del_address', null, ['class'=>'form-control', 
                            'ng-model'=>'delModel', 
                            'readonly'=>'readonly',
                            'rows'=>'3']) !!}                  
                        </div>
    
                        <div class="col-md-4 form-group">
                            {!! Form::label('transremark', 'Remark', ['class'=>'control-label']) !!}
                            {!! Form::textarea('transremark', null, ['class'=>'form-control', 'rows'=>'3', 'readonly'=>'readonly']) !!}
                        </div>                         
                    @else
                        <div class="col-md-4 form-group">
                            {!! Form::label('del_address', 'Delivery Add :', ['class'=>'control-label']) !!}
                            {!! Form::textarea('del_address', null, ['class'=>'form-control', 
                            'ng-model'=>'delModel', 
                            'rows'=>'3']) !!}                  
                        </div>

                        <div class="col-md-4 form-group">
                            {!! Form::label('transremark', 'Remark', ['class'=>'control-label']) !!}
                            {!! Form::textarea('transremark', null, ['class'=>'form-control',
                            'ng-model'=>'transremarkModel',
                            'rows'=>'3']) !!}
                        </div>                         
                    @endif                                       
                </div>

                
                <div class="row">
                @if($transaction->status == 'Cancelled')
                    <div class="col-md-4 form-group">
                        {!! Form::label('order_date', 'Order On :', ['class'=>'control-label']) !!}
                    <div class="input-group date">
                        {!! Form::text('order_date', null, ['class'=>'form-control', 'id'=>'order_date', 'readonly'=>'readonly']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('delivery_date', 'Delivery On :', ['class'=>'control-label']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_date', null, ['class'=>'form-control', 'id'=>'delivery_date', 'readonly'=>'readonly']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                    </div>
                @else 
                    <div class="col-md-4 form-group">
                        {!! Form::label('order_date', 'Order On :', ['class'=>'control-label']) !!}
                    <div class="input-group date">
                        {!! Form::text('order_date', null, ['class'=>'form-control', 'id'=>'order_date']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('delivery_date', 'Delivery On :', ['class'=>'control-label']) !!}
                    <div class="input-group date">
                        {!! Form::text('delivery_date', null, ['class'=>'form-control', 'id'=>'delivery_date']) !!}
                        <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                    </div>
                    </div>
                @endif                

                    <div class="col-md-4 form-group">
                        {!! Form::label('payterm', 'Pay Term :', ['class'=>'control-label']) !!}
                        {!! Form::textarea('payterm', null, ['class'=>'form-control', 
                        'ng-model'=>'paytermModel',
                        'readonly'=>'readonly',
                        'rows'=>'1']) !!}                  
                    </div>                                          
                </div>

                <div class="row">
                    @if($transaction->status == 'Cancelled')
                        <div class="col-md-4 form-group">
                            {!! Form::label('po_no', 'PO # :', ['class'=>'control-label']) !!}
                            {!! Form::text('po_no', null, ['class'=>'form-control', 'readonly'=>'readonly']) !!}                  
                        </div> 
                    @else   
                        <div class="col-md-4 form-group">
                            {!! Form::label('po_no', 'PO # :', ['class'=>'control-label']) !!}
                            {!! Form::text('po_no', null, ['class'=>'form-control']) !!}                  
                        </div> 
                    @endif
                </div>                                             

        </div>
    </div>



