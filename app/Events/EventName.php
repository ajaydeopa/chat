<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Carbon\Carbon;

class EventName implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($group, $user, $msg_idx, $time, $type)
    {
      if( $type == 'msg' )
      {
        $this->data = array(
          'id'   => $user->id,
          'name' => $user->name,
          'msg'  => $msg_idx,
          'time' => $time,
          'group'=> $group,
          'type' => 'msg'
        );
      }
      else
      {
        $this->data = array(
          'id'   => $user->id,
          'idx'  => $msg_idx,
          'curr' => $time,
          'group'=> $group,
          'type' => $type
        );
      }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['test-channel'];
    }
}
