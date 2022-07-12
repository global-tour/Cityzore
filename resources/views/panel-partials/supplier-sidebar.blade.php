<?php
$subUser = \Illuminate\Support\Facades\Auth::guard('subUser');
$supplier = \Illuminate\Support\Facades\Auth::guard('supplier');
if ($subUser->check()) {
    $supervisor = \App\Supplier::findOrFail($subUser->user()->supervisor);
}
?>

<div style="height: 100%;" class="container-fluid sb2">
    <div class="row">
        <div class="sb2-1">
            <div class="sb2-12">
                <ul>
                    <li>
                        @if($supplier->check())
                        <h5>
                            {{ $supplier->user()->companyName }}
                            <span>
                                <a href="http://{{$supplier->user()->website}}">{{ $supplier->user()->website}}</a>
                            </span>
                        </h5>
                        <br>
                        <i class="dash-icon-button icon-cz-user"
                           onclick="window.location.href='{{url('supplier/'.$supplier->user()->id.'/edit')}}'">
                        </i>
                        <i class="dash-icon-button icon-cz-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></i>
                        @elseif($subUser->check())
                        <h5>
                            {{ $subUser->user()->name }} {{ $subUser->user()->surname }}

                            <span>
                            <a href="http://{{$supervisor->website}}">{{ $supervisor->website}}</a>
                        </span>
                        </h5>
                        <br>
                        <i class="dash-icon-button icon-cz-user"
                           onclick="window.location.href='{{url('supplier/'.$subUser->user()->supervisor.'/edit')}}'">
                        </i>
                        <i class="dash-icon-button icon-cz-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></i>
                        @endif
                    </li>
                </ul>
            </div>
            <div class="sb2-13">
                <ul class="collapsible" data-collapsible="accordion">
                    <li><a href="{{url('/')}}" class="menu-active"><i class="icon-cz-dashboard" aria-hidden="true"></i> Dashboard</a></li>
                    @if(Auth::guard('admin')->check() || Auth::guard('supplier')->check())
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-user" aria-hidden="true"></i> Users</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(Auth::guard('admin')->check())
                                    <li>
                                        <a href="{{url('/user')}}">All Users</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/user/create')}}">Add New user</a>
                                    </li>
                                    @if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->isSuperUser == 1)
                                        <li>
                                            <a href="{{url('admin')}}">All Admins</a>
                                        </li>
                                        <li>
                                            <a href="{{url('admin/create')}}">Add New Admin</a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{url('/commissioners')}}">All Commissioners</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{url('/supplier')}}">
                                        @if (auth()->guard('admin')->check())
                                            All Suppliers
                                        @elseif (auth()->guard('supplier')->check())
                                            All Restaurants
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{url('/supplier/create')}}">
                                        @if (auth()->guard('admin')->check())
                                            Add New Supplier
                                        @elseif (auth()->guard('supplier')->check())
                                            Add New Restaurant
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif
                    @if(auth()->guard('supplier')->check())
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-user" aria-hidden="true"></i> User Management</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/subusers')}}">All Sub Users</a>
                                </li>
                                <li>
                                    <a href="{{url('/subuser/create')}}">Create New Sub User</a>
                                </li>
                                <li>
                                    <a href="{{url('/roles')}}">All Roles</a>
                                </li>
                                <li>
                                    <a href="{{url('/role/create')}}">Add New Role</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-product" aria-hidden="true"></i> Products and Options</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/product?page=1')}}">All Products</a>
                                </li>
                                <li>
                                    <a href="{{url('/product/create')}}">Create New Product</a>
                                </li>
                                <li>
                                    <a href="{{url('/option')}}">All Options</a>
                                </li>
                                <li>
                                    <a href="{{url('/option/create')}}">Create New Option</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-availability" aria-hidden="true"></i> Availabilities</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li><a href="{{url('/availabilities')}}">All Availabilities</a></li>
                                <li><a href="{{url('/av/create')}}">Create Availabilities</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-pricing" aria-hidden="true"></i> Pricings</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/pricings')}}">All Pricings</a>
                                </li>
                                <li>
                                    <a href="{{url('/pricing/create')}}">Create Pricing</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-percentage" aria-hidden="true"></i> Special Offers</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/special-offers/')}}">All Special Offers</a>
                                </li>
                                <li>
                                    <a href="{{url('/special-offers/create')}}">Create Special Offer</a>
                                </li>
                                @if(Auth::guard('admin')->check())
                                    <li>
                                        <a href="{{url('/coupons')}}">All Coupons</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/coupon/create')}}">Create New Coupon</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-booking" aria-hidden="true"></i> Bookings</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/bookings')}}">All Bookings</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-barcode" aria-hidden="true"></i> Barcodes</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/barcodes')}}">All Barcodes</a>
                                </li>
                                <li>
                                    <a href="{{url('/barcodes/create')}}">Add New Barcode</a>
                                </li>
                                <li>
                                    <a href="{{url('/barcode/multiple-ticket')}}">Create Multiple Tickets</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment" aria-hidden="true"></i> External Payment</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                <li>
                                    <a href="{{url('/external-payment')}}">External Payments</a>
                                </li>
                                <li>
                                    <a href="{{url('/external-payment/create')}}">Create New External Payment</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment" aria-hidden="true"></i> Create Voucher</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/vouchers')}}">All Vouchers</a>
                                </li>
                                <li>
                                    <a href="{{url('/voucher/create')}}">Create Voucher</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    {{-- @if(Auth::guard('supplier')->check() && !empty(Auth::guard('supplier')->user()->comission) && Auth::guard('supplier')->user()->comission > 0) --}}
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment" aria-hidden="true"></i> Finance</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <form id="financePost" method="POST" action="{{url('/finance/get-bookings')}}">
                                        @csrf
                                        <a href="javascript:triggerFinancePostFromSidebar()">Finance</a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                    {{-- @endif --}}
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment" aria-hidden="true"></i> Comments</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/comments')}}">Comments</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-instagram" aria-hidden="true"></i> Gallery</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/gallery')}}">Photo Gallery</a>
                                </li>
                                <li>
                                    <a href="{{url('/gallery/create')}}">Add New Photo</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @if(Auth::guard('admin')->check())
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-location" aria-hidden="true"></i> Attractions</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/attraction')}}">All Attractions</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/attraction/create')}}">Add New Attraction</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-management" aria-hidden="true"></i> Configuration</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/config')}}">Update Configuration</a>
                                </li>
                            </ul>
                            @if(Auth::guard('admin')->check())
                                <ul>
                                    <li>
                                        <a href="{{url('/general-config')}}">General Configuration</a>
                                    </li>
                                </ul>
                            @endif
                        </div>
                    </li>
                    @if(Auth::guard('admin')->check())
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-localization" aria-hidden="true"></i> Localization</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/languages')}}">Languages</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/language/create')}}">Add New Language</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if(Auth::guard('admin')->check() && auth()->guard('admin')->user()->isSuperUser == 1)
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-logs" aria-hidden="true"></i> Logs</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/userLogs')}}">User Logs</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/apiLogs')}}">API Logs</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/paymentLogs')}}">Payment Logs</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="sb2-2">

        <script>
            function triggerFinancePostFromSidebar() {
                $('#financePost').submit();
            }
        </script>

