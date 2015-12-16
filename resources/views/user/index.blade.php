@extends('template')
@section('title')
{{ $USER_TITLE }}
@stop
@section('content')
    
    <div class="row">        
    <a class="title_hyper pull-left" href="/user"><h1>{{ $USER_TITLE }} <i class="fa fa-user"></i></h1></a>
    </div>


<div class="panel panel-warning" ng-app="app">
    <div class="panel-heading">
            <ul class="nav nav-pills nav-justified" role="tablist">

                <li class="active"><a href="#data" role="tab" data-toggle="tab">User Data</a></li>


                <li><a href="#role_draft" role="tab" data-toggle="tab">Access Level</a></li>

                @can('view_permission')
                <li><a href="#permission_list" role="tab" data-toggle="tab">Permission</a></li>
                @endcan

                <li><a href="#profile" role="tab" data-toggle="tab">Company Profile</a></li>
            </ul>
    </div>

    <div class="panel-body">
        <div class="tab-content">

            <div class="tab-pane active" id="data">

                <div ng-controller="userController">

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">

                                <div class="pull-left display_num">
                                    <label for="display_num">Display</label>
                                    <select ng-model="itemsPerPage" ng-init="itemsPerPage='10'">
                                      <option>10</option>
                                      <option>20</option>
                                      <option>30</option>
                                    </select>
                                    <label for="display_num2" style="padding-right: 20px">per Page</label>
                                </div>

                                <div class="pull-right">
                                    @can('create_user')
                                    <a href="/user/create" class="btn btn-success">+ New {{ $USER_TITLE }}</a>
                                    @endcan                                                        
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div style="padding-bottom: 10px">
                                <label for="search_id" class="search">Search ID:</label>
                                <input type="text" ng-model="search.id">
                                <label for="search_name" class="search" style="padding-left: 10px">Name:</label>
                                <input type="text" ng-model="search.name">                    
                                <label for="search_contact" class="search" style="padding-left: 10px">Contact:</label>
                                <input type="text" ng-model="search.contact">

                            </div>
                            <div class="table-responsive">
                                <table class="table table-list-search table-hover table-bordered">
                                    <tr style="background-color: #DDFDF8">
                                        <th class="col-md-1">
                                            #
                                        </th>                    
                                        <th class="col-md-1">
                                            <a href="#" ng-click="sortType = 'id'; sortReverse = !sortReverse">
                                            ID
                                            <span ng-show="sortType == 'id' && !sortReverse" class="fa fa-caret-down"></span>
                                            <span ng-show="sortType == 'id' && sortReverse" class="fa fa-caret-up"></span>                            
                                        </th>
                                        <th class="col-md-2">
                                            <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse"> 
                                            Name
                                            <span ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></span>
                                            <span ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></span>
                                            </a>
                                        </th>
                                        <th class="col-md-2">
                                            <a href="#" ng-click="sortType = 'username'; sortReverse = !sortReverse"> 
                                            Username
                                            <span ng-show="sortType == 'username' && !sortReverse" class="fa fa-caret-down"></span>
                                            <span ng-show="sortType == 'username' && sortReverse" class="fa fa-caret-up"></span>
                                            </a>                             
                                        </th>
                                        <th class="col-md-2">
                                            Contact
                                        </th>
                                        <th class="col-md-2">
                                            Email
                                        </th>                       
                                         <th class="col-md-2">
                                            Action
                                        </th>                                                                       
                                    </tr>

                                    <tbody>
                                      
                                         <tr dir-paginate="user in users | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage" pagination-id="user" current-page="currentPage" ng-controller="repeatController">
                                            <td class="col-md-1">@{{ number }} </td>
                                            <td class="col-md-1">{{ $USER_PREFIX }}@{{ user.id }}</td>
                                            <td class="col-md-2">@{{ user.name }}</td>
                                            <td class="col-md-2">@{{ user.username }}</td>
                                            <td class="col-md-2">@{{ user.contact }}</td>
                                            <td class="col-md-2">@{{ user.email }}</td>
                                            <td class="col-md-2">


                                            <a href="/user/@{{ user.id }}/edit" class="btn btn-sm btn-primary">Edit</a>

                                            @can('delete_user')
                                            <button class="btn btn-danger btn-sm btn-delete" ng-click="confirmDelete(user.id)">Delete</button>
                                            @endcan
                                            </td>
                                        </tr>
                                        <tr ng-show="(users | filter:search).length == 0 || ! users.length">
                                            <td colspan="6">No Records Found</td>
                                        </tr>                         

                                    </tbody>
                                </table>
                            </div>            
                        </div>

                        <div class="panel-footer">
                              <dir-pagination-controls pagination-id="user" max-size="5" direction-links="true" boundary-links="true" class="pull-left"> </dir-pagination-controls>
                              <label class="pull-right totalnum" for="totalnum">Showing @{{(users | filter:search).length}} of @{{users.length}} entries</label> 
                        </div>
                    </div>
                </div>  
            </div>



            <div class="tab-pane" id="role_draft">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                            <h3 class="panel-title display_num"><strong>Access Level</strong></h3>
                        </div>
                        <div class="pull-right">
                            @can('create_role')
                            <a href="/role/create" class="btn btn-success">+ New Access</a>                          
                            @endcan
                        </div>
                    </div>

                    <div class="panel-body">
                        <ul class="list-group">
                            <li class="list-group-item row">
                                <span class="col-md-1"><strong>#</strong></span>
                                <span class="col-md-3"><strong>Access Level</strong></span>
                                <span class="col-md-6"><strong>Assigned Permission</strong></span>
                                <span class="col-md-2"><strong>Action</strong></span>                                
                            </li>

                            <?php $index = $roles->firstItem(); ?>
                            @unless(count($roles)>0)
                                <li class="list-group-item row text-center">
                                    <span colspan="7">No Records Found</span>
                                </li>
                            @else
                                @foreach($roles as $role)
                                <li class="list-group-item row">
                                    <span class="col-md-1">{{ $index++ }}</span>
                                    <span class="col-md-3">{{$role->label}}</span>
                                    <span class="col-md-6">
                                    @foreach($role->permissions as $index2 => $permission)
                                        {{$permission->name}}
                                        @if($index2 + 1 != count($role->permissions))
                                        ,
                                        @endif
                                    @endforeach
                                    </span>
                                    <span class="col-md-2">

                                    <a href="/role/{{ $role->id }}/edit" class="btn btn-sm btn-primary col-md-4" style="margin-right:5px;">Edit</a>

                                    @can('delete_role')  
                                    {!! Form::open(['method'=>'DELETE', 'action'=>['RoleController@destroy', $role->id], 'onsubmit'=>'return confirm("Are you sure you want to delete?")']) !!}                
                                        {!! Form::submit('Delete', ['class'=> 'btn btn-danger btn-sm col-md-5']) !!}
                                    {!! Form::close() !!}                          
                                    @endcan
                                    </span>                             
                                </li>
                                @endforeach
                            @endunless
                        </ul>
                    </div>

                    <div class="panel-footer">
                        {!! $roles->render() !!}

                        <label class="pull-right totalnum" for="totalnum">
                            Total of {{$roles->total()}} entries
                        </label>
                    </div>
                </div>
            </div>


            @can('view_permission')
            <div class="tab-pane" id="permission_list">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                            <h3 class="panel-title display_num"><strong>Permission List</strong></h3>
                        </div>
                    </div>

                    <div class="panel-body">
                        <ul class="list-group">
                            <li class="list-group-item row">
                                <span class="col-md-1"><strong>#</strong></span>
                                <span class="col-md-3"><strong>Module</strong></span>
                                <span class="col-md-8"><strong>Module Action</strong></span>
                            </li>
                            <li class="list-group-item row">
                                <span class="col-md-1">1</span>
                                <span class="col-md-3">User</span>
                                <span class="col-md-8">                                 
                                    create_user, view_user, edit_user, delete_user
                                </span>
                            </li>
                            <li class="list-group-item row">
                                <span class="col-md-1">2</span>
                                <span class="col-md-3">Role</span>
                                <span class="col-md-8">
                                    create_role, view_role, edit_role, delete_role
                                </span>
                            </li>
                            <li class="list-group-item row">
                                <span class="col-md-1">3</span>
                                <span class="col-md-3">Permission</span>
                                <span class="col-md-8">
                                    view_permission
                                </span>
                            </li>                                                             
                        </ul>
                    </div>

                    <div class="panel-footer">
                          <label class="pull-right totalnum" for="totalnum">Total of 3 entries</label>                                                          
                    </div>                    
                </div>
            </div>
            @endcan 

            <div class="tab-pane" id="profile">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title pull-left">
                            <h3 class="panel-title display_num"><strong>Company Profile</strong></h3>
                        </div>
                        @if($profile->name != '')
                        <div class="pull-right">
                            <a href="/profile/{{$profile->id}}/edit" class="btn btn-primary">Edit Profile</a>  
                        </div>
                        @else
                        <div class="pull-right">
                            <a href="/profile/create" class="btn btn-success">+ Create Profile</a>  
                        </div>
                        @endif                        
                    </div>

                    <div class="panel-body">
                    @if($profile->name != '')
                        <div class="col-md-8 col-md-offset-2">
                            <div class="form-group">
                                {!! Form::label('name', 'Name', ['class'=>'control-label']) !!}
                                {!! Form::text('name', $profile->name, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('roc_no', 'ROC No.', ['class'=>'control-label']) !!}
                                {!! Form::text('roc_no', $profile->roc_no, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                            </div>                        

                            <div class="form-group">
                                {!! Form::label('address', 'Address', ['class'=>'control-label']) !!}
                                {!! Form::textarea('address', $profile->address, ['class'=>'form-control', 'rows'=>'3','readonly'=>'readonly']) !!}
                            </div> 

                            <div class="form-group">
                                {!! Form::label('contact', 'Contact', ['class'=>'control-label']) !!}
                                {!! Form::text('contact', $profile->contact, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                            </div> 

                            <div class="form-group">
                                {!! Form::label('alt_contact', 'Alt. Contact', ['class'=>'control-label']) !!}
                                {!! Form::text('alt_contact', $profile->alt_contact, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                            </div> 

                            <div class="form-group">
                                {!! Form::label('email', 'Email', ['class'=>'control-label']) !!}
                                {!! Form::email('email', $profile->email, ['class'=>'form-control', 'readonly'=>'readonly']) !!}
                            </div>                                                                         

                            @if($profile->logo != '')
                                <div class="col-md-3">
                                    {!! Form::label('logo', 'Logo', ['class'=>'control-label']) !!}
                                </div>                               
                                <div class="col-md-12">
                                    {!! Html::image($profile->logo) !!}
                                </div>                         
                            @endif

                            @if($profile->header != '')
                                <div class="col-md-3">
                                    {!! Form::label('header', 'Header', ['class'=>'control-label']) !!}
                                </div>                               
                                <div class="col-md-12">
                                    {!! Html::image($profile->header) !!}
                                </div>
                            @endif

                            @if($profile->footer != '')
                                <div class="col-md-3">
                                    {!! Form::label('footer', 'Footer', ['class'=>'control-label']) !!}
                                </div>                               
                                <div class="col-md-12">
                                    {!! Html::image($profile->footer) !!}
                                </div> 
                            @endif                       

                        </div>
                    </div>
                    @else
                    <p>Company Profile Unset, Click Create Profile to Setup</p>
                    @endif

                    {{-- <div class="panel-footer">
                        {!! $roles->render() !!}

                        <label class="pull-right totalnum" for="totalnum">
                            Total of {{$roles->total()}} entries
                        </label>
                    </div> --}}
                </div>
            </div>            


    </div>
</div>          

<script src="/js/user.js"></script>              
@stop