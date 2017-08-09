@extends('layouts.main')

@section('content')
    <h3>Comments's list</h3><br>
    <div class="container-fluid">
        <table class="table">
            <tr>
                <td>
                    <form name="search" action="/admin/comments" method="post">
                        <div class="form-inline form-search">
                            <label for="search">Search comment</label>
                            <input type="text" class="search-query" style="width:250px" id="search" name="search"
                                   placeholder="enter Comment for search">
                        </div>
                    </form>
                </td>
            </tr>
        </table>
        <hr>
        <div class="container-fluid" style="visibility: {{$vis}}">
            <table id='salons' class="table-striped" cellspacing="0" style="width: 100%">
                <thead>
                <tr>
                    <th>Comment ID</th>
                    <th>Salon ID</th>
                    <th>Created at</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($comments as $comment)
                    <tr>
                        <td>{{$comment['comment_id']}}</td>
                        <td>{{$comment['salon_id']}}</td>
                        <td>{{$comment['created_at']}}</td>
                        <form class="form-inline" name="form" method="post"
                              action={{"/admin/comments/".$comment['comment_id']}}>
                            <td style="vertical-align: middle">
                                <textarea title="comment" rows="3" name="comment"
                                          style="width: 350px;resize:none">{{$comment['body']}}</textarea>
                            </td>
                            <td style="vertical-align: middle">
                                <button class="btn btn-danger " type="submit" name="operator" value="Delete"
                                        style="width: 100px;vertical-align: middle">Delete
                                </button>
                                <button class="btn btn-success" type="submit" name="operator" value="Edit"
                                        style="width: 75px">Edit
                                </button>
                            </td>
                        </form>

                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@stop