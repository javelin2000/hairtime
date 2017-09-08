@extends('layouts.main')

@section('content')
    <style>
        .btn {

        }

        .btn-warning {
            width: 120px;
        }

        .btn-primary {
            width: 120px;
        }

        th {
            text-align: center;
        }

        #tcenter {
            text-align: center;
            margin-right: 5px;
        }

        #tbutton {
            max-width: 100px;
        }
    </style>
    <h3>Messages</h3><br>
    <div class="container-fluid">
        <table class="table">
            <tr>
                <td>
                    <form name="search" action="/admin/message" method="GET">
                        <div class="form-inline form-search">
                            <label for="search">Search user</label>
                            <input type="text" class="search-query" style="width:250px" id="search" name="search"
                                   placeholder="enter user name here">
                        </div>
                    </form>
                </td>
            </tr>
        </table>
        <hr>
        <div class="container-fluid">
            <table id='messages' class="table-striped" cellspacing="0" style="width: 100%">
                <thead>
                <tr style="text-align: center">
                    <th>Name</th>
                    <th>Message</th>
                    <th>Receive</th>
                    <th>Actions</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($allmessages as $message)
                    <tr>
                        @if($message['answer_at'] == null)
                            <td id="tcenter"><b>{{$message['name']}}</b></td>
                            <td style="width: 45%"><b>{{$message['message']}}</b></td>
                            <td id="tcenter"><b>{{date('d-m-Y', mktime($message['create_at']))}}</b></td>
                        @else
                            <td id="tcenter">{{$message['name']}}</td>
                            <td>{{$message['message']}}</td>
                            <td id="tcenter">{{date('d-m-Y', mktime($message['create_at']))}}</td>
                        @endif
                        <td id="tbutton">
                            <form name="form-inline" method="GET" action={{"/admin/message/".$message['message_id']}}>
                                @if ($message['answer_at'] == null)
                                    <INPUT TYPE="HIDDEN">
                                    <p>
                                        <button class="btn btn-warning" id="btn" type="submit" name="operator"
                                                value="Answer">
                                            Answer
                                        </button>
                                        <button class="btn btn-danger" type="submit" name="operator" value="Delete">
                                            Delete
                                        </button>
                                    </p>
                                @else
                                    <INPUT TYPE="HIDDEN">
                                    <p>
                                        <button class="btn btn-primary" type="submit" name="operator" value="Answer"
                                        >Answer again
                                        </button>
                                        <button class="btn btn-danger" type="submit" name="operator" value="Delete">
                                            Delete
                                        </button>
                                    </p>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@stop