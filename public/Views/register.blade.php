@extends('layouts.template')
@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-offset-3">
            <form action="admin/register" method="post">
                <div class="form-group">
                    <br>
                    <label>User first name</label>
                    <input class="form-control" name="first_name" placeholder="Enter user first name" type="text">
                    <label>User last name</label>
                    <input class="form-control" name="last_name" placeholder="Enter user last name" type="text">
                    <label>User email</label>
                    <input class="form-control" name="email" placeholder="Enter user email" type="email">
                    <label>User login</label>
                    <span></span>
                    <br>
                    <input class="form-control" name="login" placeholder="Enter user login" type="text">
                    <label>User password</label>
                    <input class="form-control" name="password" placeholder="Enter user password" type="password">
                </div>
            </form>
        </div>
    </div>
@show