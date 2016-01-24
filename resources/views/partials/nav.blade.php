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

        <ul class="nav navbar-nav navbar-right">
            @if (Auth::guest())
                <li><a href="/auth/login">Login</a></li>
            @else
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/auth/logout">Logout</a></li>
                    </ul>
                </li>
            @endif
        </ul>
<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <div class="collapse navbar-collapse navbar-ex1-collapse" id="navbarCollapse">
            <ul class="nav navbar-nav side-nav">
                {{-- <li>
                    <a href="index.html"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                </li> --}}
                <li class="{{ strpos(Request::path(), 'transaction') !== false ? 'active' : '' }}">
                    <a href="/transaction"><i class="fa fa-fw fa-credit-card"></i> {{ $TRANS_TITLE }}</a>
                </li>                
                <li class="{{ strpos(Request::path(), 'person') !== false ? 'active' : '' }}">
                    <a href="/person"><i class="fa fa-fw fa-users"></i> {{ $PERSON_TITLE }}</a>
                </li>
                {{-- <li>
                    <a href="/sale"><i class="fa fa-fw fa-tasks"></i> Sales Pipeline</a>
                </li> --}}
                <li class="{{ strpos(Request::path(), 'item') !== false ? 'active' : '' }}">
                    <a href="/item"><i class="fa fa-fw fa-shopping-cart"></i> {{ $ITEM_TITLE }}</a>
                </li>                

                {{-- <li>
                    <a href="/scheduler"><i class="fa fa-fw fa-clock-o"></i> To Do's</a>
                </li>
                <li>
                    <a href="/report"><i class="fa fa-fw fa-file-text-o"></i> Report</a>
                </li>
                <li>
                    <a href="/massemail"><i class="fa fa-fw fa-envelope-o"></i> Email</a>
                </li>  --}}                                
                {{-- <li>
                    <a href="javascript:;" data-toggle="collapse" data-target="#demo"><i class="fa fa-fw fa-arrows-v"></i> Dropdown <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="demo" class="collapse">
                        <li>
                            <a href="#">Dropdown Item</a>
                        </li>
                        <li>
                            <a href="#">Dropdown Item</a>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <a href="/setting"><i class="fa fa-fw fa-cog"></i> Resouces Definition</a>
                </li> --}}                
                {{-- <li>
                    <a href="/person"><i class="fa fa-fw fa-briefcase"></i> {{ $PERSON_TITLE }}</a>
                </li> --}}
                <li class="{{ strpos(Request::path(), 'profile') !== false ? 'active' : '' }}">
                    <a href="/profile"><i class="fa fa-fw fa-building"></i> {{ $PROFILE_TITLE }}</a>
                </li>
                <li class="{{ strpos(Request::path(), 'user') !== false ? 'active' : '' }}">
                    <a href="/user"><i class="fa fa-fw fa-user"></i> {{ $USER_TITLE }}</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->

        {{-- <div id="navbarCollapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ strpos(Request::path(), 'transaction') !== false ? 'active' : '' }}">
                    <a href="/transaction"><i class="fa fa-fw fa-credit-card"></i> Transaction</a>
                </li>                
                <li class="{{ strpos(Request::path(), 'person') !== false ? 'active' : '' }}">
                    <a href="/person"><i class="fa fa-fw fa-users"></i> {{ $PERSON_TITLE }}</a>
                </li>
                <li class="{{ strpos(Request::path(), 'item') !== false ? 'active' : '' }}">
                    <a href="/item"><i class="fa fa-fw fa-shopping-cart"></i> Item</a>
                </li>  
                <li class="{{ strpos(Request::path(), 'user') !== false ? 'active' : '' }}">
                    <a href="/user"><i class="fa fa-fw fa-user"></i> User</a>
                </li>
            </ul>
        </div>  --}}        
    </nav>