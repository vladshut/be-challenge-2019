<?php

namespace Tests\Feature;

use App\Events\Message as MessageEvent;
use App\Message;
use App\Room;
use App\Services\BotService;
use App\Services\DialogFlowService;
use Exception;
use Illuminate\Support\Arr;
use Mockery\Mock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BotTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testHandle()
    {
        $replyText = 'Hello!';

        $this->mock(DialogFlowService::class, function ($mock) use ($replyText) {
            /** @var $mock Mock */
            $mock->shouldReceive('detectIntentText')->once()->andReturn($replyText);
        });

        $this->expectsEvents(MessageEvent::class);

        $user = $this->login();
        $room = factory(Room::class)->create();
        $room->users()->attach($user);

        $creator_id = $user->id;
        $room_id = $room->id;
        $message = BotService::KEYWORD . ' ' . 'Hi!';

        $requestData = compact('creator_id', 'room_id', 'message');

        $request = Message::create($requestData);

        /** @var BotService $bot */
        $bot = app(BotService::class);

        $bot->handle($request);

        $criteria['creator_id'] = null;
        $criteria['room_id'] = $room_id;
        $criteria['message'] = $replyText;

        $this->assertDatabaseHas('messages', $criteria);
    }
}
