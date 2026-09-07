<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="/" class="logo"><b>Bigbyte Aqua Ventures</b></a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
				<!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
						@if(isset(Auth::user()->avatar) && Auth::user()->avatar) 
						<img src="{{ asset('/uploads/images/' . Auth::user()->avatar ) }}" class="img-circle user-image" alt="User Avatar" />
						@else
						<img src="{{ asset('/uploads/images/no-avatar.png') }}" class="img-circle user-image" alt="User Avatar" /> 
						@endif
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">@if(isset(Auth::user()->name)){{ Auth::user()->name }}@endif</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- Menu Footer-->
                        <li class="user-footer">
						    <div class="pull-left">
                                <a href="{{url('/profile')}}" class="btn btn-default btn-flat">Profile</a>
						    </div>
                            <div class="pull-right">
                                <a href="{{url('/logout')}}" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
