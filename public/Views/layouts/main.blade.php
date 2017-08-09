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
    <style>
        th {
            cursor: pointer;
        }

        th:hover {
            background: dimgray;
        }
    </style>

</head>

<body>
<!--  wrapper -->
<div id="wrapper">
    <!-- navbar top -->
@include('layouts.head')
<!-- end navbar top -->

    <!-- navbar side -->
@include('layouts.side')
<!-- end navbar side -->
    <!--  page-wrapper -->
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

<!-- Core Scripts - Include with every page -->
<script src="/plugins/jquery-1.10.2.js"></script>
<script src="/plugins/bootstrap/bootstrap.min.js"></script>
<script src="/plugins/metisMenu/jquery.metisMenu.js"></script>

</body>

</html>
