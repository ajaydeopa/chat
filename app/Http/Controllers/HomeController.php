<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Channel;
use App\ChannelUser;
use App\User;
use Carbon\Carbon;

use App\Events\EventName;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
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
      return view('home', compact('user', 'channels', 'friends', 'all'));
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
      $user = Auth::user();
      $msg = $request->message;
      $channel = Channel::find($group);
      $channel->message()->create(['user_id' => $user->id, 'message' => $msg, 'at' => Carbon::now()]);
      event(new EventName($group, $user, $msg));
    }

    public function channelMessages(Request $request)
    {
      $channel = Channel::find($request->id);
      $messages = $channel->message()
                          ->join('users', 'users.id', 'messages.user_id')
                          ->select('messages.*', 'users.name')
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
}
