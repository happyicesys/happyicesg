@inject('bomcategories', 'App\Bomcategory')
@inject('custcategories', 'App\Custcategory')
@inject('bomparts', 'App\Bompart')

<div ng-controller="bomTemplateController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Template Definition
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Cust Category</label>
                                <select class="select form-control" ng-model="search.custcategory_id" ng-change="onCustcategoryChanged()">
                                    <option ng-value=""></option>
                                    @foreach($custcategories::all() as $custcategory)
                                        <option ng-value="{{$custcategory->id}}">
                                            {{$custcategory->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group" ng-show="search.custcategory_id">
                                <label class="control-label">Part</label>
                                <select class="selectpart form-control" ng-model="form.part_id">
                                    <option ng-value="" style="color: red;"></option>
                                    @foreach($bomparts::all() as $bompart)
                                        <option ng-value="{{$bompart->id}}">
                                            {{$bompart->bomcomponent->bomcategory->name}} -
                                            {{$bompart->bomcomponent->name}} -
                                            {{$bompart->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-success btn-block" ng-click="confirmTemplate(search.custcategory_id)" ng-disabled="isFormValid()"><i class="fa fa-check"></i> Add Part</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin') or auth()->user()->hasRole('supervisor'))
                        <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i> Export Excel</button>
                    @endif
                    <button class="btn btn-warning" ng-click="overwriteBom($event, search.custcategory_id)" ng-if="search.custcategory_id"><i class="fa fa-files-o"></i> Overwrite BOM [@{{search.custcategory_name}}]</button>
                    <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
                    <span ng-show="is_done"> <i class="fa fa-check-circle fa-2x" style="color: green;"></i></span>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
{{--                     <div class="row">
                        <label for="display_num">Display</label>
                        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                            <option ng-value="100">100</option>
                            <option ng-value="200">200</option>
                            <option ng-value="All">All</option>
                        </select>
                        <label for="display_num2" style="padding-right: 20px">per Page</label>
                    </div> --}}
                    <div class="row">
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="exportable_bomtemplate" style="padding-top:20px; font-size: 13px;">
                <table ng-repeat="bomcategory in alldata" class="table table-list-search table-hover table-bordered table-condensed">
                    <tr style="background-color: #DDFDF8">
                        <th colspan="14" class="text-left">CAT @{{bomcategory.category_id}} - @{{bomcategory.bomcategory_name}}</th>
                    </tr>
                    <tr style="background-color: #a3a3c2">
                        <th class="col-md-3 text-center">
                            Component Name
                        </th>
                        <th class="col-md-4 text-center">
                            Part Name
                        </th>
                        <th class="col-md-2 text-center">
                            Updated By
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr dir-paginate="bomtemplate in bomcategory.parts | itemsPerPage:itemsPerPage" total-items="totalCount">
                            <th class="col-md-3 text-left" style="background-color: #a3a3c2;">
                                @{{bomtemplate.bomcomponent_name}}
                            </th>
                            <td class="col-md-4 text-left">
                                @{{bomtemplate.bompart_name}}
                            </td>
                            <td class="col-md-2 text-center">
                                @{{bomtemplate.updated_by}}
                            </td>
                            <td class="col-md-1 text-center">
                                <button class="btn btn-danger btn-xs" ng-click="removeEntry(bomtemplate.id)"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered" ng-if="!alldata || alldata.length == 0">
                    <tr>
                        <td colspan="18" style="background-color: #a3a3c2;" class="text-center">No Records Found</td>
                    </tr>
                </table>
            </div>
            <div>
                <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>
    </div>
</div>