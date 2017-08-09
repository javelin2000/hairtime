@extends('layouts.main')

@section('content')
    <h3>Admin Profile</h3><br>
    <div class="container-fluid" style="width: 95%">
        @if ($edit)
            <from name="profile" method="POST" action="/admin/profile/{{$admin->admin_id}}">
                <table class="table-bordered" style="width: 80%">
                    <tr>
                        <td rowspan=2 align="center">
                            <p><img src="{{$admin->url}}" width="150px" height="150px"></p><br>
                            <input type="file" name="file" placeholder="select new file">
                        </td>
                        <td><p>ID: {{$admin->admin_id}}</p></td>
                        <td><p><label for="login">Login:</label><input type="text" value="{{$admin->login}}" id="login"
                                                                       name="login" placeholder="input login here"></p>
                            <p><label for="pass">Password:</label><input type="password" value="{{$admin->password}}"
                                                                         id="pass" name="password"
                                                                         placeholder="input pasword here"></p>
                        </td>
                    </tr>
                    <tr>
                        <td><p><label for="first_name">First name: </label><input name="first_name" id="first_name"
                                                                                  value="{{$admin->first_name}}"
                                                                                  placeholder="enter first name here">
                            </p></td>
                        <td><p><label for="last_name">First name: </label><input name="last_name" id="last_name"
                                                                                 value="{{$admin->last_name}}"
                                                                                 placeholder="enter last name here"></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=3><br>
                            <p><label for="email">Email: </label><input name="email" , id="email" ,
                                                                        value="{{$admin->email}}"
                                                                        placeholder="enter email here"></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=3>
                            <p>
                                <button type="submit" class="btn btn-primary" value="submit">SUBMIT</button>
                                <button type="reset" value="Reset" class="btn">RESET</button>
                            </p>
                        </td>
                    </tr>
                </table>
            </from>
        @else
            <table class="table-bordered" style="width: 80%">
                <tr>
                    <td rowspan=2 align="center"><p><img src="{{$admin->url}}" width="150px" height="150px"></p></td>
                    <td><p>ID: {{$admin->admin_id}}</p></td>
                    <td><p>Login: {{$admin->login}}</p></td>
                </tr>
                <tr>
                    <td><p>First name: {{$admin->first_name}}</p></td>
                    <td><p>Last name: {{$admin->last_name}}</p></td>
                </tr>
                <tr>
                    <td colspan=3><p>Email: {{$admin->email}}</p></td>
                </tr>
                <tr>
                    <td colspan=3><p><a href="/admin/profile/{{$admin->admin_id}}" class="btn btn-primary"
                                        style="width: 150px">Edit...</a></p></td>
                </tr>
            </table>
        @endif
    </div>


@stop