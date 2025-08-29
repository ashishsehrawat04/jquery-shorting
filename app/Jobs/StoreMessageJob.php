<?php
namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class StoreMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $msg;

    public function __construct(array $msg)
    {
        $this->msg = $msg;
    }

    public function handle()
    {
        // Save to DB
        Message::create([
            'sender_id'   => $this->msg['sender_id'],
            'receiver_id' => $this->msg['receiver_id'],
            'message'     => $this->msg['message'],
            'is_read'     => $this->msg['is_read'],
            'created_at'  => $this->msg['created_at'],
        ]);

        // Remove from Redis once stored
        Redis::lrem(
            "chat:pending:{$this->msg['receiver_id']}",
            0,
            json_encode($this->msg)
        );
    }
}
