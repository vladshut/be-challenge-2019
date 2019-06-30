<?php

namespace Tests\Feature;

use App\Events\Message as MessageEvent;
use App\Room;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageTest extends TestCase
{
    public function testStore()
    {
        $this->expectsEvents(MessageEvent::class);

        $user = $this->login();
        $room = factory(Room::class)->create();
        $room->users()->attach($user);

        $payload = ['message' => 'Hello World!', 'user_id' => $user->id, 'room_id' => $room->id];

        $this->json('POST', 'api/message', $payload)
            ->assertStatus(200)
            ->assertJson([
                'result' => 'message successfully sent',
            ]);

        $criteria = Arr::only($payload, ['message', 'room_id']);
        $criteria['creator_id'] = $payload['user_id'];

        $this->assertDatabaseHas('messages', $criteria);
    }
}
