@inject('bomcategories', 'App\Bomcategory')
@inject('custcategories', 'App\Custcategory')

<div ng-controller="bomComponentController">
    <div class="panel panel-primary" ng-cloak>
        <div class="panel-body">
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Create Component
                        <span class="badge" style="background-color: #ddd1e7;">&nbsp;</span>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Category Name</label>
                                <select class="select form-control" ng-model="form.category_id">
                                    <option ng-value=""></option>
                                    @foreach($bomcategories::all() as $bomcategory)
                                        <option ng-value="{{$bomcategory->id}}">
                                            {{$bomcategory->category_id}} - {{$bomcategory->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <table class="table table-condensed table-bordered" style="margin-top: 15px;" ng-if="!isFormValid()">
                                    <tr style="background-color: #a3a3c2">
                                        <th class="col-md-1 text-center">
                                            #
                                        </th>
                                        <th class="col-md-4 text-center">
                                            Component Name
                                        </th>
                                        <th class="col-md-7 text-center">
                                            Remark
                                        </th>
                                    </tr>
                                    <tr ng-repeat="(index, formcomponent) in formcomponents">
                                        <td class="col-md-1 text-center">
                                            @{{index + 1}}
                                        </td>
                                        <td class="col-md-4">
                                            <input type="text" name="formcomponent_names[]" ng-model="formcomponent.name" class="form-control">
                                        </td>
                                        <td class="col-md-7">
                                            <textarea name="formcomponent_remarks[]" ng-model="formcomponent.remark" class="form-control" rows="1"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-primary btn-block" ng-click="addRow()" ng-disabled="isFormValid()"><i class="fa fa-plus"></i> Add Row</button>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <button class="btn btn-success btn-block" ng-click="confirmComponents(form.category_id)" ng-disabled="isFormValid()"><i class="fa fa-check"></i> Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: -15px;">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Overwrite Sync Template
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label class="control-label">Customer Category</label>
                                <select class="selectcustcat form-control" ng-model="form.custcategory_id">
                                    <option ng-value=""></option>
                                    <option ng-value="All">All</option>
                                    @foreach($custcategories::orderBy('name', 'asc')->get() as $custcategory)
                                        <option ng-value="{{$custcategory->id}}">
                                            {{$custcategory->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 ">
                                <button class="btn btn-warning btn-block" ng-click="syncCustcat($event, form.custcategory_id)" ng-disabled="!form.custcategory_id"><i class="fa fa-sync"></i> Overwrite & Sync
                                    <span ng-show="spinner"> <i class="fa fa-spinner fa-2x fa-spin"></i></span>
                                    <span ng-show="is_done"> <i class="fa fa-check-circle fa-2x" style="color: green;"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('category_id', 'Category ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('category_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.category_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Category ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('category_name', 'Category Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('category_name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.category_name',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Category Name',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('component_id', 'Component ID', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('component_id', null,
                                                    [
                                                        'class'=>'form-control input-sm',
                                                        'ng-model'=>'search.component_id',
                                                        'ng-change'=>'searchDB()',
                                                        'placeholder'=>'Component ID',
                                                        'ng-model-options'=>'{ debounce: 500 }'
                                                    ]) !!}
                </div>
                <div class="form-group col-md-3 col-sm-6 col-xs-12">
                    {!! Form::label('component_name', 'Component Name', ['class'=>'control-label search-title']) !!}
                    {!! Form::text('component_name', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.component_name',
                                                    'ng-change'=>'searchDB()',
                                                    'placeholder'=>'Component Name',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                    !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <button class="btn btn-primary" ng-click="exportData()"><i class="fa fa-file-excel"></i> Export Excel</button>
                    <button class="btn btn-default" ng-click="formedit = !formedit"><i class="fa fa-edit"></i> Quick Edit</button>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
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

            <div class="table-responsive" id="exportable_bomcomponent" style="padding-top:20px; font-size: 13px;">
                <table ng-repeat="bomcategory in alldata" class="table table-list-search table-hover table-bordered">
                    <tr style="background-color: #DDFDF8">
                        <th colspan="14" class="text-left">
                            @{{bomcategory.category_id}} - @{{bomcategory.name}}
                            <span style="padding-left: 20px;" ng-if="bomcategory.bomcategorycustcat.length > 0">[</span>
                            <span ng-repeat="custcat in bomcategory.bomcategorycustcat">
                                @{{custcat.custcategory.name}}@{{$last ? '' : ', '}}
                            </span>
                            <span ng-if="bomcategory.bomcategorycustcat.length > 0">]</span>
                        </th>
                    </tr>
                    <tr style="background-color: #DDFDF8">
                        <th class="col-md-1 text-center" style="width:3%">
                            #
                        </th>
                        <th class="col-md-1 text-center" style="width:10%">
                            ID
                        </th>
                        <th class="col-md-2 text-center">
                            Name
                        </th>
                        <th class="col-md-1 text-center">
                            Drawing #
                        </th>
                        <th class="col-md-2 text-center">
                            Remarks
                        </th>
                        <th class="col-md-1 text-center" style="width:6%">
                            Qty
                        </th>
                        <th class="col-md-2 text-center" style="width:12%">
                            Category Assignment
                        </th>
                        <th class="col-md-1 text-center">
                            Updated By
                        </th>
                        <th class="col-md-1"></th>
                    </tr>
                    <tbody>
                        <tr ng-repeat-start="(index, bomcomponent) in bomcategory.bomcomponents" style="background-color: #ddd1e7;">
                            <td class="col-md-1 text-left" style="width:3%">
                                @{{ index + indexFrom }}
                            </td>
                            <td class="col-md-1 text-center" style="width:10%">
                                @{{bomcomponent.component_id}}
                            </td>
                            <td class="col-md-2 text-left">
                                <a href="#" data-toggle="modal" data-target="#component_modal" ng-click="editComponentModal(bomcomponent)">
                                    @{{bomcomponent.name}}
                                </a>
                            </td>
                            <td class="col-md-1 text-center" data-toggle="modal" data-target="#component_drawing_modal" ng-click="editComponentModal(bomcomponent)" style="cursor: pointer;">
                                <a href="">
                                    @{{bomcomponent.drawing_id}}
                                </a>
                            </td>
                            <td class="col-md-2 text-left">
                                <textarea class="form-control " ng-model="bomcomponent.remark" rows="2" ng-change="onBomcomponentRemarkChanged(bomcomponent.id, bomcomponent.remark)" ng-model-options='{ debounce: 700 }' ng-if="formedit"></textarea>
                                <span ng-if="!formedit">@{{bomcomponent.remark}}</span>
                            </td>
                            <td class="col-md-1 text-right" style="width:6%">
                                <input type="text" name="bomcomponent_qty[]" ng-model="bomcomponent.qty" class="form-control text-right input-sm" ng-change="onBomcomponentQtyChanged(bomcomponent.id, bomcomponent.qty)" ng-model-options='{ debounce: 700 }' ng-if="formedit">
                                <span ng-if="!formedit">@{{bomcomponent.qty}}</span>
                            </td>
                            <td class="col-md-2 text-center" style="width:12%">
                                <span ng-repeat="custcat in bomcomponent.bomcomponentcustcat">@{{custcat.custcategory.name}}@{{$last ? '' : ', '}}</span>
                                <span ng-repeat="custcategory in bomcategory.bomcategorycustcat">@{{custcategory.name}}</span>
                                <ui-select ng-model="custcategory[bomcomponent.id]" on-select="onBomcomponentCustcatChosen(bomcomponent.id, custcategory[bomcomponent.id])" ng-if="formedit">
                                    <ui-select-match>@{{$select.custcategory.name}}</ui-select-match>
                                    <ui-select-choices repeat="custcategory in bomcategory.bomcategorycustcat | filter: $select.search">
                                        <div ng-bind-html="custcategory.custcategory.name | highlight: $select.search"></div>
                                    </ui-select-choices>
                                </ui-select>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bomcomponent.updater.name}} <br>
                                @{{bomcomponent.updated_at | date:'yy/MM/dd h:mma'}}
                            </td>
                            <td class="col-md-1 text-center">
                                <button class="btn btn-danger btn-xs" ng-click="removeEntry(bomcomponent.id)"><i class="fa fa-times"></i></button>
                                <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#part_modal" ng-click="passDataModal(bomcomponent, 0)"><i class="fa fa-plus"></i> Part</button>
                                <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#part_modal" ng-click="passDataModal(bomcomponent, 1)"><i class="fa fa-plus"></i> Consumable</button>
                            </td>
                        </tr>
                        <tr ng-repeat="(index2, bompart) in bomcomponent.bomparts" ng-repeat-end ng-style="{'background-color': bompart.movable == 1 ? '#fbfafc' : '#eae3f0'}">
                            <td class="col-md-1 text-left" style="width:3%">
                                @{{index + indexFrom}}.@{{index2 + 1}}
                            </td>
                            <td class="col-md-1 text-center" style="width:10%">
                                @{{bompart.part_id}}
                            </td>
                            <td class="col-md-2 text-left">
                                <a href="#" data-toggle="modal" data-target="#part_modal" ng-click="editDataModal(bompart)">
                                    @{{bompart.name}}
                                </a>
                            </td>
                            <td class="col-md-1 text-center" data-toggle="modal" data-target="#part_drawing_modal" ng-click="editDataModal(bompart)" style="cursor: pointer;">
                                <a href="">
                                    @{{bompart.drawing_id}}
                                </a>
                            </td>
                            <td class="col-md-2 text-left">
                                <textarea class="form-control " ng-model="bompart.remark" rows="2" ng-change="onRemarkChanged(bompart.id, bompart.remark)" ng-model-options='{ debounce: 700 }' ng-if="formedit"></textarea>
                                <span ng-if="!formedit">@{{bompart.remark}}</span>
                            </td>
                            <td class="col-md-1 text-right" style="width:6%">
                                <input type="text" name="bompart_qty[]" ng-model="bompart.qty" class="form-control text-right input-sm" ng-change="onQtyChanged(bompart.id, bompart.qty)" ng-model-options='{ debounce: 700 }' ng-if="formedit">
                                <span ng-if="!formedit">@{{bompart.qty}}</span>
                            </td>
                            <td class="col-md-2 text-center" style="width:12%">
                                <span ng-repeat="bomtemplate in bompart.bomtemplates">@{{bomtemplate.custcategory.name}}@{{$last ? '' : ', '}}</span>
                                <ui-select ng-model="custcategory[bompart.id]" on-select="onCustcatChosen(bompart.id, custcategory[bompart.id])" ng-if="formedit">
                                    <ui-select-match>@{{$select.custcategory.name}}</ui-select-match>
                                    <ui-select-choices repeat="custcategory in bomcomponent.bomcomponentcustcat | filter: $select.search">
                                        <div ng-bind-html="custcategory.custcategory.name | highlight: $select.search"></div>
                                    </ui-select-choices>
                                </ui-select>
                            </td>
                            <td class="col-md-1 text-center">
                                @{{bompart.updater.name}} <br>
                                @{{bompart.updated_at | date:'yy/MM/dd h:mma'}}
                            </td>
                            <td class="col-md-1 text-center">
                                <button class="btn btn-danger btn-xs" ng-click="removeBompart(bompart.id)"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                        <tr ng-if="!alldata || alldata.length == 0">
                            <td colspan="18" class="text-center">No Records Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
            </div>
        </div>
    </div>

    <div class="modal fade" id="part_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">
                    <span ng-style="{'background-color': partform.color}">
                        @{{partform.type}}
                    </span>
                    <span ng-if="partform.component_id">for
                        <span style="background-color: #ddd1e7;">
                            @{{partform.component_id}} - @{{partform.title}}
                        </span>
                    </span>
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Part ID
                            </label>
                            <input type="text" name="part_id" class="form-control" ng-model="partform.part_id" ng-change="onPartIdChanged(partform.part_id)" ng-model-options='{ debounce: 500 }'>
{{--                             <span ng-if="formErrors['part_id']" class="help-block" style="color:red;">
                              <ul class="row">
                                  <li style="color:red;">@{{ formErrors['part_id'][0] }}</li>
                              </ul>
                            </span> --}}
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Name
                            </label>
                            <input type="text" name="name" class="form-control" ng-model="partform.name">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Qty
                            </label>
                            <input type="text" name="qty" class="form-control" ng-model="partform.qty">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Remark
                            </label>
                            <textarea name="remark" class="form-control" ng-model="partform.remark" rows="2"></textarea>
                        </div>
                        <hr>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Drawing #
                            </label>
                            <input type="text" name="drawing_id" class="form-control" ng-model="partform.drawing_id">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Supplier Order & Detail
                            </label>
                            <textarea name="supplier_order" class="form-control" ng-model="partform.supplier_order" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Unit Price (S$)
                            </label>
                            <input type="text" name="unit_price" class="form-control" ng-model="partform.unit_price">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Person In Charge
                            </label>
                            <input type="text" name="pic" class="form-control" ng-model="partform.pic">
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="files">Upload Drawing</label>
                                <input type="file" name="files" id="files" ng-files="setTheBompartFiles($files)" id="part_file" class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" ng-click="uploadBompartFile(partform.id)"><i class="fa fa-upload"></i> Upload File</button>
                                <button class="btn btn-danger" ng-click="deleteBompartDrawing(partform.id)"><i class="fa fa-times"></i> Remove File</button>
                            </div>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <a ng-href="@{{partform.drawing_path}}" ng-if="partform.drawing_path">
                                <img ng-src="@{{partform.drawing_path}}" height="250" width="250" style="border:2px solid black"
                                ng-if="partform.drawing_path &&
                                        (
                                            partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'jpeg' ||
                                            partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'jpg' ||
                                            partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'png'
                                        )">
                                <embed ng-src="@{{partform.drawing_path}}" height="250" width="250" style="border:2px solid black"
                                ng-if="partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'pdf'">
                            </a>
                            <img src="#" alt="No photo found" ng-if="!partform.drawing_path" height="250" width="250" style="border:2px solid black">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-if="!partform.id"  ng-click="createPart()" data-dismiss="modal">Create</button>
                    <button type="button" class="btn btn-success" ng-if="partform.id" ng-click="editPart()" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="component_modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">
                    Edit Component
                </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Component ID
                            </label>
                            <input type="text" name="part_id" class="form-control" ng-model="componentform.component_id">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Name
                            </label>
                            <input type="text" name="name" class="form-control" ng-model="componentform.name">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Qty
                            </label>
                            <input type="text" name="qty" class="form-control" ng-model="componentform.qty">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Remark
                            </label>
                            <textarea name="remark" class="form-control" ng-model="componentform.remark" rows="2"></textarea>
                        </div>
                        <hr>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Drawing #
                            </label>
                            <input type="text" name="drawing_id" class="form-control" ng-model="componentform.drawing_id">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Supplier Order & Detail
                            </label>
                            <textarea name="supplier_order" class="form-control" ng-model="componentform.supplier_order" rows="3"></textarea>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Unit Price (S$)
                            </label>
                            <input type="text" name="unit_price" class="form-control" ng-model="componentform.unit_price">
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Person In Charge
                            </label>
                            <input type="text" name="pic" class="form-control" ng-model="componentform.pic">
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group">
                                <label for="files">Upload Drawing</label>
                                <input type="file" name="files" id="files" ng-files="setTheBomcomponentFiles($files)" id="component_file" class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success" ng-click="uploadBomcomponentFile(componentform.id)"><i class="fa fa-upload"></i> Upload File</button>
                                <button class="btn btn-danger" ng-click="deleteBomcomponentDrawing(componentform.id)"><i class="fa fa-times"></i> Remove File</button>
                            </div>
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <a ng-href="@{{componentform.drawing_path}}" ng-if="componentform.drawing_path">
                                <img ng-src="@{{componentform.drawing_path}}" height="250" width="250" style="border:2px solid black"
                                ng-if="componentform.drawing_path &&
                                        (
                                            componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'jpeg' ||
                                            componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'jpg' ||
                                            componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'png'
                                        )">
                                <embed ng-src="@{{componentform.drawing_path}}" height="250" width="250" style="border:2px solid black"
                                ng-if="componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'pdf'">
                            </a>
                            <img src="#" alt="No photo found" ng-if="!componentform.drawing_path" height="250" width="250" style="border:2px solid black">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" ng-click="editComponent()" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="component_drawing_modal" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    &nbsp;
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <img ng-src="@{{componentform.drawing_path}}" height="600" width="550" style="border:2px solid black"
                    ng-if="componentform.drawing_path &&
                            (
                                componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'jpeg' ||
                                componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'jpg' ||
                                componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'png'
                            )">
                    <embed ng-src="@{{componentform.drawing_path}}" height="600" width="550" style="border:2px solid black"
                    ng-if="componentform.drawing_path.substr(componentform.drawing_path.lastIndexOf('.') + 1) == 'pdf'">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="part_drawing_modal" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    &nbsp;
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <img ng-src="@{{partform.drawing_path}}" height="600" width="550" style="border:2px solid black"
                    ng-if="partform.drawing_path &&
                            (
                                partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'jpeg' ||
                                partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'jpg' ||
                                partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'png'
                            )">
                    <embed ng-src="@{{partform.drawing_path}}" height="600" width="550" style="border:2px solid black"
                    ng-if="partform.drawing_path.substr(partform.drawing_path.lastIndexOf('.') + 1) == 'pdf'">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>