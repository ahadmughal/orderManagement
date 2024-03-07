<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VR1 Global Admin Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">

        <router-view>
            <Topbar />
        </router-view>


        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="index3.html" class="brand-link text-center">
                <img src="/vr1.png" alt="VR1 Global"
                    class="brand-image img-circle elevation-3" style="opacity: .8"> 
                <span class="brand-text font-weight-light ">VR1 Global</span>
            </a>

            <div class="sidebar">

                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image verticalCenter">
                        <img src="{{auth()->user()->avatar}}"
                            class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info verticalCenter">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">


                        <li class="nav-item">
                            <router-link to="/admin/dashboard" active-class="active" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                    {{-- <span class="right badge badge-danger">New</span> --}}
                                </p>
                            </router-link>
                        </li>

                        <router-view>
                            <Sidebar />
                        </router-view>

                        <li class="nav-item">

                            <form method="POST" action="{{ route('logout') }}" class="nav-link">
                                @csrf
                                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" >
                                    <i class="nav-icon fas fa-sign-out-alt"></i>
                                    <p>
                                        Logout
                                    </p>
                                </a>
                            </form>
                        </li>


                    </ul>
                </nav>

            </div>

        </aside>

        <div class="content-wrapper">

            <router-view></router-view>

        </div>


        <aside class="control-sidebar control-sidebar-dark">

            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>

        </aside>


        <footer class="main-footer">

          

            <strong>Copyright &copy; 2023 <a href="https://cloudtica.com">Cloudtica</a>.</strong> All rights reserved.

        </footer>
    </div>




</body>

</html>
