
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
                            <br>进展状况
                        </th>
                        <th class="text-center">
                            Description
                            <br>维修项目
                        </th>
                        <th class="text-center">
                            Photo/Video
                            <br>(Before) 维修前
                        </th>
                        <th class="text-center">
                            Photo/Video
                            <br>(After) 维修后
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
                            <textarea name="desc" rows="4" class="form-control" ng-model='service.desc' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{debounce: 500}" ng-if="service.id" style="min-width: 130px;"></textarea>
                            <textarea name="desc" rows="1" class="form-control" ng-model='service.desc' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{updateOn:'default change blur',debounce:{default:8000,blur:0,change:0}}" ng-if="!service.id"></textarea>
                            <div>
                                <span ng-if="service.attachments" ng-repeat="attachment in service.attachments">
                                    <a href="#" ng-click="onAttachmentModalClicked(service, false, true)" data-toggle="modal" data-target="#attachment-modal" ng-if="attachment.is_title">
                                        <div ng-switch="attachment.full_url.split('.').pop().toLowerCase()">
                                            <embed ng-src="@{{attachment.full_url | trusted}}" type="application/pdf" style="min-height:300px; max-height:500px;" ng-switch-when="pdf">
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mp4">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mov">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <img src="@{{attachment.full_url}}" class="center-block" alt="@{{attachment.full_url}}" style="max-height:300px; max-width:300px;" ng-switch-default>
                                        </div>
                                    </a>
                                </span>
                                <span ng-if="service.id">
                                    <input type="file" ng-files="setAttachment($files, service.id, true, 1)" id="attachment"  class="form-control">
                                </span>
                            </div>
                        </td>
                        <td class="col-md-3 text-center">
                            <textarea name="desc" rows="4" class="form-control" ng-model='service.desc1' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{debounce: 500}" ng-if="service.id" style="min-width: 130px;"></textarea>
                            <div>
                                <span ng-if="service.attachments" ng-repeat="attachment in service.attachments">
                                    <a href="#" ng-click="onAttachmentModalClicked(service, true, false)" data-toggle="modal" data-target="#attachment-modal" ng-if="attachment.is_primary && !attachment.is_title">
                                        <div ng-switch="attachment.full_url.split('.').pop().toLowerCase()">
                                            <embed ng-src="@{{attachment.full_url | trusted}}" type="application/pdf" style="min-height:300px; max-height:500px;" ng-switch-when="pdf">
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mp4">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mov">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <img src="@{{attachment.full_url}}" class="center-block" alt="@{{attachment.full_url}}" style="max-height:300px; max-width:300px;" ng-switch-default>
                                        </div>
                                    </a>
                                </span>
                                <span ng-if="service.id">
                                    <input type="file" ng-files="setAttachment($files, service.id, true, 2)" id="attachment"  class="form-control">
                                </span>
                            </div>
                        </td>
                        <td class="col-md-3 text-center">
                            <textarea name="desc" rows="4" class="form-control" ng-model='service.desc2' ng-change="onServiceDescChanged(serviceKey)" ng-model-options="{debounce: 500}" ng-if="service.id" style="min-width: 130px;"></textarea>
                            <div>
                                <span ng-if="service.attachments" ng-repeat="attachment in service.attachments">
                                    <a href="#" ng-click="onAttachmentModalClicked(service, false, false)" data-toggle="modal" data-target="#attachment-modal" ng-if="!attachment.is_primary && !attachment.is_title">
                                        <div ng-switch="attachment.full_url.split('.').pop().toLowerCase()">
                                            <embed ng-src="@{{attachment.full_url | trusted}}" type="application/pdf" style="min-height:300px; max-height:500px;" ng-switch-when="pdf">
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mp4">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mov">
                                                <video class=" embed-responsive-item video-js" controls>
                                                    <source ng-src="@{{attachment.full_url | trusted}}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                            <img src="@{{attachment.full_url}}" class="center-block" alt="@{{attachment.full_url}}" style="max-height:300px; max-width:300px;" ng-switch-default>
                                        </div>
                                    </a>
                                </span>
                                <span ng-if="service.id">
                                    <input type="file" ng-files="setAttachment($files, service.id, true, 3)" id="attachment"  class="form-control">
                                </span>
                            </div>
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
                            <th class="text-center" ng-if="!isTitle">
                                @{{ attachmentType ? 'Before' : 'After'}}
                            </th>
                            <th class="text-center" ng-if="isTitle">
                                Desc
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <input type="file" ng-files="setAttachment($files, service.id, true, 1)" id="attachment"  class="form-control" ng-if="isTitle">
                                <input type="file" ng-files="setAttachment($files, service.id, true, 2)" id="attachment"  class="form-control" ng-if="attachmentType && !isTitle">
                                <input type="file" ng-files="setAttachment($files, service.id, true, 3)" id="attachment"  class="form-control" ng-if="!attachmentType && !isTitle">
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-list-search table-hover table-bordered">
                        <tr ng-repeat="attachment in service.attachments">
                            <td class="text-center" ng-if="isTitle ? attachment.is_title == isTitle : (attachment.is_primary == attachmentType && !attachment.is_title)">
                                {{-- <img src="@{{attachment.full_url}}" alt="@{{attachment.full_url}}" class="img-responsive" ng-if="attachment.is_primary == attachmentType"> --}}
                                <div ng-switch="attachment.full_url.split('.').pop().toLowerCase()">
                                    <embed ng-src="@{{attachment.full_url | trusted}}" type="application/pdf" style="min-height:400px; max-height:800px;" ng-switch-when="pdf" ng-if="isTitle ? attachment.is_title == isTitle : (attachment.is_primary == attachmentType && !attachment.is_title)">
                                    <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mp4" ng-if="isTitle ? attachment.is_title == isTitle : (attachment.is_primary == attachmentType && !attachment.is_title)">
                                        <video class=" embed-responsive-item" controls>
                                            <source ng-src="@{{attachment.full_url | trusted}}">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    <div class="embed-responsive embed-responsive-16by9" ng-switch-when="mov" ng-if="isTitle ? attachment.is_title == isTitle : (attachment.is_primary == attachmentType && !attachment.is_title)">
                                        <video class=" embed-responsive-item video-js" autoplay>
                                            <source ng-src="@{{attachment.full_url | trusted}}">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    <img src="@{{attachment.full_url}}" class="center-block img-responsive" alt="@{{attachment.full_url}}"  ng-switch-default ng-if="isTitle ? attachment.is_title == isTitle : (attachment.is_primary == attachmentType && !attachment.is_title)">
                                </div>

                                <div ng-if="isTitle && attachment.is_title == isTitle">
                                    <a href="@{{attachment.full_url}}" download="@{{attachment.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                    <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, service.id, attachment.id)" ><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                <div ng-if="(attachment.is_primary == attachmentType) && (attachment.is_primary == true) && !attachment.is_title && !isTitle">
                                    <a href="@{{attachment.full_url}}" download="@{{attachment.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                    <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, service.id, attachment.id)" ><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                <div ng-if="(attachment.is_primary == attachmentType) && (attachment.is_primary == false) && !attachment.is_title  && !isTitle">
                                    <a href="@{{attachment.full_url}}" download="@{{attachment.url}}" class="btn btn-sm btn-info btn-block"><i class="fa fa-download"></i> Download</a>
                                    <a href="" class="btn btn-sm btn-danger btn-block" ng-confirm-click="Are you sure to delete?" confirmed-click="removeAttachment($event, service.id, attachment.id)" ><i class="fa fa-trash"></i> Delete</a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
