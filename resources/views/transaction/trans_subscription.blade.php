@extends('template')
@section('title')
{{ $TRANS_TITLE }}
@stop
@section('content')
<div class="create_edit">
<div class="panel panel-primary">
    <div class="panel-heading">
        Transaction Email Subscription
    </div>
    <div class="panel-body">
        <transsubscribe id="transSubscriptionController" inline-template>
			<div>
				<div class="row form-group">
					<div class="col-md-10 col-sm-12 col-xs-12">
						<div class="form-group">
							{!! Form::label('user_id', 'Add User', ['class'=>'control-label']) !!}
				            <select2 name="user_id" class="select form-control" v-model="form.user_id">
				                <option value=""></option>
								<option v-for="user in userselection" :value="user.id">
									@{{user.name}}  <span v-if="user.email">(@{{user.email}})</span> @{{user.roles.map( function (el) {
										return el.name;
									})}}
								</option>
				            </select2>
						</div>
					</div>
					<div class="col-md-2 col-sm-12 col-xs-12">
						<a href="" class="btn btn-success btn-md hidden-xs hidden-sm" style="margin-top: 27px;" :disabled="!form.user_id" @click.prevent="addUser"><i class="fa fa-plus"></i> Add</a>
						<a href="" class="btn btn-success btn-block btn-sm hidden-md hidden-lg" :disabled="!form.user_id" @click.prevent="addUser"><i class="fa fa-plus"></i> Add</a>
					</div>
				</div>

					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<tr style="background-color: #a3a3c2;">
								<th class="col-md-1 text-center">
									#
								</th>
								<th class="col-md-2 text-center">
									Name
								</th>
								<th class="col-md-2 text-center">
									Username
								</th>
								<th class="col-md-4 text-center">
									Email
								</th>
								<th class="col-md-4 text-center">
									Role
								</th>
								<th class="col-md-1"></th>
							</tr>
		                    <tr v-for="(user, index) in list">
		                        <td class="col-md-1 text-center">
		                            @{{ index + 1 }}
		                        </td>
		                        <td class="col-md-2 text-left">
		                            @{{ user.name }}
		                        </td>
		                        <td class="col-md-2 text-left">
		                            @{{ user.username }}
		                        </td>
		                        <td class="col-md-4 text-center">
		                            @{{ user.email }}
		                        </td>
		                        <td class="col-md-4 text-center">
		                            @{{ user.roles.map(function(el) { return el.name;}) }}
		                        </td>
		                        <td class="col-md-1 text-center">
		                        	<button class="btn btn-danger btn-sm" @click="removeUser(user.id)"><i class="fa fa-times"></i></button>
		                        </td>
		                    </tr>
		                    <tr v-if="list.length == 0">
		                        <td colspan="14" class="text-center"> No Results Found </td>
		                    </tr>
						</table>
					</div>
				</div>
			</div>
        </transsubscribe>
    </div>
</div>
</div>
<script src="/js/vue-controller/transSubscriptionController.js"></script>
@stop