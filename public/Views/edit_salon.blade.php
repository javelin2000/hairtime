@extends('layouts.main')

@section('content')
    <h3>Edit Salon</h3><br>
    <div class="container-fluid">
        <form class="form-horizontal" name="form" method="post" action={{"/admin/salon/".$salon['salon_id']}}>
            <table class="table" style="width: 100%">
                <tr>
                    <td style="vertical-align: middle;">
                        Salon ID: <b>{{$salon['salon_id']}}</b>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="first_name">First name</label>
                            <div class="controls">
                                <input type="text" id="first_name" name="first_name" placeholder="Enter first name here"
                                       value="{{$salon['first_name']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="last_name">Last name</label>
                            <div class="controls">
                                <input type="text" id="last_name" name="last_name" placeholder="Enter last name here"
                                       value="{{$salon['last_name']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="business_name">Business name</label>
                            <div class="controls">
                                <input type="text" id="business_name" name="business_name"
                                       placeholder="Enter business name here"
                                       value="{{$salon['business_name']}}">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="founded_in">Founded</label>
                            <div class="controls">
                                <input type="text" id="founded_in" name="founded_in"
                                       placeholder="Enter founded year here"
                                       value="{{$salon['founded_in']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="staff_number">Staff number</label>
                            <div class="controls">
                                <input type="text" id="staff_number" name="staff_number"
                                       placeholder="Enter staff number here"
                                       value="{{$salon['staff_number']}}">
                            </div>
                        </div>
                    </td>
                    <td style="vertical-align: middle;">
                        Salon's rating: <b>{{$salon['rating']}}</b>
                    </td>
                    <td style="vertical-align: middle;">
                        Salon's comments number: <b>{{$salon['comments_number']}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="city">City</label>
                            <div class="controls">
                                <input type="text" id="city" name="city" placeholder="Enter Salon's city here"
                                       value="{{$salon['city']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="address">Address</label>
                            <div class="controls">
                                <input type="text" id="address" name="address" placeholder="Enter Salon's address here"
                                       value="{{$salon['address']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="lat">Latitude</label>
                            <div class="controls">
                                <input type="text" id="lat" name="lat" placeholder="Enter Salon's Latitude here"
                                       value="{{$salon['lat']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="lng">Longitude</label>
                            <div class="controls">
                                <input type="text" id="lng" name="lng" placeholder="Enter Salon's Longitude  here"
                                       value="{{$salon['lng']}}">
                            </div>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="phone">Phone</label>
                            <div class="controls">
                                <input type="text" id="phone" name="phone" placeholder="Enter phone here"
                                       value="{{$salon['phone']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="logo">Logo URL</label>
                            <div class="controls">
                                <input type="text" id="logo" name="logo" placeholder="Enter Logo URL here"
                                       value="{{$salon['logo']}}">
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="control-group">
                            <label class="control-label" for="waze">WAZE URL</label>
                            <div class="controls">
                                <input type="text" id="waze" name="waze" placeholder="Enter WAZE URL here"
                                       value="{{$salon['waze']}}">
                            </div>
                        </div>
                    </td>
                    <td colspan="2" style="vertical-align: middle;">
                        Salon's status: <b>{{$salon['status']}}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <br>
                        <p>
                            <button class="btn btn-large btn-primary" type="submit"
                                    style="width:250px;align-items: center" name="Save">Save
                            </button>
                        </p>
                    </td>
                </tr>
            </table>


        </form>
    </div>
    <pre>{{var_dump($req)}}</pre>
@stop