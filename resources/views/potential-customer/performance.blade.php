<div class="panel panel-default" ng-cloak>
    <div class="panel-body">
      <div class="row">
            <div class="form-group col-md-3 col-sm-4 col-xs-12">
                <div class="form-group">
                    {!! Form::label('current_month', 'Current Month', ['class'=>'control-label search-title']) !!}
                    <select class="select form-control" name="current_month" ng-model="search.current_month" ng-change="searchDB()">
                        <option value="">All</option>
                        @foreach($monthOptions as $key => $value)
                            <option value="{{$key}}" selected="{{Carbon\Carbon::today()->month.'-'.Carbon\Carbon::today()->year ? 'selected' : ''}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-3 col-sm-4 col-xs-12">
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
      </div>

        <hr>

        <div class="table-responsive" id="exportable" style="padding-top: 20px;">
            <table class="table table-list-search table-hover table-bordered" style="font-size: 14px;">
                <tr style="background-color: #DDFDF8">
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('date')">
                        Date
                        <span ng-if="search.sortName == 'date' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'date' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        <a href="" ng-click="sortTable('account_manager_id')">
                        Acc Manager
                        <span ng-if="search.sortName == 'account_manager_id' && !search.sortBy" class="fa fa-caret-down"></span>
                        <span ng-if="search.sortName == 'account_manager_id' && search.sortBy" class="fa fa-caret-up"></span>
                    </th>
                    <th class="col-md-1 text-center">
                        {{-- <a href="" ng-click="sortTable('created')"> --}}
                        # Created
                        {{-- <span ng-if="search.sortName == 'created' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                        {{-- <span ng-if="search.sortName == 'created' && search.sortBy" class="fa fa-caret-up"></span> --}}
                    </th>
                    <th class="col-md-1 text-center">
                        {{-- <a href="" ng-click="sortTable('updated')"> --}}
                        # Updated
                        {{-- <span ng-if="search.sortName == 'updated' && !search.sortBy" class="fa fa-caret-down"></span> --}}
                        {{-- <span ng-if="search.sortName == 'updated' && search.sortBy" class="fa fa-caret-up"></span> --}}
                    </th>
                </tr>
                <tr>
                    <th colspan="2" class="col-md-1 text-center">
                        Total
                    </th>
                    <th class="col-md-1 text-center">
                        @{{data.createdTotal}}
                    </th>
                    <th class="col-md-1 text-right">
                        @{{data.updatedTotal}}
                    </th>
                </tr>

                <tbody ng-repeat="date in data.dates" ng-style="{'background-color': ($index%2==0) ? '#F1F1F1' : ''}">
                    <tr ng-repeat="manager in date">
                        <td class="col-md-1 text-center" style="font-size: 11px;">
                            @{{manager.date}} <br>
                            @{{manager.day}}
                        </td>
                        <td class="col-md-1 text-center">
                            @{{manager.account_manager_name}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{manager.created}}
                        </td>
                        <td class="col-md-1 text-right">
                            @{{manager.updated}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>