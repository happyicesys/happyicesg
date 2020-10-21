@inject('generalsettings', 'App\GeneralSetting')
<nav id="mainNav" class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand page-scroll" href="/"><img src="/img/Happy-Ice-Logo.png" alt="logo" height="42" width="120" style="margin-top: -9px;" /></a>
        </div>

        @php
            $home_access = false;
            $product_access = false;
            $online_order_access = false;
            $warehouse_sales_access = false;
            $recruitment_access = false;
            $vending_machine_access = false;
            $franchise_access = false;
            $about_access = false;
            $contact_access = false;
            $locate_access = false;

            $general_setting = $generalsettings::firstOrFail();
            $country_region = $general_setting->country_region;

            switch($country_region) {
                case 'SINGAPORE':
                    $home_access = false;
                    $product_access = true;
                    $online_order_access = true;
                    $warehouse_sales_access = true;
                    $recruitment_access = true;
                    $vending_machine_access = true;
                    $franchise_access = true;
                    $about_access = true;
                    $contact_access = true;
                    $order_now_access = false;
                    $vend_complain_access=true;
                    $locate_access = true;
                    break;
                case 'MALAYSIA':
                    $home_access = true;
                    $product_access = false;
                    $online_order_access = false;
                    $warehouse_sales_access = false;
                    $recruitment_access = false;
                    $vending_machine_access = false;
                    $franchise_access = false;
                    $about_access = false;
                    $contact_access = false;
                    $order_now_access = true;
                    $vend_complain_access=true;
                    break;
                default:
                    $home_access = false;
                    $product_access = false;
                    $online_order_access = false;
                    $warehouse_sales_access = false;
                    $recruitment_access = false;
                    $vending_machine_access = false;
                    $franchise_access = false;
                    $about_access = false;
                    $contact_access = false;
                    $order_now_access = false;
                    $vend_complain_access=true;
                    $locate_access = true;
            }
        @endphp
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right" role="navigation">
                @if($home_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="//{{$general_setting->home_url}}">Home</a>
                </li>
                @endif
                <li><a target="_blank" style="color: white;" href="/icecream-buffet">Ice Cream Buffet</a></li>
                @if($product_access)
                <li class="dropdown">
                    <a href="#"  style="color: white;" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false">Products<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/menu">Menu</a></li>
                        <li><a href="/client/product">Ice Cream</a></li>
                        <li><a href="/every-morning-healthy"> Every Morning Healthy Tea</a></li>
                    </ul>
                </li>
                @endif
                @if($online_order_access)
{{--
                <li>
                    <a class="page-scroll" style="color: white;" href="/d2d">Online Order</a>
                </li> --}}
{{--
                <li>
                    <a class="page-scroll" style="color: yellow;" href="/brown-sugar-milk-boba-icecream">Brown Sugar Boba Ice Cream Bar Order</a>
                </li> --}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false" style="color:yellow;">Online Order Form<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        <li><a target="_blank" href="/delivery">Ice Cream</a></li>
                        <li><a target="_blank" href="/every-morning-healthy-order">Every Morning Healthy Drinks Order</a></li>
                        </ul>
                    </li>
{{--
                    <li>
                        <a class="page-scroll" style="color: yellow;" href="/warehouse-sales">Warehouse Sales</a>
                    </li> --}}
                @endif
{{--
                @if($warehouse_sales_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/warehouse-sales">Warehouse Sales</a>
                </li>
                @endif --}}
                @if($recruitment_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/recruitment">Recruitment</a>
                </li>
                @endif
{{--                 <li>
                    <a class="page-scroll" style="color: white;" href="/vending">Vending Machine</a>
                </li> --}}
                @if($vending_machine_access)
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false" style="color:white;">Vending Machine <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/vending/funv">Fun Vending Machine</a></li>
                    {{-- <li><a href="/vending/honestv">HonestV</a></li> --}}
                    <li><a href="/vending/directv">Direct Vending Machine</a></li>
                  </ul>
                </li>
                @endif
                @if($franchise_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/franchise">Franchise</a>
                </li>
                @endif
                @if($about_access)
                <li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" type="button" aria-haspopup="true" aria-expanded="false" style="color:white;">About <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="/client/about">About Us</a></li>
                          <li><a href="/privacy">Privacy Policy</a></li>
                        </ul>
                      </li>
                    <a class="page-scroll" style="color: white;" href="/client/about">About</a>
                </li>
                @endif
                @if($contact_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/client/contact">Contact</a>
                </li>
                @endif
                @if($locate_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/#">Locate (Coming Soon)</a>
                    {{-- <a class="page-scroll" style="color: white;" href="/client/locate">Locate</a> --}}
                </li>
                @endif
                @if($order_now_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/shop">Order Now</a>
                </li>
                @endif
                @if($vend_complain_access)
                <li>
                    <a class="page-scroll" style="color: white;" href="/vendcomplain">Vending Complain</a>
                </li>
                @endif
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
