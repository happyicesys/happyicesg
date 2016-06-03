@inject('person', 'App\Person')

@extends('template')
@section('title')
Members
@stop
@section('content')

    <div class="row">
    <a class="title_hyper pull-left" href="/market/member"><h1>Members <i class="fa fa-sitemap"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app" ng-controller="memberController">
    <div class="panel-heading">
        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="active"><a href="#member" role="tab" data-toggle="tab">Members</a></li>
            @if(isset($self))
            <li><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>
            @endif
        </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">
            {{-- first element --}}
            <div class="tab-pane active" id="member">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">

                            <div class="pull-right">
                                <label for="display_num">Display</label>
                                <select ng-model="itemsPerPage" ng-init="itemsPerPage='50'">
                                    <option ng-value="10">10</option>
                                    <option ng-value="30">30</option>
                                    <option ng-value="50">50</option>
                                    <option ng-value="All">All</option>
                                </select>
                                <label for="display_num" style="padding-right: 20px">per Page</label>
                            </div>

                            {!! Form::hidden('user_id', Auth::user()->id, ['class'=>'form-control', 'id'=>'user_id']) !!}
                                <div class="dropdown">
                                <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown">
                                + New Member
                                <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    @if(Auth::user()->hasRole('admin'))
                                    <li><a href="/market/member/create/om">Operation Manager (OM)</a></li>
                                    <li><a href="/market/member/create/oe">Operation Executive (OE)</a></li>
                                    @endif
                                    <li ng-if="person.cust_type != 'AM' && person.cust_type != 'AB'"><a href="/market/member/create/am">Area Manager (AM)</a></li>
                                    <li><a ng-if="person.cust_type != 'AB'" href="/market/member/create/ab">Ambassador (AB)</a></li>
                                </ul>
                                </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('id', 'ID:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('id', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.cust_id', 'placeholder'=>'ID']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('name', 'Name:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.name', 'placeholder'=>'Name']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('contact', 'Contact:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('contact', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.contact', 'placeholder'=>'Contact']) !!}
                            </div>
                            <div class="form-group col-md-2 col-sm-4 col-xs-6">
                                {!! Form::label('parent_name', 'Manager:', ['class'=>'control-label search-title']) !!}
                                {!! Form::text('parent_name', null, ['class'=>'form-control input-sm', 'ng-model'=>'search.parent_name', 'placeholder'=>'Manager']) !!}
                            </div>
                        </div>

                        <div class="row"></div>

                        <div class="table-responsive">
                            <table class="table table-list-search table-hover table-bordered">
                                <tr style="background-color: #DDFDF8">
                                    <th class="col-md-1 text-center">
                                        #
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'cust_id'; sortReverse = !sortReverse">
                                        ID
                                        <span ng-show="sortType == 'cust_id' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'cust_id' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'cust_type'; sortReverse = !sortReverse">
                                        Role
                                        <span ng-show="sortType == 'cust_type' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'cust_type' && sortReverse" class="fa fa-caret-up"></span>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                        <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                    <th class="col-md-2 text-center">
                                        Contact
                                    </th>
                                    <th class="col-md-1 text-center">
                                        Delivery Add
                                    </th>
                                    <th class="col-md-1 text-center">
                                        Manager
                                    </th>
                                    <th class="col-md-1 text-center">
                                        <a href="#" ng-click="sortType = 'active'; sortReverse = !sortReverse">
                                        Active
                                        <span ng-show="sortType == 'active' && !sortReverse" class="fa fa-caret-down"></span>
                                        <span ng-show="sortType == 'active' && sortReverse" class="fa fa-caret-up"></span>
                                        </a>
                                    </th>
                                     <th class="col-md-1 text-center">
                                        Action
                                    </th>
                                </tr>

                                <tbody>

                                    <tr dir-paginate="member in members | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" pagination-id="member" current-page="currentPage" ng-controller="repeatController">
                                        <td class="col-md-1 text-center">@{{ number }} </td>
                                        <td class="col-md-1 text-center">@{{ member.cust_id }}</td>
                                        <td class="col-md-1 text-center">@{{ member.cust_type }}</td>
                                        <td class="col-md-2 text-center">@{{ member.name }}</td>
                                        <td class="col-md-2 text-center">
                                            @{{ member.contact }}
                                            <span ng-show="member.alt_contact.length > 0">
                                            / @{{ member.alt_contact }}
                                            </span>
                                        </td>
                                        <td class="col-md-2">@{{ member.del_address }}</td>
                                        <td class="col-md-1 text-center">@{{ member.parent_name ? member.parent_name : '-'}}</td>
                                        <td class="col-md-1 text-center">@{{ member.active }}</td>
                                        <td class="col-md-1 text-center">
                                            <a href="/market/member/@{{ member.id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                                        </td>
                                    </tr>
                                    <tr ng-show="(members | filter:search).length == 0 || ! members.length">
                                        <td colspan="9" class="text-center">No Records Found</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <dir-pagination-controls pagination-id="member" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                        <label class="pull-right totalnum" ng-if="members" for="totalnum">Showing @{{(members | filter:search).length}} of @{{members.length}} entries</label>
                    </div>
                </div>
            </div>
            {{-- end of first element--}}
            {{-- second element --}}
            @if(isset($self))
            <div class="tab-pane" id="profile">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="pull-right">
                                {!! Form::submit('Edit Profile', ['class'=> 'btn btn-success', 'form'=>'edit_profile']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">

                            {!! Form::model($self, ['id'=>'edit_profile','action'=>['MarketingController@updateSelf', $self->id]]) !!}

                                <div class="row">
                                    @include('market.member.form')
                                </div>

                                <hr size='2'>

                                <div class="form-group">
                                    {!! Form::label('password', 'Password', ['class'=>'control-label']) !!}
                                    {!! Form::password('password', ['class'=>'form-control', 'placeholder'=>'Leave Blank to Use the Same Password']) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('password_confirmation', 'Password Confirmation', ['class'=>'control-label']) !!}
                                    {!! Form::password('password_confirmation', ['class'=>'form-control', 'placeholder'=>'Leave Blank to Use the Same Password']) !!}
                                </div>

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
            @endif
            {{-- end of second element --}}
        </div>
    </div>
</div>

<style>

.dropdown{
    position: absolute;
    z-index : 999;
}

</style>
<script src="/js/member.js"></script>
@stop