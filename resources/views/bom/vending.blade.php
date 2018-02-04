@inject('bomcategories', 'App\Bomcategory')
@inject('people', 'App\Person')
@inject('bomparts', 'App\Bompart')

<div ng-controller="bomVendingController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        BOM Comparison
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group" ng-repeat="(index ,formsearch) in formsearches">
                                <label class="control-label">Customer @{{index + 1}}</label>
                                <select ui-select2 class="form-control" name="formsearches[]" ng-model="formsearch.person_id" data-placeholder="Select..">
                                    @foreach($people::where('is_vending', 1)->orWhere('is_dvm', 1)->has('custcategory')->orderBy('cust_id', 'asc')->get() as $person)
                                        <option value="{{$person->id}}">
                                            {{$person->cust_id}} - {{$person->company}}
                                            @if($person->custcategory)
                                                [{{$person->custcategory['name']}}]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-primary btn-block" ng-click="addCustomer()"><i class="fa fa-plus-square"></i> Add Customer</button>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-success btn-block" ng-click="generateBomList()"><i class="fa fa-check"></i> Generate BOM List</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{-- <button class="btn btn-primary" ng-click="exportData()">Export Excel</button> --}}
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
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{bomcategories.length}} entries</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="exportable_bomvending" style="padding-top:20px;" ng-if="bomcategories || bomcategories.length > 0">
                <table class="table table-list-search table-hover table-bordered table-condensed table-fixedheader">
                    <tr style="background-color: #DDFDF8">
                       <td colspan="1"></td>
                       <th ng-repeat="person in people" class="col-md-2 text-center">
                            @{{person.cust_id}} - @{{person.company}}
                       </th>
                    </tr>
                    <tr style="background-color: #DDFDF8">
                        <th class="text-left">
                            Category
                        </th>
                        <th ng-repeat="person in people" class="col-md-2 text-center">
                            @{{person.custcategory_name}}
                        </th>
                    </tr>

                    <tr style="background-color: #a3a3c2" ng-repeat-start="bomcategory in bomcategories">
                        <th colspan="14" class="text-left">CAT @{{bomcategory.category_id}} - @{{bomcategory.bomcategory_name}}</th>
                    </tr>

                    <tr ng-repeat="bomcomponent in bomcomponents" ng-repeat-end ng-if="bomcomponent.bompart_bomcategory_id == bomcategory.bomcategory_id">
                        <th class="col-md-1 text-left">
                            @{{bomcomponent.bomcomponent_name}}
                        </th>
                        <td class="col-md-1" ng-repeat="bomvending in bomvendings[$index]">
                            <div style="padding: 0px 0px 0px 0px" ng-style="{'color': bomvending.color}">
                                @{{bomvending.part}}
                            </div>
                            <div style="padding: 0px 0px 0px 0px">
                                <select ui-select2 name="chosen[bomvending.vending_id]" ng-model="form.part_id" ng-change="onOtherPartChoosen(bomvending.vending_id, form.part_id)" data-placeholder="Select..">
                                    <option value=""></option>
                                    <option ng-repeat="part in bomvending.choices" value="@{{part.id}}">
                                        @{{part.name}}
                                    </option>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered" ng-if="!bomcategories || bomcategories.length == 0">
                    <tr>
                        <td colspan="18" style="background-color: #a3a3c2;" class="text-center">No Records Found</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>