@extends('layouts.main')

@section('content')
    <h3>Salon's list</h3><br>
    <div class="container-fluid">
        <table class="table">
            <tr>
                <td>
                    <form name="search" action="/admin" method="post">
                        <div class="form-inline form-search">
                            <label for="search">Search Salon</label>
                            <input type="text" class="search-query" style="width:250px" id="search" name="search"
                                   placeholder="enter Salon's name or ID">
                        </div>
                    </form>
                </td>
            </tr>
        </table>
        <hr>
        <table id='salons' class="table-striped" cellspacing="0" style="width: 100%">
            <thead>
            <tr>
                <th style="align-content:center ">Salon ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Business Name</th>
                <!-- <td>Founded in</td>
                 <td>Staff Number</td>
                 <td>Rating</td>
                 <td>Comments Number</td>-->
                <th>Phone</th>
                <!--<td>City</td>
                <td>Address</td>
                <td>LAT</td>
                <td>LNG</td>
                <td>Logo</td>-->
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($salons as $salon)
                <tr>
                    <td>{{$salon['salon_id']}}</td>
                    <td>{{$salon['first_name']}}</td>
                    <td>{{$salon['last_name']}}</td>
                    <td>{{$salon['business_name']}}</td>
                <!-- <td>{{$salon['founded_in']}}</td>
                                <td>{{$salon['staff_number']}}</td>
                                <td>{{$salon['rating']}}</td>
                                <td>{{$salon['comments_number']}}</td>-->
                    <td>{{$salon['phone']}}</td>
                <!--<td>{{$salon['city']}}</td>
                                <td>{{$salon['address']}}</td>
                                <td>{{$salon['lat']}}</td>
                                <td>{{$salon['lng']}}</td>
                                <td><img src="{{$salon['logo']}}" class="img-circle" style="width: 65px;height: 65px;"></td>-->
                    @if ($salon['status'] == 'Active')
                        <form name="form-inline" method="post" action={{"/admin/salon/".$salon['salon_id']}}>
                            <INPUT TYPE="HIDDEN" NAME="status" VALUE="Inactive">
                            <td>
                                <p>
                                    <button class="btn btn-warning" type="submit" name="Deactivate"
                                            style="width: 100px">Deactivate
                                    </button>
                                    <button class="btn btn-success" type="submit" name="Edit" style="width: 75px">Edit
                                    </button>
                                    <button class="btn btn-danger" type="submit" name="Delete">Delete</button>
                                </p>
                            </td>
                        </form>
                    @else
                        <form class="form-inline" name="form" method="post"
                              action={{"/admin/salon/".$salon['salon_id']}}>
                            <INPUT TYPE="HIDDEN" NAME="status" VALUE="Active">
                            <td>
                                <p>
                                    <button class="btn btn-primary" type="submit" style="width: 100px" name="Activate">
                                        Activate
                                    </button>
                                    <button class="btn btn-success" type="submit" name="Edit" style="width: 75px">Edit
                                    </button>
                                    <button class="btn btn-danger" type="submit" name="Delete">Delete</button>
                                </p>
                            </td>
                        </form>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop