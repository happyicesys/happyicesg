@extends('template')
@section('title')
Batch Create Customers
@stop
@section('content')

<div class="create_edit">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New Customer (H)</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($customer = new \App\Person, ['action'=>'MarketingController@storeBatchCustomer']) !!}


            <div class="col-md-12 col-xs-12">
                <input type="button" class="btn btn-success" onclick="addCust();" value="+ Add More Customer">
                <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered add_cust" style="margin-top:10px;" id="tabledata">
                    <tr style="background-color: #f7f9f7">
                        <th class="col-md-1 text-center">
                            #
                        </th>
                        <th class="col-md-1 text-center">
                            Postal
                        </th>
                        <th class="col-md-1 text-center">
                            Block
                        </th>
                        <th class="col-md-1 text-center">
                            Floor
                        </th>
                        <th class="col-md-1 text-center">
                            Unit
                        </th>
                        <th class="col-md-2 text-center">
                            Name
                        </th>
                        <th class="col-md-2 text-center">
                            Contact
                        </th>
                        <th class="col-md-2 text-center">
                            Remark
                        </th>
                    </tr>
                        <tr>
                            <td class="col-md-1 text-center">
                                1
                            </td>
                            <td class="col-md-1 text-center">
                                <input type="text" name="postalArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-1 text-center">
                                <input type="text" name="blockArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-1 text-center">
                                <input type="text" name="floorArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-1 text-center">
                                <input type="text" name="unitArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-2 text-center">
                                <input type="text" name="nameArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-2 text-center">
                                <input type="text" name="contactArr[]" class="form-control"/>
                            </td>
                            <td class="col-md-2 text-center">
                                <input type="text" name="remarkArr[]" class="form-control"/>
                            </td>
                        </tr>
                </table>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group pull-right">
                    {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                    <a href="/market/customer" class="btn btn-default">Cancel</a>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

<script>
    var division = $('.add_cust');

    function addCust(){

        var tablerow = $('#tabledata tbody tr').length;

        $(division).append('<tr><td class="col-md-1 text-center">'+tablerow+'</td><td class="col-md-1 text-center"><input type="text" name="postalArr[]" class="form-control"/></td><td class="col-md-1 text-center"><input type="text" name="blockArr[]" class="form-control"/></td><td class="col-md-1 text-center"><input type="text" name="floorArr[]" class="form-control"/></td><td class="col-md-1 text-center"><input type="text" name="unitArr[]" class="form-control"/></td><td class="col-md-2 text-center"><input type="text" name="nameArr[]" class="form-control"/></td><td class="col-md-2 text-center"><input type="text" name="contactArr[]" class="form-control"/></td><td class="col-md-2 text-center"><input type="text" name="remarkArr[]" class="form-control"/></td></tr>');
    }
</script>

@stop