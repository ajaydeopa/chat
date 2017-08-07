@extends('layouts.app')

<style type="text/css">
  .time {
    color: grey;
    font-size: 12
  }

  .svg {
    margin-left: 300;
    margin-top: 110;
  }

  .members {
    margin-left: 10
  }

  .list-group {
    height: 125px;
    overflow-y: scroll;
  }
</style>

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-3">
      <div class="panel panel-default"><div class="panel-heading">Hello <B>{{$user->name}}</B></div></div>

      <!-- start groups -->
        <div class="panel panel-default">
          <div class="panel-heading">
            Groups
            <p class="pull-right" style="cursor: pointer" id="groups"><B>+</B></p>
          </div>

          <div class="panel-body">
            <ul class="list-group" id="group-list">
              @foreach($channels as $group)
                <li class="list-group-item channel" gid='{{$group->id}}' type='{{$group->type}}' style="cursor: pointer">{{$group->channel}}</li>
              @endforeach
            </ul>
          </div>
        </div>
      <!-- end groups -->

      <!-- start friends -->
        <div class="panel panel-default">
          <div class="panel-heading">
            Friends
            <p class="pull-right" style="cursor: pointer" id="friends"><B>+</B></p>
          </div>

          <div class="panel-body">
            <ul class="list-group" id="friend-list">
              @foreach($friends as $group)
                <li class="list-group-item channel" gid='{{$group->id}}' type='{{$group->type}}' style="cursor: pointer">
                  @if( $group->detail->name != '' )
                    {{$group->detail->name}}
                  @else
                    {{$group->detail->email}}
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      <!-- end friends -->
    </div>

    <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          Messages
          <button class="btn btn-default pull-right btn-sm members" id="button-member" style="display: none">
            <small>Add member</small>
          </button>
          <button class="btn btn-default pull-right btn-sm members" id="member-list" style="display: none">
            <small>All members</small>
          </button>
        </div>

        <div class="panel-body" id="all">
          @foreach($all as $channel)
            <div id="chat{{$channel->id}}" style="overflow-y:scroll; height:400px; display: none">
              <img src='{{url("images/loader.svg")}}' class="svg">
            </div>
          @endforeach
        </div>
      </div>
      <form id="message-form">
        <div class="form-group">
          <input type="text" class="form-control" id="message" autofocus required>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="group-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title">Create new group</h5>
      </div>
      <div class="modal-body">
        <form id="group-form">
          {{ csrf_field() }}
          <strong id="error" style="color: red; font-size: 12px"></strong>
          <div class="form-group">
            <input type="text" class="form-control" name="channel" placeholder="Group name" autofocus>
          </div>
          <div class="form-group">
            <input type="submit" class="form-control" value="Create">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="friend-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title">Add new friend</h5>
      </div>
      <div class="modal-body">
        <form id="friend-form">
          {{ csrf_field() }}
          <strong id="error" style="color: red; font-size: 12px"></strong>
          <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Email" autofocus>
          </div>
          <div class="form-group">
            <input type="submit" class="form-control" value="Add">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="member-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title">Add new member</h5>
      </div>
      <div class="modal-body">
        <form id="member-form">
          {{ csrf_field() }}
          <strong id="error" style="color: red; font-size: 12px"></strong>
          <div class="form-group">
            <input type="email" class="form-control" id="memail" placeholder="Email" autofocus required>
          </div>
          <div class="form-group">
            <input type="submit" class="form-control" value="Add">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="all-member-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title">Group members</h5>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="all-mem-list">
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('footer')
  <script>
    var latest;
    var action = new Array();

    $('#member-list').click(function(){
      $('#all-member-modal').modal();
      url = '{{url("member/list")}}';
      $.post(url, {id: latest}, function(users){
        content = '';
        for( i = 0; i < users.length; i++ )
          content += '<li class="list-group-item">'+ users[i]['name'] +' ('+ users[i]['email'] +')</li>';
        $('#all-mem-list').html(content);
      });
    });

    $('#button-member').click(function(){
      $('#member-modal').modal();
      $('#memail').val('');
    });

    $(function(){
      @if( $friends->count() > 0 )
        latest = '{{$friends->first()->id}}';
        $('#chat'+latest).show();
        action.push(latest);
        $('.channel[gid="'+latest+'"]').addClass('active');
        messages(latest);
        call(latest);
      @endif
    });

    function messages(id)
    {
      url = '{{url("get/messages")}}';
      $.get(url, {id: id}, function(msg){
        content = '';

        for( i = 0; i < msg.length; i++ )
          content += '<div class="well well-sm"><p><B>'+ msg[i]['name'] +'</B><span class="time">&nbsp;&nbsp;&nbsp;'+ msg[i]['time'] +'</span></p><p>'+msg[i]['message']+'</p></div>';
        $('#chat'+id).html(content).scrollTop(1E10);
      });
    }

    $('#message-form').submit(function(e){
      e.preventDefault();
      url = '{{url("fire")}}/'+latest;
      msg = $('#message');
      $.get(url, {message: msg.val()}, function(){
        msg.val('');
      });
    });

    $.ajaxSetup(
    {
      headers:
      {
        'X-CSRF-Token': $('input[name="_token"]').val()
      }
    });

    $('.row').on('click', '.channel', function(){
      id   = $(this).attr('gid');
      type = $(this).attr('type');
      $('div[id^="chat"]').hide();
      $('#chat'+id).show();

      if( type == 1 ) $('.members').show();
      else $('.members').hide();

      i = 0;
      while( i < action.length )
      {
        if( action[i] == id )
          break;
        i++;
      }
      if( i == action.length )
      {
        $('#chat'+id).html('<img src=\'{{url("images/loader.svg")}}\' class="svg">');
        action.push(id);
        call(id);
        messages(id);
      }
      latest = action[i];
      $('.channel').removeClass('active');
      $('.channel[gid="'+latest+'"]').addClass('active');
      //alert(latest+"     "+action);
    });

    $('#friends').click(function(){
      $('#friend-modal').modal();
      $('input[name="email"]').val('');
    });

    $('#groups').click(function(){
        $('#group-modal').modal();
        $('input[name="channel"]').val('');
    });

    $('#group-form').submit(function(e){
      e.preventDefault();
      name = $('input[name="channel"]').val();
      $(this).find('#error').html('');
      if( name == '' )
        $(this).find('#error').html('Group name required');
      else
      {
        url = '{{url("check/channel")}}';
        $.post(url, {channel: name}, function(ch){
          if( ch == 0 )
          {
            url = '{{url("new/channel")}}';
            form = $('#group-form').serializeArray();
            $.post(url, form, function(channel){
              $('#group-modal').modal('hide');
              content = '<li class="list-group-item channel" gid="' + channel['id'] + '" type="1" style="cursor: pointer">' + channel['channel'] + '</li>';

              $('#group-list').append(content);

              content = '<div id="chat'+ channel['id'] +'" style="overflow-y:scroll; height:400px; display: none"></div>';
              $('#all').append(content);
            });
          }
          else
            $('#group-form').find('#error').html('Group name taken');
        });
      }
    });

    $('#friend-form').submit(function(e){
      e.preventDefault();
      name = $('input[name="email"]').val();
      $(this).find('#error').html('');
      if( name == '' )
        $(this).find('#error').html('Email required');
      else
      {
        url = '{{url("check/friend")}}';
        $.post(url, {email: name}, function(ch){
          if( ch == 0 )
          {
            url = '{{url("new/friend")}}';
            form = $('#friend-form').serializeArray();
            $.post(url, form, function(channel){
              $('#friend-modal').modal('hide');
              content = '<li class="list-group-item channel" gid="' + channel['id'] + '" type="0" style="cursor: pointer">' + channel['name'] + '</li>';

              $('#friend-list').append(content);

              content = '<div id="chat'+ channel['id'] +'" style="overflow-y:scroll; height:400px; display: none"></div>';
              $('#all').append(content);
            });
          }
          else if( ch == -1 )
          {
            url = '{{url("new/user")}}';
            form = $('#friend-form').serializeArray();
            $.post(url, form, function(channel){
              $('#friend-modal').modal('hide');
              content = '<li class="list-group-item channel" gid="' + channel['id'] + '" type="0" style="cursor: pointer">' + channel['name'] + '</li>';

              $('#friend-list').append(content);

              content = '<div id="chat'+ channel['id'] +'" style="overflow-y:scroll; height:400px; display: none"></div>';
              $('#all').append(content);
            });
          }
          else if( ch == -2 )
            $('#friend-form').find('#error').html('Enter someone elses email id');
          else
            $('#friend-form').find('#error').html(name+' already exists in friends list');
        });
      }
    });

    $('#member-form').submit(function(e){
      e.preventDefault();
      email = $('#memail').val();
      url = '{{url("check/member")}}';
      $.post(url, {email: email, cid: latest}, function(ch){
        if( ch == 1 )
          $('#member-form').find('#error').html('Enter someone elses email id');
        else if( ch == 2 )
          $('#member-form').find('#error').html(email+' already exists in the group');
        else
          $('#member-modal').modal('hide');
      });
      
    });

    function call(id)
    {
      var socket = io('http://127.0.0.1:3000');
      socket.on("test-channel:"+id, function(message){
        $('#chat'+id).append('<div class="well well-sm"><p><B>'+message.data.name+'</B><span class="time">&nbsp;&nbsp;&nbsp;'+message.data.time+'</span></p><p>'+message.data.msg+'</p></div>').scrollTop(1E10);
      });
    }
  </script>
@stop
