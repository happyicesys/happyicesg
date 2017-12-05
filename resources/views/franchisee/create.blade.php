@inject('people', 'App\Person')

@extends('template')
@section('title')
{{ $FRANCHISE_TRANS }}
@stop
@section('content')

<div class="create_edit" ng-app="app" ng-controller="ftransController">
<div class="panel panel-primary">

    <div class="panel-heading">
        <h3 class="panel-title"><strong>New {{$FRANCHISE_TRANS}}</strong></h3>
    </div>

    <div class="panel-body">
        {!! Form::model($ftransaction = new \App\Ftransaction, ['action'=>'FtransactionController@store']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('person_id', 'Customer', ['class'=>'control-label']) !!}
                        {!! Form::select('person_id',
                            [''=>null] + $people::whereHas('profile', function($q){
                                $q->filterUserProfile();
                            })->filterFranchiseePeople()->select(DB::raw("CONCAT(cust_id,' - ',company) AS full, id"))->orderBy('cust_id')->whereActive('Yes')->where('cust_id', 'NOT LIKE', 'H%')->lists('full', 'id')->all(),
                            null,
                            [
                            'id'=>'person_id',
                            'class'=>'person form-control',
                            'ng-model'=>'personModel',
                            'ng-change'=>'onPersonSelected(personModel)'
                            ])
                        !!}
                    </div>
                </div>
            </div>
            {{-- division of panel --}}
            <div class="panel panel-primary">
                <div class="panel-body">

                    <div class="panel-body">
                        <div class="table-responsive">
                        <table class="table table-list-search table-hover table-bordered">
                            <tr style="background-color: #DDFDF8">
                                        <th class="col-md-1 text-center">
                                            #
                                        </th>
                                        <th class="col-md-1 text-center">
                                            INV #
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Status
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Delivery Date
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Delivered By
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Total Amount
                                        </th>
                                         <th class="col-md-1 text-center">
                                            Payment
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Last Modified By
                                        </th>
                                        <th class="col-md-1 text-center">
                                            Last Modified Time
                                        </th>
{{--                                         <th class="col-md-1 text-center">
                                            Action
                                        </th> --}}
                            </tr>

                            <tbody>

                                <tr dir-paginate="ftransaction in ftransactions | filter:search | orderBy:sortType:sortReverse | itemsPerPage:itemsPerPage"  current-page="currentPage" ng-controller="repeatController">
                                    <td class="col-md-1 text-center">@{{ number }} </td>
                                    <td class="col-md-1 text-center">
                                        <a href="/franchisee/@{{ ftransaction.id }}/edit">
                                            @{{ ftransaction.user_code }}
                                            @{{ ftransaction.ftransaction_id }}
                                        </a>
                                    </td>
                                    {{-- status by color --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="ftransaction.status == 'Pending'">
                                        @{{ ftransaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: orange;" ng-if="ftransaction.status == 'Confirmed'">
                                        @{{ ftransaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="ftransaction.status == 'Delivered'">
                                        @{{ ftransaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black; background-color:orange;" ng-if="ftransaction.status == 'Verified Owe'">
                                        @{{ ftransaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: black; background-color:green;" ng-if="ftransaction.status == 'Verified Paid'">
                                        @{{ ftransaction.status }}
                                    </td>
                                    <td class="col-md-1 text-center" ng-if="ftransaction.status == 'Cancelled'">
                                        <span style="color: white; background-color: red;" > @{{ ftransaction.status }} </span>
                                    </td>
                                    {{-- status by color ended --}}
                                    <td class="col-md-1 text-center">@{{ ftransaction.delivery_date | delDate: "yyyy-MM-dd"}}</td>
                                    <td class="col-md-1 text-center">@{{ ftransaction.driver }}</td>
                                    <td class="col-md-1 text-center">@{{ ftransaction.total }}</td>
                                    {{-- pay status --}}
                                    <td class="col-md-1 text-center" style="color: red;" ng-if="ftransaction.pay_status == 'Owe'">
                                        @{{ ftransaction.pay_status }}
                                    </td>
                                    <td class="col-md-1 text-center" style="color: green;" ng-if="ftransaction.pay_status == 'Paid'">
                                        @{{ ftransaction.pay_status }}
                                    </td>
                                    {{-- pay status ended --}}
                                    <td class="col-md-1 text-center">@{{ ftransaction.updated_by}}</td>
                                    <td class="col-md-1 text-center">@{{ ftransaction.updated_at}}</td>
                                </tr>
                                <tr ng-if="(ftransactions | filter:search).length == 0 || ! ftransactions.length">
                                    <td colspan="12" class="text-center">No Records Found</td>
                                </tr>

                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end of the division --}}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group pull-right" style="padding: 30px 0px 0px 0px;">
                        {!! Form::submit('Add', ['class'=> 'btn btn-success']) !!}
                        <a href="/franchisee" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>
</div>

<script src="/js/franchisee_create.js"></script>
<script>
    $('.select').select2();
</script>

@stop