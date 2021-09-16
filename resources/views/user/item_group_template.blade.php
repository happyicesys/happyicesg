@inject('items', 'App\Item')
@inject('itemGroups', 'App\ItemGroup')

<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">
                Create Item Group
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="name" class="control-label">
                    Group Name
                </label>
                <input type="text" name="name" class="form-control" ng-model="form.name" placeholder="Name">
            </div>
            <button class="btn btn-success btn-md" ng-click="onItemGroupNameCreateClicked()">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                Create
            </button>
        </div>
    </div>
</div>
</div>

<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">
                Create Binding
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="item_group_name" class="control-label">
                    Group Name
                </label>
                <select name="item_group_name" class="form-control select" ng-model="form.item_group_id">
                    <option value=""></option>
                    @foreach($itemGroups->orderBy('name', 'asc')->get() as $itemGroup)
                        <option value="{{$itemGroup->id}}">
                            {{$itemGroup->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="item_id" class="control-label">
                    Item Name
                </label>
                <select name="item_id" class="form-control select" ng-model="form.item_id">
                    <option value=""></option>
                    @foreach($items->orderBy('name', 'asc')->get() as $item)
                        <option value="{{$item->id}}">
                            {{$item->product_id}} - {{$item->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-warning btn-md" ng-click="onItemGroupBindingClicked()">
                <i class="fa fa-retweet" aria-hidden="true"></i>
                Binding
            </button>
        </div>
    </div>
</div>
</div>

<div class="panel panel-default">

    <div class="panel-body">
        <div class="row">
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name', 'ng-change' => "searchDB()"]) !!}
            </div>
            <div class="form-group col-md-4 col-sm-6 col-xs-12">
                {!! Form::label('item_id', 'Item', ['class'=>'control-label search-title']) !!}
                {!! Form::select('item_id', [''=>'All'] + $items::orderBy('name')->pluck('name', 'id')->all(),
                    null,
                    [
                        'class'=>'selectmultiple form-control',
                        'ng-model'=>'search.item_id',
                        'multiple'=>'multiple',
                        'ng-change' => "searchDB()"
                    ])
                !!}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                    {{-- <button class="btn btn-sm btn-primary" ng-click="exportCustCatGroupExcel($event)">Export Excel</button> --}}
                @endif
            </div>
            <div class="col-md-offset-8 col-md-4 col-xs-12 text-right">
                <div class="row">
                    <label for="display_num">Display</label>
                    <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                        <option ng-value="100">100</option>
                        <option ng-value="200">200</option>
                        <option ng-value="All">All</option>
                    </select>
                    <label for="display_num2" style="padding-right: 20px">per Page</label>
                </div>
                <div class="row">
                    <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                </div>
            </div>
        </div>

        <div class="row"></div>
        <div class="table-responsive" id="exportable_item_group">
            <table class="table table-list-search table-hover table-bordered">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                        Name
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-6 text-center">
                        <a href="#" ng-click="sortType = 'items'; sortReverse = !sortReverse">
                        Items
                        <span ng-show="sortType == 'items' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'items' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage" pagination-id="itemGroups" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ data.name }} <br>
                            <button class="btn btn-danger btn-xs" ng-click="onItemGroupDelete(data)">
                                Delete
                            </button>
                        </td>
                        <td class="col-md-9 text-left">
                            <ul ng-repeat="item in data.items">
                                <li>
                                    @{{item.product_id}} - @{{item.name}} &nbsp;

                                    <button class="btn btn-warning btn-xs" ng-click="onItemGroupUnbind(item.id)">
                                        Unbind
                                    </button>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <dir-pagination-controls max-size="5" pagination-id="itemGroups" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
    </div>
</div>