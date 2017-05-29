<!DOCTYPE html>
<?php session_start() ?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HairTime | Admin Panel</title>
    <link href="/plugins/bootstrap/bootstrap.css" rel="stylesheet"/>

    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet"/>
    <!--
    <link href="/plugins/pace/pace-theme-big-counter.css" rel="stylesheet" />-->
    <link href="/css/style.css" rel="stylesheet"/>
    <link href="/css/main-style.css" rel="stylesheet"/>
    <!-- Page-Level CSS -->

</head>

<body>
<!--  wrapper -->
<div id="wrapper">
    <!-- navbar top -->
@include('layouts.headconfirm')
<!-- end navbar top -->

    <div id="page-wrapper">

        <div class="row">
            <!-- Page Header -->
            <div class="col-lg-12">
                @yield('content')
            </div>
            <!--End Page Header -->
        </div>
    </div>
    <!-- end page-wrapper -->
</div>
<!-- end wrapper -->


</body>

</html>
