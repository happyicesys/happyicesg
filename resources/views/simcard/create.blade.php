@inject('simcards', 'App\Simcard')

@extends('template')
@section('title')
SIM Card
@stop
@section('content')
{{-- <div class="modal fade" id="simcard_modal" role="dialog"> --}}
    {{-- <form method="POST" action="/api/simcard/update/{{$simcard->id}}"> --}}
    {!! Form::model($simcard = new \App\Simcard, ['action'=>'VMController@storeSimcard']) !!}
        {{-- {!! csrf_field() !!} --}}
        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h4 class="modal-title">
                    {{$simcard->id ? 'Edit SIM Card: '.$simcard->simcard_no.' ('.$simcard->telco_name.')' : 'Create SIM Card'}}
            </h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Phone Num
                        </label>
                        <input type="text" name="phone_no" class="form-control" value="{{$simcard->phone_no}}">
                    </div>
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            Telco Name
                            <span style="color: red;">*</span>
                        </label>
                        <select name="telco_name" id="telco_name" class="select form-control" >
                            <option value="">Select..</option>
                            @foreach($simcards::TELCOPROVIDERS as $index => $value)
                                <option value="{{$index}}" {{$index === $simcard->telco_name ? 'selected' : ''}}>
                                    {{$value}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <label class="control-label">
                            SIM Card Num
                            <small>(Singtel 15 digits, Starhub 18 digits)</small>
                        </label>
                        <input type="text" name="simcard_no" class="form-control" value="{{$simcard->simcard_no}}">
                    </div>

    {{--
                    <div class="col-md-12 col-sm-12 col-xs-12" ng-if="form.vending_id">
                        <div class="form-group">
                            {!! Form::label('serial_no', 'Binded Serial', ['class'=>'control-label']) !!}
                            <a href="/vm/@{{form.vending_id}}/edit">
                                <input type="text" name="serial_no" class="form-control" ng-model=form.serial_no readonly>
                            </a>
                        </div>
                    </div> --}}



                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-success">
                    @if($simcard->id)
                        Save
                    @else
                        Create
                    @endif
                </button>
                <a href="/vm" class="btn btn-default">Back</a>
            </div>
        </div>
    {{-- </form> --}}
    {!! Form::close() !!}
{{-- </div> --}}
@stop