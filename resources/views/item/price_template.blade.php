@inject('people', 'App\Person')
@inject('priceTemplates', 'App\PriceTemplate')
{{--
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="panel-title">
                Create Price Template
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="name" class="control-label">
                    Template Name
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
</div> --}}

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
                <label for="price_template_name" class="control-label">
                    Price Template Name
                </label>
                <select name="price_template_name" class="form-control select" ng-model="form.price_template_id">
                    <option value=""></option>
                    @foreach($priceTemplates->orderBy('name', 'asc')->get() as $priceTemplate)
                        <option value="{{$priceTemplate->id}}">
                            {{$priceTemplate->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="person_id" class="control-label">
                    Customer
                </label>
                <select name="person_id" class="form-control select" ng-model="form.person_id">
                    <option value=""></option>
                    @foreach($people->orderBy('name', 'asc')->get() as $person)
                        <option value="{{$person->id}}">
                            {{$person->cust_id}} - {{$person->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-warning btn-md" ng-click="onPriceTemplateBindingClicked()">
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
                {!! Form::label('person_id', 'Customer', ['class'=>'control-label search-title']) !!}
                {!! Form::select('person_id', [''=>'All']+$people::select(DB::raw("CONCAT(cust_id,' - ',name) AS full, id"))->orderBy('cust_id', 'asc')->pluck('full', 'id')->all(),
                    null,
                    [
                        'class'=>'selectmultiple form-control',
                        'ng-model'=>'search.person_id',
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
                        Price Template
                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                    <th class="col-md-6 text-center">
                        <a href="#" ng-click="sortType = 'people'; sortReverse = !sortReverse">
                        Customer Binding
                        <span ng-show="sortType == 'people' && !sortReverse" class="fa fa-caret-down"></span>
                        <span ng-show="sortType == 'people' && sortReverse" class="fa fa-caret-up"></span>
                        </a>
                    </th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage" pagination-id="priceTemplates" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{ data.name }} <br>
                            <button class="btn btn-danger btn-xs" ng-click="onPriceTemplateDelete(data)">
                                Delete
                            </button>
                        </td>
                        <td class="col-md-4 text-left">
                            @{{ data.remarks }}
                        </td>
                        <td class="col-md-5 text-left">
                            <ul ng-repeat="person in data.people">
                                <li>
                                    @{{person.cust_id}} - @{{person.name}} &nbsp;

                                    <button class="btn btn-warning btn-xs" ng-click="onPriceTemplateUnbind(person.id)">
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
        <dir-pagination-controls max-size="5" pagination-id="priceTemplates" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
    </div>
</div>