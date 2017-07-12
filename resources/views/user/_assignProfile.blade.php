	<div>
		<div class="row form-group">
			<div class="col-md-10 col-sm-12 col-xs-12">
				<div class="form-group">
					{!! Form::label('profile_id', 'Add Profile', ['class'=>'control-label']) !!}
		            <select2 name="profile_id" class="select form-control" v-model="form.profile_id">
		                <option value=""></option>
						<option v-for="profile in profileselection" :value="profile.id">
							@{{profile.name}}
						</option>
		            </select2>
				</div>
			</div>
			<div class="col-md-2 col-sm-12 col-xs-12">
				<a href="" class="btn btn-success btn-md hidden-xs hidden-sm" style="margin-top: 27px;" :disabled="!form.profile_id" @click.prevent="addProfile"><i class="fa fa-plus"></i> Add</a>
				<a href="" class="btn btn-success btn-block btn-sm hidden-md hidden-lg" :disabled="!form.profile_id" @click.prevent="addProfile"><i class="fa fa-plus"></i> Add</a>
			</div>
		</div>

			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<tr style="background-color: #a3a3c2;">
						<th class="col-md-1 text-center">
							#
						</th>
						<th class="col-md-10 text-left">
							Accesible Profile
						</th>
						<th class="col-md-1"></th>
					</tr>
                    <tr v-for="(profile, index) in list">
                        <td class="col-md-1 text-center">
                            @{{ index + 1 }}
                        </td>
                        <td class="col-md-10 text-left">
                            @{{ profile.name }}
                        </td>
                        <td class="col-md-1 text-center">
                        	<button class="btn btn-danger btn-sm" @click="removeProfile(profile.id)"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <tr v-if="list.length == 0">
                        <td colspan="14" class="text-center"> No Results Found </td>
                    </tr>
				</table>
			</div>
		</div>
	</div>

