@inject('accessories', 'App\Accessory')

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Accessories</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-7 text-center">
                    Item
                </th>
                <th class="col-md-2 text-center">
                    Qty
                </th>
                <th class="col-md-2 text-center">
                    Action
                </th>
            </tr>

            <tbody>

                <?php $index = $addaccessories->firstItem(); ?>
                @unless(count($addaccessories)>0)
                <td class="text-center" colspan="4">No Records Found</td>
                @else
                    @foreach($addaccessories as $addaccessory)
                    <tr>
                        <td class="col-md-1 text-center">
                            {{ $index++ }}
                        </td>
                        <td class="col-md-7">
                            {{ $addaccessory->accessory->name }}
                        </td>
                        <td class="col-md-2 text-center">
                            {{ $addaccessory->accessoryqty }}
                        </td>
                        <td class="col-md-2 text-center">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['PersonController@removeAccessory', $addaccessory->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                @endunless

            </tbody>
        </table>
        {!! $addaccessories->render() !!}
    </div>

    <div class="col-md-12" style="border: solid black thin">
        {!! Form::model($addaccessory = new \App\AddAccessory, ['id'=>'form_accessory', 'action'=>['PersonController@addAccessory', $person->id]]) !!}
        {!! Form::text('person_id', $person->id, ['id'=>'person_id', 'class'=>'hidden form-control']) !!}
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    {!! Form::label('accessory_id', 'Accessory', ['class'=>'control-label']) !!}
                    {!! Form::select('accessory_id', [''=>null]+$accessories::lists('name', 'id')->all(), null, ['class'=>'select form-control']) !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('accessoryqty', 'Qty', ['class'=>'control-label']) !!}
                    {!! Form::text('accessoryqty', null, ['class'=>'form-control']) !!}
                </div>
            </div>

            <div class="col-md-2">
            {!! Form::submit('Add', ['name'=>'add', 'class'=> 'btn btn-success', 'form'=>'form_accessory', 'style'=>'margin-top:31px']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

