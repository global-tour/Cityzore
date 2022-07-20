<style>
    .dash-icon-button {
        transition: .5s;
        color: #bbbbbb;
        cursor: pointer;
        font-size: 20px;
        padding-right: 5px;
        padding-left: 5px
    }

    .dash-icon-button:hover {
        color: #000000;
        transition: .5s
    }
</style>
<?php
$user = auth()->user();
$roles = json_decode($user->roles, true);
$permissionsArray = [];
if (!is_null($roles)) {
    foreach ($roles as $role) {
        $permissions = \App\Role::where('name', '=', $role)->first()->permission()->get();
        foreach ($permissions as $permission) {
            array_push($permissionsArray, $permission->name);
        }
    }
}
?>
<div style="height: 100%;" class="container-fluid sb2">
    <div class="row">
        <div class="sb2-1">
            <div class="sb2-12">
                <ul>
                    <li>
                        <h5>
                            {{ Auth::guard('admin')->user()->name }} {{ Auth::guard('admin')->user()->surname }}
                            <span>
                            {{ Auth::guard('admin')->user()->address}}
                            </span>
                        </h5>
                        <br>
                        <i class="dash-icon-button icon-cz-user"
                           onclick="window.location.href='{{url('admin/'.auth()->guard('admin')->user()->id.'/edit')}}'">
                        </i>
                        <i class="dash-icon-button icon-cz-logout"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"></i>
                        <a href="{{url('/adminchat')}}" target="_blank"><i
                                class="dash-icon-button  icon-cz-bell"></i></a>
                    </li>
                </ul>
            </div>
            <div class="sb2-13">
                <ul class="collapsible" data-collapsible="accordion">
                    <li><a href="{{url('/')}}"><i class="icon-cz-dashboard" aria-hidden="true"></i> Dashboard</a></li>
{{--                    <li><a href="{{url('/statistic')}}"><i class="icon-cz-graphics" aria-hidden="true"></i>--}}
{{--                            Statistic</a></li>--}}
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-graphics"
                                                                                   aria-hidden="true"></i> Statistic</a>
                        {{--                        <div class="collapsible-body left-sub-menu" style="display: {{$request==['/user','/user/create','/admin','admin/create','/commissioners']}}block">--}}
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a  href="{{url('/statistic')}}">All Statistics</a>
                                </li>
                                <li>
                                    <a href="{{url('/statistic/barcode-analysis')}}">Barcode Analysis</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-booking" aria-hidden="true"></i> Bookings</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Bookings", $permissionsArray))
                                    <li>
                                        <a href="{{url('/bookings')}}">All Bookings</a>
                                    </li>
                                @endif
                                @if(in_array("Booking Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/booking/create')}}">Create Booking</a>
                                    </li>
                                @endif


                                @if(in_array("Booking Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/meeting/index')}}" target="_blank">Edit Meetings</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{url('/on-goings')}}" target="_blank">On-Goings</a>
                                </li>
                                <li>
                                    <a href="{{ url('/mailer') }}">Bulk Mail</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-barcode"
                                                                                   aria-hidden="true"></i> Barcodes</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Barcodes", $permissionsArray))
                                    <li>
                                        <a href="{{url('/barcodes')}}">All Barcodes</a>
                                    </li>
                                @endif
                                @if(in_array("Barcode Create", $permissionsArray))
                                    <li>
                                        <a href="{{url('/barcodes/create')}}">Add New Barcode</a>
                                    </li>
                                @endif
                                @if(in_array("Create Ticket", $permissionsArray))
                                    <li>
                                        <a href="{{url('/barcode/multiple-ticket')}}">Create Multiple Tickets</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @if(in_array("Option Create", $permissionsArray) || in_array("Product Create", $permissionsArray) || in_array("Product Edit", $permissionsArray))
                        <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-availability"
                                                                                       aria-hidden="true"></i>
                                Availabilities</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li><a href="{{url('/availabilities')}}">All Availabilities</a></li>
                                    <li><a href="{{url('/av/create')}}">Create Availabilities</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-pricing"
                                                                                       aria-hidden="true"></i> Pricings</a>
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
                    @endif
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-product"
                                                                                   aria-hidden="true"></i> Products and
                            Options</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Products", $permissionsArray))
                                    <li>
                                        <a href="{{url('/product?page=1')}}">All Products</a>
                                    </li>
                                @endif
                                @if(in_array("Product Create", $permissionsArray))
                                    <li>
                                        <a href="{{url('/product/create')}}">Create New Product</a>
                                    </li>
                                @endif
                                @if(in_array("Show All Options", $permissionsArray))
                                    <li>
                                        <a href="{{url('/option')}}">All Options</a>
                                    </li>
                                @endif
                                @if(in_array("Option Create", $permissionsArray) || in_array("Product Create", $permissionsArray) || in_array("Product Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/option/create')}}">Create New Option</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment"
                                                                                   aria-hidden="true"></i> External
                            Payment</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All External Payments", $permissionsArray))
                                    <li>
                                        <a href="{{url('/external-payment')}}">External Payments</a>
                                    </li>
                                @endif
                                @if(in_array("External Payment Create", $permissionsArray))
                                    <li>
                                        <a href="{{url('/external-payment/create')}}">Create New External Payment</a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-ticket"
                                                                                   aria-hidden="true"></i> Tickets</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/ticket-type/create')}}">Add New Ticket Type</a>
                                </li>
                                <li>
                                    <a href="{{url('/ticket-type')}}">All Ticket Types</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-percentage"
                                                                                   aria-hidden="true"></i> Special
                            Offers</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Special Offers", $permissionsArray))
                                    <li>
                                        <a href="{{url('/special-offers/')}}">All Special Offers</a>
                                    </li>
                                @endif
                                @if(in_array("Special Offer Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/special-offers/create')}}">Create Special Offer</a>
                                    </li>
                                @endif
                                @if(in_array("Show All Coupons", $permissionsArray))
                                    <li>
                                        <a href="{{url('/coupons')}}">All Coupons</a>
                                    </li>
                                @endif
                                @if(in_array("Coupon Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/coupon/create')}}">Create New Coupon</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li><a href="{{url('/category')}}"><i class="icon-cz-hamburger" aria-hidden="true"></i> Categories</a></li>
                    <li><a href="{{url('/platform')}}"><i class="icon-cz-umbrella" aria-hidden="true"></i> Platforms</a></li>
                    <li><a href="{{url('/mails')}}"><i class="icon-cz-mail" aria-hidden="true"></i> Mails</a></li>

                    <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-user"
                                                                                   aria-hidden="true"></i> Users</a>
                        {{--                        <div class="collapsible-body left-sub-menu" style="display: {{$request==['/user','/user/create','/admin','admin/create','/commissioners']}}block">--}}
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a class="active" href="{{url('/user')}}">All Users</a>
                                </li>
                                @if(in_array("User Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/user/create')}}">Add New user</a>
                                    </li>
                                @endif
                                @if(in_array("Admin Create/Edit/Delete", $permissionsArray))
                                    <li>
                                        <a href="{{url('admin')}}">All Admins</a>
                                    </li>
                                    <li>
                                        <a href="{{url('admin/create')}}">Add New Admin</a>
                                    </li>
                                @endif
                                @if(in_array("User Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/commissioners')}}">All Commissioners</a>
                                    </li>
                                @endif
                                @if(in_array("Show All Suppliers", $permissionsArray))
                                    <li>
                                        <a href="{{url('/supplier')}}">All Suppliers</a>
                                    </li>
                                @endif
                                @if(in_array("Supplier Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/supplier/create')}}">Add New Supplier</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @if(in_array("User Management", $permissionsArray))
                        <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-user"
                                                                                       aria-hidden="true"></i> User
                                Management</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/roles')}}">All Roles</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/role/create')}}">Add New Role</a>
                                    </li>
                                    @if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->isSuperUser == 1)
                                        <li>
                                            <a href="{{url('permissions')}}">All Permissions</a>
                                        </li>
                                        <li>
                                            <a href="{{url('permission/create')}}">Add New Permission</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(in_array("User Management", $permissionsArray))
                        <li><a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-user"
                                                                                       aria-hidden="true"></i> Guide
                                Management</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('guide/index')}}">General Index</a>
                                    </li>

                                    <li>
                                        <a href="{{url('guide/planning')}}">Planning</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(in_array("Voucher Template Access", $permissionsArray))
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment"
                                                                                   aria-hidden="true"></i>
                            Voucher Templates</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>

                                    <li>
                                        <a href="{{url('/voucher-template')}}">All Voucher Templates</a>
                                    </li>


                                    <li>
                                        <a href="{{url('/voucher-template/create')}}">Create Voucher Template</a>
                                    </li>

                            </ul>
                        </div>

                    </li>
                    @endif

                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment"
                                                                                   aria-hidden="true"></i> Create
                            Voucher</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Vouchers", $permissionsArray))
                                    <li>
                                        <a href="{{url('/vouchers')}}">All Vouchers</a>
                                    </li>
                                @endif
                                @if(in_array("Voucher Create", $permissionsArray))
                                    <li>
                                        <a href="{{url('/voucher/create')}}">Create Voucher</a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </li>
                    @if(in_array("Finance Show/Download", $permissionsArray))
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-payment"
                                                                                       aria-hidden="true"></i>
                                Finance</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/finance')}}">Finance</a>
                                    </li>

                                    <li>
                                        <a href="{{url('/bills')}}">Bills</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if(in_array("Show All Comments", $permissionsArray))
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-comment"
                                                                                       aria-hidden="true"></i> Comments</a>
                            <div class="collapsible-body left-sub-menu">
                                <ul>
                                    <li>
                                        <a href="{{url('/comments')}}">Comments</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-instagram"
                                                                                   aria-hidden="true"></i> Gallery</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Photos", $permissionsArray))
                                    <li>
                                        <a href="{{url('/gallery')}}">Photo Gallery</a>
                                    </li>
                                @endif
                                @if(in_array("Photo Upload", $permissionsArray))
                                    <li>
                                        <a href="{{url('/gallery/create')}}">Add New Photo</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/gallery/cityPhotos')}}">City Photos</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/gallery/addCityPhoto')}}">Add Photo to a City</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-location"
                                                                                   aria-hidden="true"></i>
                            Attractions</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Attractions", $permissionsArray))
                                    <li>
                                        <a href="{{url('/attraction')}}">All Attractions</a>
                                    </li>
                                @endif
                                @if(in_array("Attraction Create/Edit/Delete", $permissionsArray))
                                    <li>
                                        <a href="{{url('/attraction/create')}}">Add New Attraction</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-copy"
                                                                                   aria-hidden="true"></i> Blog
                            Posts</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show All Blog Post", $permissionsArray))
                                    <li>
                                        <a href="{{url('/blog')}}">All Blog Posts</a>
                                    </li>
                                @endif
                                @if(in_array("Blog Create/Edit", $permissionsArray))
                                    <li>
                                        <a href="{{url('/blog/create')}}">Add New Blog Posts (Cityzore)</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/blog/createPCT')}}">Add New Blog Posts (Pariscitytours)</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-floppy"
                                                                                   aria-hidden="true"></i> FAQs</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                <li>
                                    <a href="{{url('/faq')}}">All FAQs</a>
                                </li>
                                <li>
                                    <a href="{{url('/faq/create')}}">Create New FAQ</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-management"
                                                                                   aria-hidden="true"></i> Configuration</a>
                        <div class="collapsible-body left-sub-menu">
                            <ul>
                                @if(in_array("Show Config", $permissionsArray))
                                    <li>
                                        <a href="{{url('/config')}}">Update Configuration</a>
                                    </li>
                                @endif
                                @if(in_array("Show General Config", $permissionsArray))
                                    <li>
                                        <a href="{{url('/general-config')}}">General Configuration</a>
                                    </li>
                                @endif
                                 @if(in_array("Show Cache Config", $permissionsArray))
                                    <li>
                                        <a href="{{url('/cache-config')}}">Cache Configuration</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                    @if(in_array("Language Show/Create", $permissionsArray))
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-localization"
                                                                                       aria-hidden="true"></i>
                                Localization</a>
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
                    @if(!is_null(auth()->guard('admin')->user()->roles) || in_array('Super Admin', json_decode(auth()->guard('admin')->user()->roles, true)))
                        <li>
                            <a href="javascript:void(0)" class="collapsible-header"><i class="icon-cz-logs"
                                                                                       aria-hidden="true"></i> Logs</a>
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
                                    <li>
                                        <a href="{{url('/errorLogs')}}">Error Logs</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/meetingLogs')}}">Meeting Logs</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/customerLogs')}}">Mobile Customer Logs</a>
                                    </li>
                                    <li>
                                        <a href="{{url('/mail-information')}}">Mail Information</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="sb2-2">
