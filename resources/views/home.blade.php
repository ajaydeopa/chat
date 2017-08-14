@extends('layouts.app')
<link href="{{ asset('css/player.css') }}" rel="stylesheet">
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

  .music {
    text-decoration: none;
    cursor: pointer;
  }

  .size {
    font-size: 12
  }

  #plList {
    height: 250px;
    overflow-y: scroll;
  }

  .file-preview {
    display: none
  }

  .center {
    text-align: center;
  }

  #songloader {
    margin-left: 35%;
    margin-top: 7%;
  }
</style>

@section('content')

  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            Hello <B>{{$user->name}}</B>
            <a class="pull-right music">Songs</a>
          </div>
        </div>

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
              <div class="chat" id="chat{{$channel->id}}" style="overflow-y:scroll; height:400px; display: none">
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
              <label for="sel1">Select from existing users</label>
              <select class="form-control select_user" type="friend">
                <option value="0"></option>
                @foreach($users as $u)
                  <option value="{{$u->email}}">{{$u->name}}</option>
                @endforeach
              </select>
            </div>
            <h5 style="text-align: center;"><B>Or</B></h5>
            <div class="form-group">
              <label for="sel1">Enter email</label>
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
              <label for="sel1">Select from existing users</label>
              <select class="form-control select_user" type="member">
                <option value="0"></option>
                @foreach($users as $u)
                  <option value="{{$u->email}}">{{$u->name}}</option>
                @endforeach
              </select>
            </div>
            <h5 style="text-align: center;"><B>Or</B></h5>
            <div class="form-group">
              <label for="sel1">Enter email</label>
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

  <div id="song-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h5 class="modal-title">Songs</h5>
        </div>
        <div class="modal-body">
          <div class="container" id="container">
            <div class="column add-bottom">
              <div id="mainwrap">
                <div id="nowPlay">
                  <span class="left" id="npAction">Paused...</span>
                  <span class="right" id="npTitle"></span>
                </div>
                <div id="audiowrap">
                  <div id="audio0">
                    <audio preload id="audio1" controls="controls">Your browser does not support HTML5 Audio!</audio>
                  </div>
                  <div id="tracks">
                    <a id="btnPrev">&laquo;</a>
                    <a id="btnNext">&raquo;</a>
                  </div>
                </div>
                <div id="plwrap">
                  <ul id="plList"></ul>
                </div>
              </div>
            </div>
            <br>
            {{ Form::open(['files' => true, 'id' => 'song-form', 'class' => 'center']) }}
              {{ csrf_field() }}
              <strong id="error-file" style="color: red"></strong>
              <div class="form-group">
                {{Form::file('file', ['id' => 'file', 'name' => 'file', 'class' => 'file form-control'])}}
              </div>
            {{ Form::close() }}
          </div>
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
    var latest, songid, tracks, audio,
    ch = true,
    uid = '{{$user->id}}',
    action = new Array(),
    fileExtension = ['mp3'],
    npAction = $('#npAction'),
    npTitle = $('#npTitle'),
    playing = false,
    index = 0,
    b = document.documentElement;

    b.setAttribute('data-useragent', navigator.userAgent);
    b.setAttribute('data-platform', navigator.platform);

    // start completed
      // start ajax setup
        $.ajaxSetup(
        {
          headers:
          {
            'X-CSRF-Token': $('input[name="_token"]').val()
          }
        });
      // end ajax setup

      // start event on page load
        $(function(){
          @if( $friends->count() > 0 )
            latest = '{{$friends->first()->id}}';
            $('#chat'+latest).show();
            action.push(latest);
            $('.channel[gid="'+latest+'"]').addClass('active');
            messages(latest);
            call(latest);
          @endif
          $("#file").fileinput();

          url = '{{url("get/songs")}}';
          $.post(url, function(data){
            songid = data[0]['id'];
            tracks = data;
            loadSongs();
          });
        });
      // end event on page load

      // start open songs moadal
        $('.music').click(function(){
          $('#song-modal').modal();
        });
      // end open songs moadal

      // start upload new song
        $('#song-form').submit(function(e){
          e.preventDefault();
          file = $('#file').val();
          $('#error-file').html('');
          if ($.inArray(file.split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#error-file').html('We only support mp3 format<br>');
          }
          else
          {
            url = '{{url("song/upload")}}';
            var formData = new FormData(this);
                
            $.ajax({
              url: url,
              type: 'POST',
              data: formData,
              mimeType: "multipart/form-data",
              contentType: false,
              cache: false,
              processData: false,
              success: function (data, textStatus, jqXHR) {
                if( data == 'success' )
                {
                  $('#plList').html('<img src=\'{{url("images/song.svg")}}\' class="svg" id="songloader">');
                  url = '{{url("get/songs")}}';
                  $.post(url, function(data){
                    $('#plList').html('');
                    tracks = data;
                    loadSongs();
                  });
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
              }
            });
          }
        });
      // end upload new song

      // start event when song changed
        $('#plList').on('click', '.song-list', function(){
          songid = $(this).attr('key');
        });
      // end event when song changed

      // start change input field value when selected user changed
        $('.select_user').change(function(){
          type = $(this).attr('type');
          val  = $(this).val();
          if( type == 'friend' )
            $('input[name="email"]').val(val);
          else
            $('#memail').val(val);
        });
      // end change input field value when selected user changed

      // start list of all group members
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
      // end list of all group members

      // start event when add new friend clicked
        $('#friends').click(function(){
          $('#friend-modal').modal();
          $('input[name="email"]').val('');
          $('.select_user').val('0');
          $('#friend-form').find('#error').html('');
        });
      // end event when add new friend clicked

      // start event when add new group clicked
        $('#groups').click(function(){
            $('#group-modal').modal();
            $('input[name="channel"]').val('');
        });
      // end event when add new group clicked

      // start event when add new member clicked
        $('#button-member').click(function(){
          $('#member-modal').modal();
          $('#memail').val('');
          $('.select_user').val('0');
          $('#member-form').find('#error').html('');
        });
      // end event when add new member clicked

      // start change channel when channel selected
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
      // end change channel when channel selected

      // start event when add group form submitted
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
      // end event when add group form submitted

      // start event when add friend form submitted
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
      // end event when add friend form submitted

      // start event when add member form submitted
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
      // end event when add member form submitted

      // start event when new message send
        $('#message-form').submit(function(e){
          e.preventDefault();
          url = '{{url("fire")}}/'+latest;
          msg = $('#message');
          $.get(url, {message: msg.val(), type: 'msg'}, function(){
            msg.val('');
          });
        });
      // end event when new message send

      // start load channel messages
        function messages(id)
        {
          url = '{{url("get/messages")}}';
          $.get(url, {id: id}, function(msg){
            content = '';

            for( i = 0; i < msg.length; i++ )
              content += '<div class="well well-sm size"><p><B>'+ msg[i]['name'] +'</B><span class="time">&nbsp;&nbsp;&nbsp;'+ msg[i]['time'] +'</span></p><p class="size">'+msg[i]['message']+'</p></div>';
            $('#chat'+id).html(content).scrollTop(1E10);
          });
        }
      // end load channel messages
    // end completed

    // start create a connection for selected channel
      function call(id)
      {
        var socket = io('http://127.0.0.1:3000');
        socket.on("test-channel:"+id, function(message){
          console.log(message.data);
          if( message.data.type == 'msg' )
          {
            $('#chat'+id).append('<div class="well well-sm"><p><B>'+message.data.name+'</B><span class="time">&nbsp;&nbsp;&nbsp;'+message.data.time+'</span></p><p class="size">'+message.data.msg+'</p></div>').scrollTop(1E10);
          }
          else if( message.data.id != uid )
          {
            ch = false;
            index = message.data.idx;
            if( message.data.type == 'play' )
              playTrack(index, message.data.curr);
            else if( message.data.type == 'pause' )
              audio.pause();
          }
        });
      }
    // end create a connection for selected channel

    // start load songs
      function loadSongs()
      { 
        var supportsAudio = !!document.createElement('audio').canPlayType;
        if (supportsAudio) {
          var buildPlaylist = $.each(tracks, function(key, value) {
            var trackNumber = value.track,
              trackName = value.name,
              id = value.id,
              trackLength = value.length;
            if (trackNumber.toString().length === 1) {
              trackNumber = '0' + trackNumber;
            } else {
              trackNumber = '' + trackNumber;
            }
            $('#plList').append('<li class="song-list" key="' + id + '"><div class="plItem"><div class="plNum">' + trackNumber + '.</div><div class="plTitle">' + trackName + '</div><div class="plLength">' + trackLength + '</div></div></li>');
          }),
          trackCount = tracks.length;
          
          audio = $('#audio1').bind('play', function () {
            playing = true;
            npAction.text('Now Playing...');
            if( ch )
            {
              url = '{{url("fire")}}/'+latest;
              $.get(url, {index: index, curr: this.currentTime, type: 'play'}, function(){
                console.log('play' + index);
              });
            }
            ch = true;
          }).bind('pause', function () {
            playing = false;
            npAction.text('Paused...');
          }).bind('ended', function () {
            npAction.text('Paused...');
            type = 'ended';
            if ((index + 1) < trackCount) {
              index++;
              loadTrack(index);
              audio.play();
            } else {
              audio.pause();
              index = 0;
              loadTrack(index);
            }
          }).get(0);

          var btnPrev = $('#btnPrev').click(function () {
            if ((index - 1) > -1) {
              index--;
              loadTrack(index);
              if (playing) {
                audio.play();
              }
            } else {
              audio.pause();
              index = 0;
              loadTrack(index);
            }
          }),
          btnNext = $('#btnNext').click(function () {
            if ((index + 1) < trackCount) {
              index++;
              loadTrack(index);
              if (playing) {
                audio.play();
              }
            } else {
              audio.pause();
              index = 0;
              loadTrack(index);
            }
          }),
          li = $('#plList li').click(function () {
            var id = parseInt($(this).index());
            if (id !== index) {
              playTrack(id, 0);
            }
          });
            
          loadTrack(index);
        }
      }
    // end load songs

    // start load track with index = id
      function loadTrack(id)
      {
        extension = audio.canPlayType('audio/mpeg') ? '.mp3' : audio.canPlayType('audio/ogg') ? '.ogg' : '';
        $('.plSel').removeClass('plSel');
        $('#plList li:eq(' + id + ')').addClass('plSel');
        npTitle.text(tracks[id].name);
        index = id;
        audio.src = '{{url("")}}/songs/' + tracks[id].file + extension;
      }
    // end load track with index = id

    // start play track with index = id
      function playTrack(id, curr_time)
      {
        loadTrack(id);
        audio.currentTime = curr_time;
        audio.play();
      }
    // end play track with index = id
  </script>
@stop