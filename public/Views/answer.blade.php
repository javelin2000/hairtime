@extends('layouts.main')

@section('content')
    <style>
        .btn {
            margin-left: 10px;
            margin-top: 5px;
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

        textarea {
            width: 95%;
            min-height: 75px;
            height: 125px;
            resize: vertical;
            margin: auto 10px;

        }

        #tmessage {
            width: 75%;
            text-align: right;
        }

        .bubble {
            position: relative;
            width: 95%;
            padding: 25px;
            background: #96FFFF;
            -webkit-border-radius: 38px;
            -moz-border-radius: 38px;
            border-radius: 38px;
        }

        .bubble:after {
            content: '';
            position: absolute;
            border-style: solid;
            border-width: 20px 20px 0;
            border-color: #96FFFF transparent;
            display: block;
            width: 0;
            z-index: 1;
            bottom: -20px;
            left: 15%;
        }

        .bubble-answ {
            position: relative;
            width: 95%;
            padding: 25px;
            background: #96FFA4;
            -webkit-border-radius: 38px;
            -moz-border-radius: 38px;
            border-radius: 38px;
        }

        .bubble-answ:after {
            content: '';
            position: absolute;
            border-style: solid;
            border-width: 20px 20px 0;
            border-color: #96FFA4 transparent;
            display: block;
            width: 0;
            z-index: 1;
            bottom: -20px;
            left: 85%;
        }

        form {
            border-radius: 38px;
            padding: 25px;

        }
    </style>
    <h3>Answer to message</h3><br>
    <div class="container-fluid">
        <div class="container-fluid">
            <p style="margin-left: 25px"><b>Messages with user: {{$message['name']}}</b></p>
            <div class="row">
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11" style="text-align: right">
                    <div class="bubble">
                        {{$message['message']}}
                        <p style="margin-top: 5px">{{date('d-m-Y', mktime($message['create_at']))}}</p>

                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
                </div>
            </div>
            @foreach($answers as $answer)
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
                    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11" style="text-align: right">
                        <br><br>
                        <div class="bubble-answ">
                            {{$answer['message']}}<br>
                            {{date('d-m-Y', mktime($answer['create_at']))}}
                        </div>
                    </div>
                </div>
            @endforeach
            <div>
                <br><br>
                <form name="form" method="POST" action={{"/admin/message/".$message['message_id']}}>
                    <textarea name="textanswer" maxlength="500" autofocus></textarea>
                    <p>
                        <button class="btn btn-primary" id="btn" type="submit" name="operator" value="Send">
                            Send
                        </button>
                        <button class="btn btn-danger" type="submit" name="operator" value="Cancel">Cancel</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
@stop