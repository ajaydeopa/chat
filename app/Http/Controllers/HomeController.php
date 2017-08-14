<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Channel;
use App\ChannelUser;
use App\User;
use App\Songs;
use Carbon\Carbon;

use App\Events\EventName;
use App\Http\Controllers\mp3file;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
      $user = Auth::user();
      $cids = $user->channel()->select('channel_id')->get();
      $all  = Channel::whereIn('id', $cids)->get();

      $channels = $all->where('type', 1);
      $friends  = $all->where('type', 0);
      foreach ($friends as $friend)
      {
        $temp = $friend->user()->where('user_id', '!=', $user->id)->first();
        $friend->detail = User::select('id', 'name', 'email')->find($temp->user_id);
      }

      $users = User::all()->except($user->id);
      return view('home', compact('user', 'channels', 'friends', 'all', 'users', 'songs'));
    }

    public function checkChannel(Request $request)
    {
      $channel = $request->channel;
      return Channel::where('channel', $channel)->count();
    }

    public function checkFriend(Request $request)
    {
      $user = Auth::user();
      $friend = User::where('email', $request->email)->first();
      if( $friend == '' )
        return -1;
      if( $friend->id == $user->id )
        return -2;
      $cids = $user->channel()->select('channel_id')->get();
      $cids = Channel::whereIn('id', $cids)->where('type', 0)->select('id')->get();
      $ch = $friend->channel()->whereIn('channel_id', $cids)->count();
      return $ch;
    }

    public function newFriend(Request $request)
    {
      $user = Auth::user();
      $channel = Channel::create(['type' => 0, 'channel' => 'direct']);
      $channel->user()->create(['user_id' => $user->id]);
      $user = User::where('email', $request->email)->first();
      $channel->user()->create(['user_id' => $user->id]);
      $channel->name = $user->name;
      return $channel;
    }

    public function newChannel(Request $request)
    {
      $user = Auth::user();
      $channel = Channel::create($request->all()+['type' => 1]);
      $channel->user()->create(['user_id' => $user->id]);
      return $channel;
    }

    public function newUser(Request $request)
    {
      $user = Auth::user();
      $channel = Channel::create(['type' => 0, 'channel' => 'direct']);
      $channel->user()->create(['user_id' => $user->id]);
      $user = User::create(['email' => $request->email, 'login_flag' => 0, 'name' => $request->email]);
      $channel->user()->create(['user_id' => $user->id]);
      $channel->name = $request->email;
      return $channel;
    }

    public function message($group, Request $request)
    {
      date_default_timezone_set('Asia/Kolkata');
      $type = $request->type;
      $user = Auth::user();

      if( $type == 'msg' )
      {
        $time    = Carbon::now();
        $msg     = $request->message;
        $channel = Channel::find($group);
        $channel->message()->create(['user_id' => $user->id, 'message' => $msg, 'at' => $time]);
        event(new EventName($group, $user, $msg, $time->format('H:i'), $type));
        return;
      }
      //return $request->all();
      event(new EventName($group, $user, $request->index, $request->curr, $type));
    }

    public function channelMessages(Request $request)
    {
      $channel = Channel::find($request->id);
      $messages = $channel->message()
                          ->join('users', 'users.id', 'messages.user_id')
                          ->select('messages.*', 'users.name')
                          ->orderBy('messages.id')
                          ->get();

      foreach ($messages as $msg)
      {
        $temp = Carbon::parse($msg->at);
        $msg->time = $temp->format('H:i');
        $msg->date = $temp->format('Y-m-d');
      }
      return $messages;
    }

    public function checkMember(Request $request)
    {
      $user  = Auth::user();
      $email = $request->email;

      if( $user->email == $email )
        return 1;

      $channel = Channel::find($request->cid);
      $friend  = User::where('email', $email)->first();
      if( $friend != '' )
      {
        $ch = $channel->user()->where('user_id', $friend->id)->count();
        if( $ch > 0 )
          return 2;
      }
      else
        $friend = User::create(['email' => $email, 'login_flag' => 0, 'name' => $email]);

      $channel->user()->create(['user_id' => $friend->id]);
      return 0;
    }

    public function channelMembers(Request $request)
    {
      $channel = Channel::find($request->id);
      $users = $channel->user()
                       ->join('users', 'users.id', 'channel_users.user_id')
                       ->select('users.name', 'users.email')
                       ->get();
      return $users;
    }

    public function uploadSong(Request $request)
    {
      $file = $request->file('file');
      $len = new mp3file($file);
      $duration = $len->getDuration();
      try
      {
        $path = public_path().'/songs';
        $name = $file->getClientOriginalName();
        $file->move($path, $name);
        Songs::create(['name' => $name, 'duration' => $len->formatTime($duration)]);
        return 'success';
      }
      catch( \Exception $e )
      {
        return $e;
      }
    }

    public function allSongs()
    {
      $songs = Songs::all();
      $song = [];
      $i = 1;

      foreach ($songs as $s)
      {
        $string = $s->name;
        $pos = strlen($string) - 1;
        while( $pos >= 0 )
        {
          if( $string[$pos] == '.' )
            break;
          $pos--;
        }

        $first  = substr($string, 0, $pos);

        $song[$i-1]['id']     = $s->id;
        $song[$i-1]['track']  = $i;
        $song[$i-1]['name']   = $first;
        $song[$i-1]['file']   = $first;
        $song[$i-1]['length'] = $s->duration;
        $i++;
      }
      return $song;
    }
}
