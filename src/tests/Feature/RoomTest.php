<?php

namespace Tests\Feature;

use App\Room;
use App\User;
use Illuminate\Support\Arr;
use Tests\TestCase;

class RoomTest extends TestCase
{
    public function testShow()
    {
        $user = $this->login();
        /** @var Room $room */
        $room = factory(Room::class)->create();
        $room->users()->attach($user);


        $this->json('GET', "api/rooms/{$room->id}")
            ->assertStatus(200)
            ->assertJson([
                'room_id' => $room->id,
                'room_name' => $room->name,
                'creator_id' => $room->creator->id,
                'created_at' => (string) $room->created_at,
                'users' => [
                    ['user_id' => $user->id, 'user_name' => $user->user_name, ],
                ]
            ]);
    }

    public function testIndex()
    {
        $roomsCount = 5;
        $user = $this->login();

        $rooms = factory(Room::class, $roomsCount)->create()->each(function(Room $room) use ($user) {
            $room->users()->attach($user);
        });


        $this->json('GET', "api/rooms")
            ->assertStatus(200)
            ->assertJsonStructure(["*" => [
                'room_id',
                'room_name',
                'creator_id',
                'created_at',
                'users' => ['*' => [
                    'user_id',
                    'user_name',
                ]],
            ]])
            ->assertJsonCount($roomsCount);
    }

    public function testStore()
    {
        $user = $this->login();

        $userId = $user->id;
        $name = 'Chat room';

        $payload = ['name' => $name, 'user_id' => $userId];

        $this->json('POST', 'api/rooms', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'room_id',
                'room_name',
                'creator_id',
                'created_at',
            ]);

        $this->assertDatabaseHas('rooms', ['name' => $name, 'creator_id' => $userId]);
    }

    public function testDelete()
    {
        $user = $this->login();
        /** @var Room $room */
        $room = factory(Room::class)->create();
        $room->users()->attach($user);


        $this->json('DELETE', "api/rooms/{$room->id}")
            ->assertStatus(200)
            ->assertJson([
                'result' => 'room removed successfully',
            ]);

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function testJoin()
    {
        $user = $this->login();
        $room = factory(Room::class)->create();

        $payload = ['user_id' => $user->id];

        $this->json('POST', "api/rooms/{$room->id}/join", $payload)
            ->assertStatus(200)
            ->assertJson([
                'result' => 'user successfully joined the room',
            ]);

        $this->assertDatabaseHas('room_user', ['room_id' => $room->id, 'user_id' => $user->id]);
    }

    public function testLeave()
    {
        $user = $this->login();
        /** @var Room $room */
        $room = factory(Room::class)->create();
        $room->users()->attach($user);

        $payload = ['user_id' => $user->id];

        $this->json('POST', "api/rooms/{$room->id}/leave", $payload)
            ->assertStatus(200)
            ->assertJson([
                'result' => 'user successfully left the room',
            ]);

        $this->assertDatabaseMissing('room_user', ['room_id' => $room->id, 'user_id' => $user->id]);
    }
}
