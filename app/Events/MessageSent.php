<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\SocialMessage;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(SocialMessage $message)
    {
        // Keep only the minimal fields needed by the frontend
        $this->message = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'body' => $message->body,
            'attachment' => ($message->attachment_public_id || $message->attachment_path) ? [
                'url' => $message->attachment_url,
                'name' => $message->attachment_name,
                'type' => $message->attachment_type,
            ] : null,
            'created_at' => $message->created_at->format('d/m/Y H:i'),
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        $a = min($this->message['sender_id'], $this->message['receiver_id']);
        $b = max($this->message['sender_id'], $this->message['receiver_id']);
        return new PrivateChannel('conversation.' . $a . '.' . $b);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
