@inject('people', 'App\Person')

    <div class="panel panel-primary">
        <div class="panel-body">

            @if($transaction->status == 'Pending')
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
            @else
                {!! Form::text('person_id', $transaction->person->company, ['class'=>'form-control', 'id'=>'person_id', 'readonly'=>'readonly']) !!}
            @endif

                {!! Form::text('person_copyid', '@{{personModel}}', ['class'=>'form-control hidden']) !!}
                {{-- <select id="person_id" name="person_id" class="person_select form-control" 
                        ng-model="personModel" ng-change="onPersonSelected(personModel)">
                        <option ng-selected="person.id == personModel" ng-repeat="person in people" ng-value="person.id" value="@{{person.id}}">@{{person.cust_id}} - @{{person.company}} - @{{person.del_address}}</option>
                </select> --}}

                <div class="col-md-6 form-group">
                    {!! Form::label('bill_to', 'Bill To :', ['class'=>'control-label']) !!}
                    {!! Form::textarea('bill_to', null, ['class'=>'form-control',
                    'ng-model'=>'billModel',  
                    'readonly'=>'readonly',
                    'rows'=>'3']) !!}                
                </div>


                <div class="col-md-6 form-group">
                    {!! Form::label('del_address', 'Delivery Add :', ['class'=>'control-label']) !!}
                    {!! Form::textarea('del_address', null, ['class'=>'form-control', 
                    'ng-model'=>'delModel', 
                    'readonly'=>'readonly',
                    'rows'=>'3']) !!}                  
                </div>

                <div class="col-md-6 form-group">
                    {!! Form::label('payterm', 'Pay Term :', ['class'=>'control-label']) !!}
                    {!! Form::textarea('payterm', null, ['class'=>'form-control', 
                    'ng-model'=>'paytermModel',
                    'readonly'=>'readonly',
                    'rows'=>'1']) !!}                  
                </div>                  

                <div class="col-md-6 form-group">
                    {!! Form::label('delivery_date', 'Delivery Date :', ['class'=>'control-label']) !!}
                <div class="input-group date">
                    {!! Form::text('delivery_date', null, ['class'=>'form-control', 'id'=>'delivery_date']) !!}
                    <span class="input-group-addon"><span class="glyphicon-calendar glyphicon"></span></span>
                </div>
                </div>

            <div class="col-md-12 form-group">
                {!! Form::label('transremark', 'Remark', ['class'=>'control-label']) !!}
                {!! Form::textarea('transremark', null, ['class'=>'form-control', 'rows'=>'2']) !!}
            </div>

        </div>
    </div>


