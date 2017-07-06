@inject('people', 'App\Person')

  <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">{{ $APP_NAME }}</a>
            @if(! auth()->guest())
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
            @unless (Auth::guest())
            <ul class="nav navbar-nav side-nav">

            @unless(Auth::user()->type == 'marketer')
                <li class="{{ strpos(Request::path(), 'transaction') !== false ? 'active' : '' }}">
                    <a href="/transaction"><i class="fa fa-fw fa-credit-card"></i> {{ $TRANS_TITLE }}</a>
                </li>
                <li class="{{ strpos(Request::path(), 'person') !== false ? 'active' : '' }}">
                    <a href="/person"><i class="fa fa-fw fa-users"></i> {{ $PERSON_TITLE }}</a>
                </li>
                <li class="{{ strpos(Request::path(), 'item') !== false ? 'active' : '' }}">
                    <a href="/item"><i class="fa fa-fw fa-shopping-cart"></i> {{ $ITEM_TITLE }}</a>
                </li>
                @cannot('transaction_view')
                @cannot('accountant_view')
                    <li class="{{ strpos(Request::path(), 'profile') !== false ? 'active' : '' }}">
                        <a href="/profile"><i class="fa fa-fw fa-building"></i> {{ $PROFILE_TITLE }}</a>
                    </li>
                    <li class="{{ strpos(Request::path(), 'user') !== false ? 'active' : '' }}">
                        <a href="/user"><i class="fa fa-fw fa-user"></i> {{ $USER_TITLE }} & Data</a>
                    </li>
                @endcannot
                <li class="{{ strpos(Request::path(), 'detailrpt') !== false ? 'active' : '' }}">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-fw fa-book"></i> {{ $DETAILRPT_TITLE }}</a>
                    <ul class="dropdown-menu">
                        <li class="text-left"><a href="/detailrpt/account"> Account</a></li>
                        <li class="text-left"><a href="/detailrpt/sales"> Sales</a></li>
                        <li class="text-left"><a href="/detailrpt/invbreakdown/detail"> InvBreakdown Detail</a></li>
                        <li class="text-left"><a href="/detailrpt/invbreakdown/summary"> InvBreakdown Summary</a></li>
                        <li class="text-left"><a href="/detailrpt/stock/date"> Stock Sold (Date)</a></li>
                        <li class="text-left"><a href="/detailrpt/stock/customer"> Stock Sold (Customer)</a></li>
                        <li class="text-left"><a href="/detailrpt/stock/billing"> Stock (Billing)</a></li>
                    </ul>
                </li>
                @endcannot
                <li class="{{ strpos(Request::path(), 'report') !== false ? 'active' : '' }}">
                    <a href="/report"><i class="fa fa-fw fa-file-text-o"></i> {{ $REPORT_TITLE }}</a>
                </li>
            @endunless
                @if(Auth::user()->hasRole('admin') or Auth::user()->type == 'marketer' or $people::where('user_id', Auth::user()->id)->first())
                    <li class="{{ strpos(Request::path(), 'setup') !== false ? 'active' : '' }}">
                        <a href="/market/setup"><i class="fa fa-fw fa-cog"></i> DtD Setting</a>
                    </li>
                    <li class="{{ strpos(Request::path(), 'member') !== false ? 'active' : '' }}">
                        <a href="/market/member"><i class="fa fa-fw fa-sitemap"></i> DtD Members</a>
                    </li>
                    <li class="{{ strpos(Request::path(), 'market/customer') !== false ? 'active' : '' }}">
                        <a href="/market/customer"><i class="fa fa-fw fa-male"></i> DtD Customers</a>
                    </li>
                    <li class="{{ strpos(Request::path(), 'deal') !== false ? 'active' : '' }}">
                        <a href="/market/deal"><i class="fa fa-fw fa-wpforms"></i> DtD Deals</a>
                    </li>
                @endif
{{--                 <li class="{{ strpos(Request::path(), 'docs') !== false ? 'active' : '' }}">
                    <a href="/market/docs"><i class="fa fa-fw fa-file-o"></i> DtD Report</a>
                </li> --}}
            </ul>
        </div>
        @endunless
    </nav>