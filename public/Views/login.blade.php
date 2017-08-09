@extends('layouts.template')

@section('login')
<div class="container">

    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center logo-margin ">
            <img src="/img/image.jpg" style="width: 100%" alt=""/>
        </div>
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <form method="post" action="/admin/login">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="login" name="login" type="text" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="password" name="password" type="password"
                                       value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                </label>
                                <label style="align-items:right">
                                    <a href="/admin/fogot">Forgot password...</a>
                                </label>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" class="btn btn-lg btn-success btn-block" value="Войти" name="log_in"/>
                            <!-- <a class="btn btn-lg btn-success btn-block" type="submit" name="log_in"  >Login</a> -->
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop