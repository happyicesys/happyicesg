@inject('freezers', 'App\Freezer')

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <h3 class="panel-title"><strong>Maintenance Log</strong></h3>
        </div>
    </div>

    <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-2 text-center">
                    Customer
                </th>
                <th class="col-md-2 text-center">
                    Affected Component
                </th>
                <th class="col-md-3 text-center">
                    Repair Details
                </th>
                <th class="col-md-3 text-center">
                    Refund?
                </th>
                <th class="col-md-2 text-center">
                    Action
                </th>
            </tr>

            <tbody>

                <?php $index = $addfreezers->firstItem(); ?>
                @unless(count($addfreezers)>0)
                <td class="text-center" colspan="4">No Records Found</td>
                @else
                    @foreach($addfreezers as $addfreezer)
                    <tr>
                        <td class="col-md-1 text-center">
                            {{ $index++ }}
                        </td>
                        <td class="col-md-7">
                            {{ $addfreezer->freezer->name }}
                        </td>
                        <td class="col-md-2 text-center">
                            {{ $addfreezer->freezerqty }}
                        </td>
                        <td class="col-md-2 text-center">
                            {!! Form::open(['method'=>'DELETE', 'action'=>['PersonController@removeFreezer', $addfreezer->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                                {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm']) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                    @endforeach
                @endunless

            </tbody>
        </table>
        </div>
        {!! $addfreezers->render() !!}
    </div>

    <div class="col-md-12" style="border: solid black thin">
        {!! Form::model($addfreezer = new \App\AddFreezer, ['id'=>'form_freezer', 'action'=>['PersonController@addFreezer', $person->id]]) !!}
        {!! Form::text('person_id', $person->id, ['id'=>'person_id', 'class'=>'hidden form-control']) !!}
        <div class="row">
            <div class="col-md-7">
                <div class="form-group">
                    {!! Form::label('freezer_id', 'Freezer', ['class'=>'control-label']) !!}
                    {!! Form::select('freezer_id', [''=>null]+$freezers::lists('name', 'id')->all(), null, ['class'=>'select form-control']) !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('freezerqty', 'Qty', ['class'=>'control-label']) !!}
                    {!! Form::text('freezerqty', null, ['class'=>'form-control']) !!}
                </div>
            </div>

            <div class="col-md-2">
            {!! Form::submit('Add', ['name'=>'add', 'class'=> 'btn btn-success', 'form'=>'form_freezer', 'style'=>'margin-top:31px']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

