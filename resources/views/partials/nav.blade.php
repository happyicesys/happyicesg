  <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">{{ $APP_NAME }}</a>
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

                <li class="{{ strpos(Request::path(), 'transaction') !== false ? 'active' : '' }}">
                    <a href="/transaction"><i class="fa fa-fw fa-credit-card"></i> {{ $TRANS_TITLE }}</a>
                </li>
                <li class="{{ strpos(Request::path(), 'person') !== false ? 'active' : '' }}">
                    <a href="/person"><i class="fa fa-fw fa-users"></i> {{ $PERSON_TITLE }}</a>
                </li>
                @cannot('transaction_view')
                    <li class="{{ strpos(Request::path(), 'item') !== false ? 'active' : '' }}">
                        <a href="/item"><i class="fa fa-fw fa-shopping-cart"></i> {{ $ITEM_TITLE }}</a>
                    </li>
                @cannot('accountant_view')
                    <li class="{{ strpos(Request::path(), 'profile') !== false ? 'active' : '' }}">
                        <a href="/profile"><i class="fa fa-fw fa-building"></i> {{ $PROFILE_TITLE }}</a>
                    </li>
                    <li class="{{ strpos(Request::path(), 'user') !== false ? 'active' : '' }}">
                        <a href="/user"><i class="fa fa-fw fa-user"></i> {{ $USER_TITLE }} & Data</a>
                    </li>
                @endcannot
                @endcannot
                <li class="{{ strpos(Request::path(), 'report') !== false ? 'active' : '' }}">
                    <a href="/report"><i class="fa fa-fw fa-file-text-o"></i> {{ $REPORT_TITLE }}</a>
                </li>
{{--
                <li class="{{ strpos(Request::path(), 'marketing') !== false ? 'active' : '' }}">
                    <a href="/marketing"><i class="fa fa-fw fa-sitemap"></i> {{ $MARKETING_TITLE }}</a>
                </li> --}}
            </ul>
        </div>
        @endunless
    </nav>