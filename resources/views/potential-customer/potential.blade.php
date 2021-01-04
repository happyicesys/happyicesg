<div class="panel panel-default" ng-cloak>
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-right">
              <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#potential-customer-modal" ng-click="onAddPotentialCustomerTemplateButtonClicked()">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Potential Customer
              </button>
            </div>
        </div>
    </div>

    <div class="panel-body">
      <div class="row">
          <div class="form-group col-md-2 col-sm-4 col-xs-12">
              {!! Form::label('custcategory', 'Cust Category', ['class'=>'control-label search-title']) !!}
              <select name="custcategory" class="selectmultiple form-control" ng-model="search.custcategory" ng-change="searchDB()" multiple>
                  <option value="">All</option>
                  @foreach($custcategories::orderBy('name')->get() as $custcategory)
                  <option value="{{$custcategory->id}}">{{$custcategory->name}}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group col-md-2 col-sm-4 col-xs-12">
              {!! Form::label('name', 'Name', ['class'=>'control-label search-title']) !!}
              {!! Form::text('name', null,
                                              [
                                                  'class'=>'form-control input-sm',
                                                  'ng-model'=>'search.name',
                                                  'placeholder'=>'Name',
                                                  'ng-change'=>'searchDB()',
                                                  'ng-model-options'=>'{ debounce: 500 }'
                                              ])
              !!}
          </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('account_manager', 'Account Manager', ['class'=>'control-label']) !!}
                @if(auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                    <select name="account_manager" class="select form-control" ng-model="search.account_manager" ng-change="searchDB()" ng-init="merchandiserInit('{{auth()->user()->id}}')" disabled>
                        <option value="">All</option>
                        @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                        <option value="{{$user->id}}">
                            {{$user->name}}
                        </option>
                        @endforeach
                    </select>
                @else
                    {!! Form::select('account_manager',
                            [''=>'All']+$users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->lists('name', 'id')->all(),
                            null,
                            [
                                'class'=>'select form-control',
                                'ng-model'=>'search.account_manager',
                                'ng-change'=>'searchDB()'
                            ])
                    !!}
                @endif
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('contact', 'Contact', ['class'=>'control-label search-title']) !!}
                {!! Form::text('contact', null,
                                                [
                                                    'class'=>'form-control input-sm',
                                                    'ng-model'=>'search.contact',
                                                    'placeholder'=>'Contact',
                                                    'ng-change'=>'searchDB()',
                                                    'ng-model-options'=>'{ debounce: 500 }'
                                                ])
                !!}
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('created_at', 'Created At', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "created_at"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Created At"
                            ng-model = "search.created_at"
                            ng-change = "dateChange('created_at', search.created_at)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('created_at', search.created_at)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('created_at', search.created_at)"></span>
                </div>
            </div>
            <div class="form-group col-md-2 col-sm-4 col-xs-12">
                {!! Form::label('updated_at', 'Updated At', ['class'=>'control-label search-title']) !!}
                <div class="input-group">
                    <datepicker>
                        <input
                            name = "updated_at"
                            type = "text"
                            class = "form-control input-sm"
                            placeholder = "Updated At"
                            ng-model = "search.updated_at"
                            ng-change = "dateChange('updated_at', search.updated_at)"
                        />
                    </datepicker>
                    <span class="input-group-addon fa fa-backward" ng-click="onPrevSingleClicked('updated_at', search.updated_at)"></span>
                    <span class="input-group-addon fa fa-forward" ng-click="onNextSingleClicked('updated_at', search.updated_at)"></span>
                </div>
            </div>
      </div>

            <div class="row" style="padding-top: 20px;">
                <div class="col-md-4 col-xs-12">
                    {{-- <button class="btn btn-sm btn-primary" ng-click="exportData()"><i class="fa fa-file-excel-o"></i><span class="hidden-xs"></span> Export Excel</button> --}}
                </div>
                <div class="col-md-4 col-md-offset-4 col-xs-12 text-right">
                    <div class="row" style="padding-right:18px;">
                        <label>Display</label>
                        <select ng-model="itemsPerPage" name="pageNum" ng-init="itemsPerPage='100'" ng-change="pageNumChanged()">
                            <option ng-value="100">100</option>
                            <option ng-value="200">200</option>
                            <option ng-value="All">All</option>
                        </select>
                        <label>per Page</label>
                    </div>
                    <div class="row">
                        <label class="" style="padding-right:18px;" for="totalnum">Showing @{{alldata.length}} of @{{totalCount}} entries</label>
                    </div>
                </div>
            </div>
        <hr>

        <div class="table-responsive" id="exportable" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        #
                    </th>
                    <th class="col-md-2 text-center">
                        <a href="" ng-click="sortTable('name')">
                        Name
                        <span ng-if="search.sortName == 'name' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'name' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-2 text-center">
                        Progress
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('custcategory_id')">
                        Cust Category
                        <span ng-if="search.sortName == 'custcategory_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'custcategory_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('account_manager_id')">
                        Acc Manager
                        <span ng-if="search.sortName == 'account_manager_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'account_manager_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        Attn To
                    </th>
                    <th class="col-md-1 text-center">
                        Contact
                    </th>
                    <th class="col-md-1 text-center">
                        Address
                    </th>
                    <th class="col-md-1 text-center">
                        Postcode
                    </th>
                    <th class="col-md-1 text-center">
                        Remarks
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('created_at')">
                        Created By
                        <span ng-if="search.sortName == 'created_at' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'created_at' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('updated_at')">
                        Updated By
                        <span ng-if="search.sortName == 'updated_at' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'updated_at' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center"></th>
                </tr>

                <tbody>
                    <tr dir-paginate="data in alldata | itemsPerPage:itemsPerPage | orderBy:sortType:sortReverse" total-items="totalCount" current-page="currentPage">
                        <td class="col-md-1 text-center">
                            @{{ $index + indexFrom }}
                        </td>
                        <td class="col-md-2 text-center">
                            @{{data.name}}
                        </td>
                        <td class="col-md-2 text-left" style="min-width: 110px;">
                            <ul style="margin-left: 0px; padding-left: 5px; font-size: 12px;">
                                <li ng-if="data.is_seventh">
                                    Need follow-up
                                </li>
                                <li ng-if="data.is_first">
                                    Sample given
                                </li>
                                <li ng-if="data.is_second">
                                    Meet Boss
                                </li>
                                <li ng-if="data.is_third">
                                    First try boss reject
                                </li>
                                <li ng-if="data.is_fourth">
                                    Approved
                                </li>
                                <li ng-if="data.is_eighth">
                                    In-principle approved
                                </li>
                                <li ng-if="data.is_fifth">
                                    2nd try
                                </li>
                                <li ng-if="data.is_sixth">
                                    3rd try
                                </li>
                            </ul>
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.custcategory.name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.account_manager.name}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.attn_to}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.contact}}
                        </td>
                        <td class="col-md-2 text-left" style="word-wrap: break-word;min-width: 160px;max-width: 300px;white-space: normal;">
                            @{{data.address}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.postcode}}
                            <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#mapModal" ng-click="onMapClicked(data)"><i class="fa fa-map-o"></i></button>
                        </td>
                        <td class="col-md-2 text-left" style="word-wrap: break-word;min-width: 160px;max-width: 300px;white-space: normal;">
                            @{{data.remarks}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.creator.name}} <br>
                            @{{data.created_at}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{data.updater.name}} <br>
                            @{{data.updated_at}}
                        </td>
                        <td class="col-md-1 text-center">
                            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#potential-customer-modal" ng-click="onSingleEntryEdit(data)">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </button>
{{--
                            <button class="btn btn-danger btn-sm" ng-click="onSingleEntryRemove(data.id)">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button> --}}
                        </td>
                    </tr>
                    <tr ng-if="!alldata || alldata.length == 0">
                        <td colspan="14" class="text-center">No Records Found</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" class="pull-left" on-page-change="pageChanged(newPageNumber)"> </dir-pagination-controls>
        </div>
    </div>
</div>

<div id="potential-customer-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">
                @{{form.id ? 'Edit Potential Customer' : 'New Potential Customer'}}
                <span ng-if="form.id">
                  @{{form.name}}
                </span>
              </h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="name">
                    Name
                </label>
                <label style="color: red;">*</label>
                <input type="text" class="form-control" ng-model="form.name">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" ng-model="form.is_seventh" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        Need follow-up
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_first" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        Sample given
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_second" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        Meet boss
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_third" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        First try boss reject
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_fourth" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        Approved
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_eighth" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        In-principle approved
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_fifth" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        2nd try
                    </span>
                </label>
                <label>
                    <input type="checkbox" ng-model="form.is_sixth" ng-true-value="1" ng-false-value="0">
                    <span style="padding-left: 5px; margin-top: 5px;">
                        3rd try
                    </span>
                </label>
            </div>
            <div class="form-group">
                <label for="custcategory">
                    Custcategory
                </label>
                <select class="select form-control" ng-model="form.custcategory_id">
                    <option value=""></option>
                    @foreach($custcategories::orderBy('name')->get() as $custcategory)
                    <option value="{{$custcategory->id}}">
                        {{$custcategory->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="account_manager">
                    Account Manager
                </label>
                <select class="select form-control" ng-model="form.account_manager_id">
                    <option value=""></option>
                    @foreach($users::where('is_active', 1)->whereIn('type', ['staff', 'admin'])->orderBy('name')->get() as $user)
                    <option value="{{$user->id}}">
                        {{$user->name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="attn_to">
                    Attn To
                </label>
                <input type="text" class="form-control" ng-model="form.attn_to">
            </div>
            <div class="form-group">
                <label for="contact">
                    Contact
                </label>
                <input type="text" class="form-control" ng-model="form.contact">
            </div>
            <div class="form-group">
                <label for="address">
                    Address
                </label>
                <textarea class="form-control" rows="4" ng-model="form.address"></textarea>
            </div>
            <div class="form-group">
                <label for="postcode">
                    Postcode
                </label>
                <input type="text" class="form-control" ng-model="form.postcode">
            </div>
            <div class="form-group">
                <label for="remarks">
                    Remarks
                </label>
                <textarea class="form-control" rows="4" ng-model="form.remarks"></textarea>
            </div>
            {{-- <form action="/api/potential-customer/3/attachment" class="dropzone"></form> --}}

            <div ng-if="form.id" class="form-group">
                {{-- <input type="file" nv-file-select uploader="uploader"/><br/>
                <ul>
                    <li ng-repeat="item in uploader.queue">
                        Name: <span ng-bind="item.file.name"></span><br/>
                        <button ng-click="item.upload()">upload</button>
                    </li>
                </ul> --}}
                {{-- <ng-dropzone class="dropzone" options="attachmentOptions"></ng-dropzone> --}}
                {{-- <ng-dropzone class="dropzone" options="dzOptions" ></ng-dropzone> --}}
                {{-- <div class="panel panel-primary">
                    <div class="panel-heading">
                        Attachment(s)
                    </div>
                    <div class="panel-body">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                <th class="col-md-1 text-center">
                                    #
                                </th>
                                <th class="col-md-10 text-center">
                                    Image
                                </th>
                                <th class="col-md-1 text-center">
                                    Action
                                </th>
                            </tr>

                            <tbody>
                                @unless(count($invattachments)>0)
                                    <td class="text-center" colspan="12">No Records Found</td>
                                @else
                                    @foreach($invattachments as $index => $invattachment)

                                    @php
                                        $ext = pathinfo($invattachment->path, PATHINFO_EXTENSION);
                                    @endphp

                                    <tr>
                                        <td class="col-md-1 text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="col-md-10">
                                            @if($ext == 'pdf')
                                                <embed src="{{$invattachment->path}}" type="application/pdf" style="max-width:350px; max-height:500px;">
                                            @else
                                                <a href="{{$invattachment->path}}">
                                                    <img src="{{$invattachment->path}}" alt="{{$invattachment->name}}" style="max-width:350px; max-height:350px;">
                                                </a>
                                            @endif
                                        </td>
                                        <td class="col-md-1 text-center">
                                            @if(!auth()->user()->hasRole('subfranchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                                <button type="submit" form="remove_file" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> <span class="hidden-xs">Delete</span></button>
                                                @if($ext == 'pdf')
                                                    <a href="{{$invattachment->path}}" class="btn btn-sm btn-info">Download</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endunless
                            </tbody>
                        </table>

                        @if(count($invattachments) > 0)
                            {!! Form::open(['id'=>'remove_file', 'method'=>'DELETE', 'action'=>['TransactionController@removeAttachment', $invattachment->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}
                            {!! Form::close() !!}
                        @endif

                        @if(!auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee') or (auth()->user()->hasRole('hd_user') and $transaction->status == 'Pending') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                        {!! Form::open(['action'=>['TransactionController@addInvoiceAttachment', $transaction->id], 'class'=>'dropzone', 'style'=>'margin-top:20px']) !!}
                        @endif

                        {!! Form::close() !!}
                        <label class="pull-right totalnum" for="totalnum">
                            Total of {{count($invattachments)}} entries
                        </label>
                    </div>
                </div> --}}
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="!form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Submit</button>
              <button type="button" class="btn btn-success" data-dismiss="modal" ng-if="form.id" ng-click="onFormSubmitClicked()" ng-disabled="!form.name">Save</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </div>

  </div>
</div>

<div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Plotted Map</h4>
            </div>
            <div class="modal-body">
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    Dropzone.autoDiscover = false;
    $('.dropzone').dropzone({
        init: function()
        {
            this.on("complete", function()
            {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                // location.reload();
              }
            });
        }

    });
});
</script>