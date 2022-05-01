
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Create Service : {{$person->cust_id}} - {{$person->company}}</strong></h3>
            </div>
            <div class="pull-right">
                <button class="btn btn-success" ng-click="onNewServiceClicked($event)" data-toggle="modal" data-target="#form-service-modal">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    New Service Job
                </button>
{{--
                <a href="/transaction/{{$transaction->id}}/service/create" class="btn btn-success">
                  <i class="fa fa-plus" aria-hidden="true"></i>
                  New Service Job
                </a> --}}
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div>
            <div class="table-responsive">
                <table class="table table-list-search table-hover table-bordered table-condensed">
                    <tr style="background-color: #DDFDF8;">
                        <th class="text-center">
                            #
                        </th>
                        <th class="text-center">
                            Job Desc
                        </th>
                        <th class="text-center">
                            Before
                        </th>
                        <th class="text-center">
                            After
                        </th>
                        <th></th>
                    </tr>
                    <tr dir-paginate="service in services | itemsPerPage:itemsPerPage" pagination-id="formService" total-items="servicesTotal" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index +1 }}
                            <br>
                            <span class="badge badge-info" ng-if="service.status == 1">
                                New
                            </span>
                            <span class="badge badge-success" style="background-color: green;" ng-if="service.status == 2">
                                Completed
                            </span>
                            <span class="badge badge-danger" style="background-color: red;" ng-if="service.status == 99">
                                Cancelled
                            </span>
                        </td>
                        <td class="col-md-3 text-left">
                            @{{ service.desc }}
                        </td>
                        <td class="col-md-3 text-center">
                            <span ng-if="service.attachment1">
                                <a href="@{{service.attachment1.url}}">
                                    <img src="@{{service.attachment1.url}}" alt="@{{service.attachment1.url}}" style="width:200px; height:200px;">
                                </a>
                            </span>
                        </td>
                        <td class="col-md-3 text-center">
                            <span ng-if="service.attachment2">
                                <a href="@{{service.attachment2.url}}">
                                    <img src="@{{service.attachment2.url}}" alt="@{{service.attachment2.url}}" style="width:200px; height:200px;">
                                </a>
                            </span>
                        </td>
                        <td class="col-md-1 text-center">
                            <div class="btn-group hidden-xs">
                                <button class="btn btn-default btn-sm" ng-click="editService($event, service)" data-toggle="modal" data-target="#form-service-modal">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                                <a href="" class="btn btn-sm btn-success" ng-click="completeService($event, service.id)">
                                    <i class="fa fa-check"></i>
                                </a>
                                <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to cancel?" confirmed-click="cancelService($event, service.id)">
                                    <i class="fa fa-times"></i>
                                </a>
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('supervisor'))
                                    <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="deleteService($event, service.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="visible-xs">
                                <button class="btn btn-default btn-sm btn-block" ng-click="editService($event, service)" data-toggle="modal" data-target="#form-service-modal">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                                <a href="" class="btn btn-sm btn-success btn-block" ng-click="completeService($event, service.id)">
                                    <i class="fa fa-check"></i>
                                </a>
                                <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to cancel?" confirmed-click="cancelService($event, service.id)">
                                    <i class="fa fa-times"></i>
                                </a>
                                @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('supervisor'))
                                    <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="deleteService($event, service.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr ng-if="!services || services.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="form-service-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                  @{{formService.id ? 'Edit Service' : 'New Service'}} for
                  #{{$transaction->id}} <br> ({{$transaction->person->cust_id}} - {{$transaction->person->company}})
                </h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="template-name">
                  Service Desc
                </label>
                <label style="color: red;">*</label>
                <textarea class="form-control" ng-model="formService.desc" rows="6"></textarea>
              </div>

                <div class="hidden-xs">
                    <div class="table-responsive">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-6 text-center">
                                    Before
                                </th>
                                <th class="col-md-6 text-center">
                                    After
                                </th>
                            </tr>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="file" ng-files="setAttachment1($files)" id="attachment1"  class="form-control">
                                    </td>
                                    <td>
                                        <input type="file" ng-files="setAttachment2($files)" id="attachment2"  class="form-control">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment1 || formService.attachment2">
                                    <td class="text-center">
                                        <img src="@{{formService.attachment1.url}}" alt="@{{formService.attachment1.url}}" style="width:200px; height:200px;" ng-if="formService.attachment1">
                                    </td>
                                    <td class="text-center">
                                        <img src="@{{formService.attachment2.url}}" alt="@{{formService.attachment2.url}}" style="width:200px; height:200px;" ng-if="formService.attachment2">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment1 || formService.attachment2">
                                    <td class="text-center">
                                        <div class="btn-group" ng-if="formService.attachment1">
                                            <a href="@{{formService.attachment1.url}}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> <span class="hidden-xs">Download</span></a>
                                            <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, formService.id, formService.attachment1.id)"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" ng-if="formService.attachment2">
                                            <a href="@{{formService.attachment2.url}}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> <span class="hidden-xs">Download</span></a>
                                            <a href="" class="btn btn-sm btn-danger" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, formService.id, formService.attachment2.id)"><i class="fa fa-trash"></i> <span class="hidden-xs">Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="visible-xs">
                    <div class="table-responsive">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="text-center">
                                    Before
                                </th>
                            </tr>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="file" ng-files="setAttachment1($files)" id="attachment1"  class="form-control">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment1">
                                    <td class="text-center">
                                        <img src="@{{formService.attachment1.url}}" alt="@{{formService.attachment1.url}}" class="img-responsive" ng-if="formService.attachment1">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment1">
                                    <td class="text-center">
                                        <div ng-if="formService.attachment1">
                                            <a href="@{{formService.attachment1.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                            <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, formService.id, formService.attachment1.id)" ><i class="fa fa-trash"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive" style="padding-top:10px;">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="text-center">
                                    After
                                </th>
                            </tr>

                            <tbody>
                                <tr>
                                    <td>
                                        <input type="file" ng-files="setAttachment2($files)" id="attachment2"  class="form-control">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment2">
                                    <td class="text-center">
                                        <img src="@{{formService.attachment2.url}}" alt="@{{formService.attachment2.url}}" class="img-responsive" ng-if="formService.attachment2">
                                    </td>
                                </tr>
                                <tr ng-if="formService.attachment2">
                                    <td class="text-center">
                                        <div ng-if="formService.attachment2">
                                            <a href="@{{formService.attachment2.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                            <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, formService.id, formService.attachment2.id)"><i class="fa fa-trash"></i> Delete</span></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-group hidden-xs">
                  <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="!formService.id" ng-click="onServiceSubmitClicked($event, {{$transaction->id}})" ng-disabled="!form.name">Create</button>
                  <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="formService.id" ng-click="onServiceUpdated($event, formService.id)" ng-disabled="!form.name">Save</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                <div class="visible-xs">
                    <button type="button" class="btn btn-success btn-block" data-dismiss="modal" ng-if="!formService.id" ng-click="onServiceSubmitClicked($event, {{$transaction->id}})" ng-disabled="!form.name">Create</button>
                    <button type="button" class="btn btn-success btn-block" data-dismiss="modal" ng-if="formService.id" ng-click="onServiceUpdated($event, formService.id)" ng-disabled="!form.name">Save</button>
                    <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>
                  </div>
            </div>
        </div>
    </div>
</div>