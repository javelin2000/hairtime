<nav class="navbar-default navbar-static-side" role="navigation" style="position: fixed">
    <!-- sidebar-collapse -->
    <div class="sidebar-collapse">
        <!-- side-menu -->
        <ul class="nav" id="side-menu">
            <li>
                <!-- user image section-->
                <div class="user-section">
                    <div class="user-section-inner">
                        <img src="@if ($admin['url'] != null){{$admin['url']}}@else{{'/img/mystery-man.png'}} @endif"
                             class="img-circle" alt="admin logo">
                    </div>
                    <div class="user-info">
                        <div><h5>{{$admin['first_name'].' '.$admin['last_name']}}</h5></div>
                        <div class="user-text-online">
                            <span class="user-circle-online btn btn-success btn-circle "></span>&nbsp;Online
                        </div>
                    </div>
                </div>
                <!--end user image section-->
            </li>

            <li @if ($menu=='salons') class="selected" @endif>
                <a href="/admin"><i class="fa fa-list fa-fw"></i>Salon's list</a>
            </li>
            <li @if ($menu=='workers') class="selected" @endif>
                <a href="/admin"><i class="fa fa-scissors fa-fw"></i>Worker's list</a>
            </li>
            <li @if ($menu=='customers') class="selected" @endif>
                <a href="/admin"><i class="fa fa-list fa-fw"></i>Customer's list</a>
            </li>
            <li @if ($menu=='comments') class="selected" @endif>
                <a href="/admin/comments"><i class="fa fa-comment fa-fw"></i>Comments</a>
            </li>

        </ul>
        <!-- end side-menu -->
    </div>
    <!-- end sidebar-collapse -->
</nav>