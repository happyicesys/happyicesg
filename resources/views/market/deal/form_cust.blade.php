@inject('people', 'App\Person')
@inject('users', 'App\User')

    <div class="panel panel-primary">
        <div class="panel-body">

            <label style="margin-bottom: 15px; font-size: 18px;">
                <a href="/person/@{{ personModel }}">
                    {{$transaction->person->cust_id}} - {{$transaction->person->company}} ({{$transaction->person->name}})
                </a>
            </label>
            {!! Form::text('person_id', '@{{personModel}}', ['class'=>'hidden form-control']) !!}
            {!! Form::text('person_copyid', '@{{personModel}}', ['class'=>'hidden form-control']) !!}
            {!! Form::text('person_code', '@{{personcodeModel}}', ['class'=>'hidden form-control']) !!}
            {!! Form::text('name', '@{{nameModel}}', ['class'=>'hidden form-control']) !!}

            <div class="row">
                @unless($transaction->person->cust_id[0] === 'H')
                    <div class="col-md-4 form-group">
                        {!! Form::label('bill_address', 'Bill To :', ['class'=>'control-label']) !!}
                        {!! Form::textarea('bill_address', null, ['class'=>'form-control',
                        'ng-model'=>'billModel',
                        'readonly'=>'readonly',
                        'rows'=>'3']) !!}
                    </div>
                @endunless

                {{-- if the conditions given set as readonly --}}
                @if($transaction->status === 'Cancelled' or $transaction->status === 'Delivered' or ($transaction->person->cust_id[0] === 'D' and $people::where('user_id', Auth::user()->id)->first() ? $people::where('user_id', Auth::user()->id)->first()->cust_type === 'AB' : false and $transaction->status === 'Confirmed' and \Carbon\Carbon::today() >= \Carbon\Carbon::parse($transaction->delivery_date)->subDay()))
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
            {{-- if the conditions given set as readonly --}}
            @if($transaction->status === 'Cancelled' or $transaction->status === 'Delivered' or ($transaction->person->cust_id[0] === 'D' and $people::where('user_id', Auth::user()->id)->first() ? $people::where('user_id', Auth::user()->id)->first()->cust_type === 'AB' : false and $transaction->status == 'Confirmed' and \Carbon\Carbon::today() >= \Carbon\Carbon::parse($transaction->delivery_date)->subDay()))
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
                <div class="input-group deldate">
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
            {{-- if the conditions given set as readonly --}}
                @if($transaction->status === 'Cancelled' or $transaction->status === 'Delivered' or ($transaction->person->cust_id[0] === 'D' and $people::where('user_id', Auth::user()->id)->first() ? $people::where('user_id', Auth::user()->id)->first()->cust_type === 'AB' : false and $transaction->status === 'Confirmed' and \Carbon\Carbon::today() >= \Carbon\Carbon::parse($transaction->delivery_date)->subDay()))
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

                <div class="col-md-4 form-group">
                    {!! Form::label('attn_name', 'Attn. Name :', ['class'=>'control-label']) !!}
                    {!! Form::text('attn_name', null, ['class'=>'form-control',
                    'ng-model'=>'attNameModel',
                    'readonly'=>'readonly']) !!}
                </div>

                <div class="col-md-4 form-group">
                    {!! Form::label('tel_no', 'Tel No. :', ['class'=>'control-label']) !!}
                    {!! Form::text('tel_no', null, ['class'=>'form-control',
                    'ng-model'=>'contactModel',
                    'readonly'=>'readonly']) !!}
                </div>
            </div>

            <div class="row">
                @if($transaction->status === 'Verified Paid')
                    <div class="col-md-4 form-group">
                        {!! Form::label('pay_method', 'Payment Method :', ['class'=>'control-label']) !!}
                        {!! Form::select('pay_method',
                            [''=>null, 'cash'=>'Cash', 'cheque'=>'Cheque/TT'],
                            null,
                            ['class'=>'select form-control'])
                        !!}
                    </div>

                    <div class="col-md-4 form-group">
                        {!! Form::label('note', 'Note :', ['class'=>'control-label']) !!}
                        {!! Form::text('note', null, ['class'=>'form-control']) !!}
                    </div>
                @endif
            </div>

        </div>
    </div>



