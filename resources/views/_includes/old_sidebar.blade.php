<?php //echo Request::segment(1); die; ?>
<?php //echo Route::current()->getActionName(); die; ?>
<?php //echo Route::getCurrentRoute()->getActionName(); die; ?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
            @if(isset(Auth::user()->avatar) && Auth::user()->avatar) 
                <img src="/uploads/images/{{ Auth::user()->avatar }}" class="img-circle" alt="User Avatar" /> 
            @else
                <img src="{{ asset('/uploads/images/no-avatar.png') }}" class="img-circle" alt="User Avatar" /> 
            @endif
            </div>
            <div class="pull-left info">
                <p>@if(isset(Auth::user()->name)){{ Auth::user()->name }}@else Name @endif</p>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
        
            <li class="{{ add_active_class(Request::segment(1), 'company') }}">
				<a href="{{ url('/company') }}"><span>Company</span></a>
			</li>
            <li class="{{ add_active_class(Request::segment(1), 'user') }}">
				<a href="{{ url('/user') }}"><span>Users</span></a>
			</li>
            <li class="treeview {{ add_active_class(Request::segment(1), ['vendor', 'supplier-invoice', 'cash-payment-voucher']) }}">
                <a href="{{ url('/vendor') }}"><span>Vendors</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class(Request::segment(1), 'supplier-invoice') }}">
                        <a href="{{ url('/supplier-invoice') }}"><span>Supplier's Invoice</span></a>
                    </li>
                    <li class="{{ add_active_class($route_uri, 'cash-payment-voucher') }}">
						<a href="{{ url('/') }}"><span>Cash Payment Voucher</span></a>
					</li>
                </ul>
            </li>
            
            <li class="treeview {{ add_active_class(Request::segment(1), ['customer', 'product-list', 'cash-invoice', 'credit-invoice', 'collection-receipt', 'service-list', 'billing-invoice', 'receipt']) }}">
                <a href="{{ url('/customer') }}"><span>Customers</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class($route_uri, ['product-list', 'cash-invoice', 'credit-invoice', 'collection-receipt']) }}">
                        <a href="javascript:;"><span>Trading</span></a>
                        <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li class="{{ add_active_class($route_uri, 'product-list') }}"><a href="{{ url('/product-list') }}"><span>Product List</span></a></li>
                            <li class="{{ add_active_class($route_uri, 'cash-invoice') }}"><a href="{{ url('/') }}"><span>Cash Invoice</span></a></li>
                            <li class="{{ add_active_class($route_uri, 'credit-invoice') }}"><a href="{{ url('/') }}"><span>Credit Invoice</span></a></li>
                            <li class="{{ add_active_class($route_uri, 'collection-receipt') }}"><a href="{{ url('/collection-receipt') }}"><span>Collection Receipt</span></a></li>
                        </ul>
                    </li>
                    <li class="{{ add_active_class($route_uri, ['service-list', 'billing-invoice', 'receipt']) }}">
                        <a href="javascript:;"><span>Services</span></a>
                        <a href="#" class="sub-sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li class="{{ add_active_class($route_uri, 'service-list') }}"><a href="{{ url('/service-list') }}"><span>List of Services</span></a></li>
                            <li class="{{ add_active_class($route_uri, 'billing-invoice') }}"><a href="{{ url('/') }}"><span>Billing Invoice</span></a></li>
                            <li class="{{ add_active_class($route_uri, 'receipt') }}"><a href="{{ url('/') }}"><span>Official Receipt</span></a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            
            <li class="treeview {{ add_active_class(Request::segment(1), ['employee', 'list-employees']) }}">
                <a href="javascript:;"><span>Employee</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class($route_uri, 'list-employees') }}"><a href="{{ url('/list-employees') }}"><span>List of employees</span></a></li>
                    <li class="{{ add_active_class($route_uri, 'payroll') }}"><a href="#"><span>Payroll</span></a></li>
                </ul>
            </li>
            
            <li class="treeview {{ add_active_class(Request::segment(1), ['chart-account', 'chart-account-type']) }}">
                <a href="{{ url('/chart-account') }}"><span>Chart of Acct.</span></a>
                <a href="#" class="sub-dropdown"><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li class="{{ add_active_class(Request::segment(1), 'chart-account-type') }}"><a href="{{ url('/chart-account-type') }}"><span>Account Type</span></a></li>
                </ul>
            </li>
            
            <li class="{{ add_active_class(Request::segment(1), 'tax') }}"><a href="{{ url('/tax') }}"><span>Tax Settings</span></a></li>
            <li class="{{ add_active_class(Request::segment(1), 'journal') }}"><a href="{{ url('/') }}"><span>Journal</span></a></li>
            <li class="{{ add_active_class(Request::segment(1), 'ledger') }}"><a href="{{ url('/') }}"><span>Ledger</span></a></li>
            <li class="{{ add_active_class(Request::segment(1), 'reports') }}"><a href="{{ url('/') }}"><span>Reports</span></a></li>
            
        </ul><!-- /.sidebar-menu -->
        
    </section>
    <!-- /.sidebar -->
</aside>
