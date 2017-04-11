<div ng-controller="itemController">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.id', 'placeholder'=>'ID']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('type', 'Action:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('type', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.type', 'placeholder'=>'Action']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('batch_num', 'Batch Num:', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('batch_num', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.batch_num', 'placeholder'=>'Batch Num']) !!}
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('rec_date', 'Received On:', ['class'=>'control-label search-title']) !!}
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="dropdown4" role="button" data-toggle="dropdown" data-target="" href="">
                            <div class="input-group">
                                {!! Form::text('rec_date', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.rec_date', 'placeholder'=>'Received On']) !!}
                            </div>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <datetimepicker data-ng-model="search2.rec_date" data-datetimepicker-config="{ dropdownSelector: '#dropdown4', minView: 'day'}" ng-change="dateChange3(search2.rec_date)"/>
                        </ul>
                    </div>
                </div>
                <div class="form-group col-md-2 col-sm-4 col-xs-6">
                    {!! Form::label('created_at', 'Created On:', ['class'=>'control-label search-title']) !!}
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="dropdown3" role="button" data-toggle="dropdown" data-target="" href="">
                            <div class="input-group">
                                {!! Form::text('created_at', null, ['class'=>'form-control input-sm', 'ng-model'=>'search2.created_at', 'placeholder'=>'Created On']) !!}
                            </div>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <datetimepicker data-ng-model="search2.created_at" data-datetimepicker-config="{ dropdownSelector: '#dropdown3', minView: 'day'}" ng-change="dateChange2(search2.created_at)"/>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
            <div class="pull-right display_panel_title">
                <label for="display_num">Display</label>
                <select ng-model="itemsPerPage2" ng-init="itemsPerPage2='100'">
                  <option ng-value="100">100</option>
                  <option ng-value="200">200</option>
                  <option ng-value="All">All</option>
                </select>
                <label for="display_num2" style="padding-right: 20px">per Page</label>
            </div>
        </div>
    </div>

    <div class="row"></div>

    <div class="table-responsive">
        <table class="table table-list-search table-hover table-bordered">
            <tr style="background-color: #DDFDF8">
                <th class="col-md-1 text-center">
                    #
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType2 = 'id'; sortReverse2 = !sortReverse2">
                    ID
                    <span ng-if="sortType2 == 'id' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'id' && sortReverse2" class="fa fa-caret-up"></span>
                    </a>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType2 = 'type'; sortReverse2 = !sortReverse2">
                    Action
                    <span ng-if="sortType2 == 'type' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'type' && sortReverse2" class="fa fa-caret-up"></span>
                    </a>
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType2 = 'batch_num'; sortReverse2 = !sortReverse2">
                    Batch Num
                    <span ng-if="sortType2 == 'batch_num' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'batch_num' && sortReverse2" class="fa fa-caret-up"></span>
                </th>
                 <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType2 = 'remark'; sortReverse2 = !sortReverse2">
                    Remark
                    <span ng-if="sortType2 == 'remark' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'remark' && sortReverse2" class="fa fa-caret-up"></span>
                    </a>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType2 = 'rec_date'; sortReverse2 = !sortReverse2">
                    Received On
                    <span ng-if="sortType2 == 'rec_date' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'rec_date' && sortReverse2" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-2 text-center">
                    <a href="" ng-click="sortType2 = 'created_at'; sortReverse2 = !sortReverse2">
                    Created On
                    <span ng-if="sortType2 == 'created_at' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'created_at' && sortReverse2" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    <a href="" ng-click="sortType2 = 'created_by'; sortReverse2 = !sortReverse2">
                    Created By
                    <span ng-if="sortType2 == 'created_by' && !sortReverse2" class="fa fa-caret-down"></span>
                    <span ng-if="sortType2 == 'created_by' && sortReverse2" class="fa fa-caret-up"></span>
                </th>
                <th class="col-md-1 text-center">
                    Action
                </th>
            </tr>

            <tbody>
                <tr dir-paginate="inventory in inventories | filter:search2 | orderBy:sortType2:sortReverse2 | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController" pagination-id="inventory">
                    <td class="col-md-1 text-center">@{{ number }} </td>
                    <td class="col-md-1 text-center">@{{ inventory.id }}</td>
                    <td class="col-md-1 text-center">@{{ inventory.type }}</td>
                    <td class="col-md-2 text-center">@{{ inventory.batch_num ? inventory.batch_num : '-' }}</td>
                    <td class="col-md-2 text-center">@{{ inventory.remark }}</td>
                    <td class="col-md-2 text-center">@{{ inventory.rec_date }}</td>
                    <td class="col-md-2 text-center">@{{ inventory.created_at }}</td>
                    <td class="col-md-1 text-center">@{{ inventory.created_by }}</td>
                    <td class="col-md-1 text-center">
                        <a href="/inventory/@{{ inventory.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
                <tr ng-if="(inventories | filter:search2).length == 0 || ! inventories.length">
                    <td class="text-center" colspan="9">No Records Found!</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-md-12 col-sm-12 col-xs-12">
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" pagination-id="inventory"> </dir-pagination-controls>
        <label ng-if-"inventories" class="pull-right totalnum" for="totalnum">Showing @{{(inventories | filter:search).length}} of @{{inventories.length}} entries</label>
    </div>
</div>