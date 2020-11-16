<div>
  <div class="row form-group">
    <div class="col-md-10 col-sm-12 col-xs-12">
      <div class="form-group">
        {!! Form::label('custcategoryId', 'Add Custcategory', ['class'=>'control-label']) !!}
              <select2 name="custcategoryId" class="select form-control" v-model="form.custcategoryId">
                  <option value=""></option>
          <option v-for="custcategory in custcategorySelection" :value="custcategory.id">
            @{{custcategory.name}}
          </option>
              </select2>
      </div>
    </div>
    <div class="col-md-2 col-sm-12 col-xs-12">
      <a href="" class="btn btn-success btn-md hidden-xs hidden-sm" style="margin-top: 27px;" :disabled="!form.custcategoryId" @click.prevent="addCustcategory"><i class="fa fa-plus"></i> Add</a>
      <a href="" class="btn btn-success btn-block btn-sm hidden-md hidden-lg" :disabled="!form.custcategoryId" @click.prevent="addCustcategory"><i class="fa fa-plus"></i> Add</a>
    </div>
  </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <tr style="background-color: #a3a3c2;">
          <th class="col-md-1 text-center">
            #
          </th>
          <th class="col-md-10 text-left">
            Custcategory
          </th>
          <th class="col-md-1"></th>
        </tr>
                  <tr v-for="(custcategory, index) in list">
                      <td class="col-md-1 text-center">
                          @{{ index + 1 }}
                      </td>
                      <td class="col-md-10 text-left">
                          @{{ custcategory.name }}
                      </td>
                      <td class="col-md-1 text-center">
                        <button class="btn btn-danger btn-sm" @click="removeCustcategory(custcategory.id)"><i class="fa fa-times"></i></button>
                      </td>
                  </tr>
                  <tr v-if="list.length == 0">
                      <th colspan="14" class="text-center">
                        Able to access ALL (default)
                      </th>
                  </tr>
      </table>
    </div>
  </div>
</div>

