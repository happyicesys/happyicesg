
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left display_panel_title">
                <h3 class="panel-title"><strong>Create Service : {{$person->cust_id}} - {{$person->company}}</strong></h3>
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
                            Description
                        </th>
                        <th class="text-center">
                            Photo (Before)
                        </th>
                        <th class="text-center">
                            Photo (After)
                        </th>
                    </tr>
                    <tr ng-repeat="(serviceKey, service) in services">
                        <td class="col-md-1 text-center">
                            @{{ $index +1 }}
                            <br>
                            <span class="badge badge-info" ng-if="service.status == 1">
                                New
                            </span>
                            <span class="badge badge-warning" style="background-color: orange;" ng-if="service.status == 90">
                                Incompleted
                            </span>
                            <span class="badge badge-success" style="background-color: green;" ng-if="service.status == 2">
                                Completed
                            </span>
                            <span class="badge badge-danger" style="background-color: red;" ng-if="service.status == 99">
                                Cancelled
                            </span>
                            <div style="padding-top: 10px;">
                                <span ng-if="(service.status != 2 || !service.status) && service.id">
                                    <button class="btn btn-success btn-sm btn-block" ng-click="onStatusClicked($event, service.id, 2)">
                                        Done
                                        <br>
                                        完成
                                    </button>
                                </span>
                                <span ng-if="(service.status != 90 || !service.status) && service.id">
                                    <button class="btn btn-warning btn-sm btn-block" ng-click="onStatusClicked($event, service.id, 90)">
                                        Incomplete
                                        <br>
                                        未能完成
                                    </button>
                                </span>
                                <span ng-if="(service.status != 99 || !service.status) && service.id">
                                    <button class="btn btn-danger btn-sm btn-block" ng-click="onStatusClicked($event, service.id, 99)">
                                        Cancelled
                                        <br>
                                        取消
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td class="col-md-3 text-left">
                            <textarea name="desc" rows="9" class="form-control" ng-model='service.desc' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{debounce: 500}" ng-if="service.id"></textarea>
                            <textarea name="desc" rows="1" class="form-control" ng-model='service.desc' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{debounce: 500}" ng-if="!service.id"></textarea>
                        </td>
                        <td class="col-md-3 text-center">
                            <span ng-if="service.attachment1">
                                <a href="#" ng-click="onAttachmentModalClicked(service, service.attachment1.id)" data-toggle="modal" data-target="#attachment-modal">
                                    <img src="@{{service.attachment1.url}}" alt="@{{service.attachment1.url}}" style="width:200px; height:200px;">
                                </a>
                            </span>
                            <span ng-if="!service.attachment1 && service.id">
                                <input type="file" ng-files="setAttachment1($files, service.id)" id="attachment1"  class="form-control">
                            </span>
                        </td>
                        <td class="col-md-3 text-center">
                            <span ng-if="service.attachment2">
                                <a href="#" ng-click="onAttachmentModalClicked(service, service.attachment2.id)" data-toggle="modal" data-target="#attachment-modal">
                                    <img src="@{{service.attachment2.url}}" alt="@{{service.attachment2.url}}" style="width:200px; height:200px;">
                                </a>
                            </span>
                            <span ng-if="!service.attachment2 && service.id">
                                <input type="file" ng-files="setAttachment2($files, service.id)" id="attachment2"  class="form-control">
                            </span>
                        </td>
                    </tr>
                    <tr ng-if="!services || services.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </table>
                <button class="btn btn-success" ng-click="onNewServiceClicked($event)">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    New Service Job
                </button>
            </div>
        </div>
    </div>
</div>

<div id="attachment-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Edit Attachment for @{{service.transaction_id}}
                    <br> ({{$transaction->person->cust_id}} - {{$transaction->person->company}})
                </h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr style="background-color: #DDFDF8">
                            <th class="text-center">
                                @{{attachmentType == 1 ? 'Before' : 'After'}}
                            </th>
                        </tr>

                        <tbody>
                            <tr>
                                <td>
                                    <input type="file" ng-files="setAttachment1($files, service.id, true)" id="attachment1"  class="form-control" ng-if="attachmentType == 1">
                                    <input type="file" ng-files="setAttachment2($files, service.id, true)" id="attachment2"  class="form-control" ng-if="attachmentType == 2">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <img src="@{{service.attachment1.url}}" alt="@{{service.attachment1.url}}" class="img-responsive" ng-if="attachmentType == 1">
                                    <img src="@{{service.attachment2.url}}" alt="@{{service.attachment2.url}}" class="img-responsive" ng-if="attachmentType == 2">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <div ng-if="attachmentType == 1">
                                        <a href="@{{service.attachment1.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                        <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, service.id, service.attachment1.id)" ><i class="fa fa-trash"></i> Delete</a>
                                    </div>
                                    <div ng-if="attachmentType == 2">
                                        <a href="@{{service.attachment2.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                        <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, service.id, service.attachment2.id)" ><i class="fa fa-trash"></i> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
{{--
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
            </div> --}}
        </div>
    </div>
</div>