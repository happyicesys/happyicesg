@inject('people', 'App\Person')

  <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">{{ config('app.name', 'Happyice') }}</a>
            @if(!auth()->guest() and !auth()->user()->hasRole('franchisee') and !auth()->user()->hasRole('watcher') and !auth()->user()->hasRole('subfranchisee'))
                <a href="/transaction/create" class="btn btn-success btn-sm" style="margin: 10px 0px 0px 10px;">
                    <i class="fa fa-plus"></i>
                    New Transaction
                </a>
            @endif
        </div>

<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="/auth/login">Login</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">User:  {{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/auth/logout">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>

            @php
                $access = false;
                $transaction_access = false;
                $jobassign_access = false;
                $person_access = false;
                $hd_access = false;
                $personasset_access = false;
                $item_access = false;
                $profile_access = false;
                $user_access = false;
                $detailrpt_access = false;
                $dailyreport_access = false;
                $report_access = false;
                $operation_access = false;
                $dtd_access = false;
                $franchisee_access = false;
                $bom_access = false;
                $ecommerce_access = false;
                $personmaintenance_access = false;
                $jobcard_access = false;
                $vending_access = false;
                $route_template = false;
                $potential_customer_access = true;

                if(auth()->guest()) {
                    $access = false;
                }else {
                    if(auth()->user()->hasRole('hd_user')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        $personasset_access = true;
                        $hd_access = true;
                    }

                    if(auth()->user()->hasRole('watcher')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                    }

                    if(auth()->user()->type == 'marketer') {
                        $access = true;
                        // $dtd_access = true;
                    }

                    if(auth()->user()->hasRole('franchisee')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        $franchisee_access = true;
                    }

                    if(auth()->user()->hasRole('subfranchisee')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        // $franchisee_access = true;
                    }

                    if(auth()->user()->hasRole('driver') or auth()->user()->hasRole('technician')) {
                        $access = true;
                        // $transaction_access = true;
                        $jobassign_access = true;
                        $franchisee_access = true;
                        $hd_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        $detailrpt_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $dailyreport_access = true;
                    }

                    if(auth()->user()->hasRole('salesperson')) {
                        $access = true;
                        $transaction_access = true;
                        $franchisee_access = true;
                        $hd_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        // $dtd_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $dailyreport_access = true;
                    }

                    if(auth()->user()->hasRole('account') or auth()->user()->hasRole('accountadmin')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        $detailrpt_access = true;
                        // $dtd_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $vending_access = true;
                        $dailyreport_access = true;
                    }

                    if(auth()->user()->hasRole('supervisor')) {
                        $access = true;
                        $transaction_access = true;
                        $jobassign_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        $detailrpt_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $dailyreport_access = true;
                        $operation_access = true;
                    }

                    if(auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus')) {
                        $access = true;
                        $transaction_access = true;
                        $jobassign_access = true;
                        $person_access = true;
                        $item_access = true;
                        $detailrpt_access = true;
                        $dailyreport_access = true;
                        $ecommerce_access = true;
                        $operation_access = true;
                        $personmaintenance_access = true;
                    }

                    if(auth()->user()->hasRole('driver-supervisor')) {
                        $access = true;
                        $jobassign_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        $detailrpt_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $operation_access = true;
                        $dailyreport_access = true;
                        $route_template = true;
                    }

                    if(auth()->user()->hasRole('supervisor_msia')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        $item_access = true;
                        $report_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $vending_access = true;
                        $operation_access = true;
                        $dailyreport_access = true;
                    }

                    if(auth()->user()->can_access_inv) {
                        $item_access = true;
                    }else {
                        $item_access = false;
                    }

                    if(auth()->user()->hasRole('admin')) {
                        $access = true;
                        $transaction_access = true;
                        $jobassign_access = true;
                        $person_access = true;
                        $hd_access = true;
                        $personasset_access = true;
                        $item_access = true;
                        $profile_access = true;
                        $user_access = true;
                        $detailrpt_access = true;
                        $report_access = true;
                        $operation_access = true;
                        // $dtd_access = true;
                        $franchisee_access = true;
                        $bom_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $vending_access = true;
                        $dailyreport_access = true;
                        $route_template = true;
                    }

                    if(auth()->user()->hasRole('operation')) {
                        $access = true;
                        $transaction_access = true;
                        $person_access = true;
                        $hd_access = true;
                        $personasset_access = true;
                        $item_access = true;
                        $detailrpt_access = true;
                        $report_access = true;
                        $operation_access = true;
                        $bom_access = true;
                        $ecommerce_access = true;
                        $personmaintenance_access = true;
                        $jobcard_access = true;
                        $dailyreport_access = true;
                    }

                    if(auth()->user()->hasRole('event') or auth()->user()->hasRole('event_plus')) {
                        $access = true;
                        $transaction_access = true;
                        $jobassign_access = true;
                        $person_access = true;
                        $detailrpt_access = true;
                    }
                }
            @endphp

            @if($access)
            <ul class="nav navbar-nav side-nav" style="overflow-y: scroll;">
                @if($transaction_access)
                    <li class="{{ Request::segment(1) == 'transaction' ? 'active' : '' }}">
                        <a href="/transaction"><i class="fa fa-fw fa-credit-card"></i> {{ $TRANS_TITLE }}</a>
                    </li>
                @endif

                @if($jobassign_access)
                    <li class="{{ Request::segment(2) == 'jobassign' ? 'active' : '' }}">
                        <a href="/transaction/jobassign"><i class="fa fa-paper-plane" aria-hidden="true"></i> Job Assign</a>
                    </li>
                @endif
                @if($person_access)
                    <li class="{{ Request::segment(1) == 'person' ? 'active' : '' }}">
                        <a href="/person"><i class="fa fa-fw fa-users"></i> {{ $PERSON_TITLE }}</a>
                    </li>
                @endif

                @if($potential_customer_access)
                    <li class="{{ Request::segment(1) == 'potential-customer' ? 'active' : '' }}">
                        <a href="/potential-customer"><i class="fa fa-address-card-o"></i> Potential Customer</a>
                    </li>
                @endif                

                @if($route_template)
                    <li class="{{ Request::segment(1) == 'route-template' ? 'active' : '' }}">
                        <a href="/route-template"><i class="fa fa-road" aria-hidden="true"></i> Route Template</a>
                    </li>
                @endif
                @if($hd_access)
                    <li class="{{ Request::segment(1) == 'hdprofile' || Request::segment(1) == 'personasset' ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-truck"></i>
                            HD Profile
                        <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="text-left"><a href="/hdprofile/transation"> Transaction</a></li>
                            <li class="text-left"><a href="/personasset">{{$PERSONASSET_TITLE}}</a></li>
                            {{-- <li class="text-left"><a href="/hdprofile/sales"> Sales</a></li> --}}
                        </ul>
                    </li>
                @endif
                @if($personasset_access)

                @endif
                @if($item_access)
                    <li class="{{ Request::segment(1) == 'item' ? 'active' : '' }}">
                        <a href="/item"><i class="fa fa-fw fa-shopping-cart"></i> {{ $ITEM_TITLE }}</a>
                    </li>
                @endif
                @if($profile_access)
                    <li class="{{ Request::segment(1) == 'profile' ? 'active' : '' }}">
                        <a href="/profile"><i class="fa fa-fw fa-building"></i> {{ $PROFILE_TITLE }}</a>
                    </li>
                @endif
                @if($user_access)
                    <li class="{{ Request::segment(1) == 'user' ? 'active' : '' }}">
                        <a href="/user"><i class="fa fa-fw fa-user"></i> {{ $USER_TITLE }} & Data</a>
                    </li>
                @endif
                @if($detailrpt_access)
                    <li class="{{ Request::segment(1) == 'detailrpt' ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-book"></i> {{ $DETAILRPT_TITLE }} <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                                <li class="text-left"><a href="/detailrpt/sales"> Sales</a></li>
                            @if(!auth()->user()->hasRole('supervisor') and !auth()->user()->hasRole('driver-supervisor') and !auth()->user()->hasRole('driver') and !auth()->user()->hasRole('technician') and !auth()->user()->hasRole('merchandiser') and !auth()->user()->hasRole('merchandiser_plus') and !auth()->user()->hasRole('event') and !auth()->user()->hasRole('event_plus'))
                                <li class="text-left"><a href="/detailrpt/account"> Account</a></li>
                                <li class="text-left"><a href="/detailrpt/invbreakdown/detail"> InvBreakdown Detail</a></li>
                                <li class="text-left"><a href="/detailrpt/invbreakdown/summary"> InvBreakdown Summary</a></li>
                                <li class="text-left"><a href="/detailrpt/stock/date"> Stock Sold (Date)</a></li>
                                <li class="text-left"><a href="/detailrpt/stock/customer"> Stock Sold (Customer)</a></li>
                                <li class="text-left"><a href="/detailrpt/stock/billing"> Stock (Billing)</a></li>
                                <li class="text-left"><a href="/detailrpt/vending"> Vending Machine</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if($dailyreport_access)
                    <li class="{{ Request::segment(1) == 'dailyreport' ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-flag"></i> Performance <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            @if(!auth()->user()->hasRole('merchandiser') and !auth()->user()->hasRole('merchandiser_plus'))
                            <li class="text-left"><a href="/dailyreport/commission"> Driver Commission</a></li>
                            <li class="text-left"><a href="/dailyreport/driver-location-count"> Driver # Location</a></li>
                            @endif
                            @if(auth()->user()->hasRole('admin') or auth()->user()->hasRole('account') or auth()->user()->hasRole('supervisor') or auth()->user()->hasRole('merchandiser') or auth()->user()->hasRole('merchandiser_plus'))
                                <li class="text-left"><a href="/dailyreport/account-manager-performance"> Acc Manager</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if($report_access)
                    <li class="{{ Request::segment(1) == 'report' ? 'active' : '' }}">
                        <a href="/report"><i class="fa fa-fw fa-file-text-o"></i> {{ $REPORT_TITLE }}</a>
                    </li>
                @endif
                @if($operation_access)
                    <li class="{{ Request::segment(1) == 'operation' ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-sticky-note-o"></i> Operations <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            @if(!auth()->user()->hasRole('merchandiser') and !auth()->user()->hasRole('merchandiser_plus'))
                                <li class="text-left"><a href="/operation/worksheet"> Worksheet</a></li>
                            @endif
                            @if(!auth()->user()->hasRole('driver-supervisor'))
                                <li class="text-left"><a href="/operation/merchandiser"> Merchandiser</a></li>
                                <li class="text-left"><a href="/operation/merchandiser-mobile"> Merchandiser (Mobile)</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if($dtd_access)
                    <li class="{{ (Request::segment(1) == 'setup' || Request::segment(1) == 'member' || Request::segment(1) == 'market') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-address-card"></i> Door To Door <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            <li class="text-left"><a href="/market/setup"> DtD Setting</a></li>
                            <li class="text-left"><a href="/market/member"> DtD Members</a></li>
                            <li class="text-left"><a href="/market/customer"> DtD Customers</a></li>
                            <li class="text-left"><a href="/market/deal"> DtD Deals</a></li>
                        </ul>
                    </li>
                @endif
                @if($franchisee_access)
                    <li class="{{ Request::segment(1) == 'franchise' ? 'active' : '' }}">
                        <a href="/franchisee"><i class="fa fa-fw fa-handshake-o"></i> {{ $FRANCHISE_TRANS }}</a>
                    </li>
                    <li class="{{ Request::segment(1) == 'franrpt' ? 'active' : '' }}">
                        <a href="/franrpt"><i class="fa fa-fw fa-area-chart"></i> {{ $FRANCHISE_RPT }}</a>
                    </li>
                @endif
                @if($vending_access)
                    <li class="{{ (Request::segment(1) == 'bom' || Request::segment(1) == 'personmaintenance' || Request::segment(1) == 'vm' || Request::segment(1) == 'deal') ? 'active' : '' }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-rocket"></i> Vending <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu">
                            @if($bom_access)
                                <li class="text-left"><a href="/bom"> {{ $BOM_TITLE }}</a></li>
                            @endif
                            @if($personmaintenance_access)
                                <li class="text-left"><a href="/personmaintenance"> {{ $PERSONMAINTENANCE_TITLE }}</a></li>
                            @endif
                            <li class="text-left"><a href="/vm"> Machine</a></li>
                            <li class="text-left"><a href="/simcard"> SIM card</a></li>
                        </ul>
                    </li>
                @endif
                @if($jobcard_access)
                    <li class="{{ Request::segment(1) == 'jobcard' ? 'active' : '' }}">
                        <a href="/jobcard"><i class="fa fa-th-list "></i> {{ $JOBCARD_TITLE }}</a>
                    </li>
                @endif
{{--                 @if($ecommerce_access)
                    <li class="{{ strpos(Request::path(), 'ecommerce') !== false ? 'active' : '' }}">
                        <a href="/ecommerce"><i class="fa fa-shopping-bag "></i> {{ $ECOMMERCE_TITLE }}</a>
                    </li>
                @endif --}}
            </ul>
            @endif
        </div>
    </nav>