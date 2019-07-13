<?php


namespace App\Services;


use App\Message;
use App\Order;
use App\User;
use DateTime;
use Exception;
use Google\Cloud\Dialogflow\V2\Context;

class BotService
{
    /** @var DialogFlowService */
    private $dialogFlowService;


    const KEYWORD = '@bot';

    /**
     * Bot constructor.
     * @param DialogFlowService $dialogFlowService
     */
    public function __construct(DialogFlowService $dialogFlowService)
    {
        $this->dialogFlowService = $dialogFlowService;
    }

    /**
     * @param Message $message
     * @return bool
     * @throws Exception
     */
    public function handle(Message $message)
    {
        if (!$this->canHandle($message)) {
            return false;
        }

        $sessionId = self::KEYWORD . '::' . $message->room_id . '::' . $message->creator_id;

        try {
            $queryResult = $this->dialogFlowService->detectIntentText($message->message, $sessionId, $message->creator->lang);
        } catch (Exception $exception) {
            return false;
        };

        if ($queryResult->getIntent()) {
            if ($queryResult->getIntent()->getDisplayName() === 'switch-lang') {
                $message->creator->switchLang()->save();
            }

            if ($queryResult->getIntent()->getDisplayName() === 'rent-set-reminder') {
                $parameters = $queryResult->getParameters()->getFields();
                $date = $parameters->offsetGet('date')->getStringValue() ?? null;
                $time = $parameters->offsetGet('time')->getStringValue() ?? null;

                $date = (new DateTime($date))->format('Y-m-d');
                $time = (new DateTime($time))->format('H:i:s');
                $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', "$date $time");

                $type = $parameters->offsetGet('car-type')->getStringValue() ?? null;

                $remindIn = 0;

                try {
                    $remindInAmount = $parameters->offsetGet('duration')->getStructValue();
                    $remindInAmount = $remindInAmount ? $remindInAmount->getFields()->offsetGet('amount')->getNumberValue() : null;

                    $remindInUnit = $parameters->offsetGet('duration')->getStructValue();
                    $remindInUnit = $remindInUnit ? $remindInUnit->getFields()->offsetGet('unit')->getStringValue() : null;

                    $availableUnits = ['day' => 60*24, 'h' => 60, 'min' => 1];

                    if (in_array($remindInUnit , ['day', 'h', 'min'])) {
                        $remindIn = $availableUnits[$remindInUnit] * $remindInAmount;
                    }
                } catch (Exception $e) {

                }

                $orders = Order::all()
                    ->where('datetime', '=', $dateTime->format('Y-m-d H:i:s'))
                    ->where('type', '=', $type);

                if ($orders->count() != 0) {
                    $reply = Message::create([
                        'message' => $type . ' is already booked for ' . $dateTime->format('Y-m-d H:i:s'),
                        'room_id' => $message->room_id,
                        'user_id' => null,
                    ]);

                    event(new \App\Events\Message($reply));

                    return true;
                }

                $order = Order::create(
                    [
                        'user_id' => $message->creator_id,
                        'room_id' => $message->room_id,
                        'datetime' => $dateTime,
                        'type' => $type,
                        'remind_in' => $remindIn,
                    ]
                );

                $reply = Message::create([
                    'message' => 'Car was successfully booked for you. Rental id ' . $order->id,
                    'room_id' => $message->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                return true;
            } else if ($queryResult->getIntent()->getDisplayName() === 'list') {
                $orders = Order::all()->where('user_id', '=', $message->creator_id);

                $replyStr = '';

                foreach ($orders as $order) {
                    $replyStr .= $order->id;
                    $replyStr .= " $order->datetime";
                    $replyStr .= " $order->type";
                    $replyStr .= $order->remind_in ? " Will remind in $order->remind_in minutes" : " Without reminder";
                    $replyStr .= "\n";
                }

                if ($orders->count() == 0) {
                    $replyStr = 'You have not any rentals yet.';
                }

                $reply = Message::create([
                    'message' => $replyStr,
                    'room_id' => $message->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                return true;
            } else if ($queryResult->getIntent()->getDisplayName() === 'cancel-one') {
                $parameters = $queryResult->getParameters()->getFields();
                $orderId = $parameters->offsetGet('id')->getNumberValue() ?? null;

                Order::destroy($orderId);

                $reply = Message::create([
                    'message' => 'Rental ' . $orderId . ' was successfully deleted!',
                    'room_id' => $message->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                return true;
            } else if ($queryResult->getIntent()->getDisplayName() === 'cancel-all') {
                $orders = Order::all()->where('user_id', '=', $message->creator_id);

                /** @var Order $order */
                foreach ($orders as $order) {
                    $order->delete();
                }

                $reply = Message::create([
                    'message' => 'All your rentals was successfully cancelled!',
                    'room_id' => $message->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                return true;
            } else if ($queryResult->getIntent()->getDisplayName() === 'reschedule') {
                $parameters = $queryResult->getParameters()->getFields();

                $date = $parameters->offsetGet('date')->getStringValue() ?? null;
                $time = $parameters->offsetGet('time')->getStringValue() ?? null;

                $date = (new DateTime($date))->format('Y-m-d');
                $time = (new DateTime($time))->format('H:i:s');
                $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', "$date $time");

                $orderId = $parameters->offsetGet('id')->getNumberValue() ?? null;

                /** @var Order $order */
                $order = Order::find($orderId);

                if (!$order) {
                    $reply = Message::create([
                        'message' => "Wrong order id $orderId!",
                        'room_id' => $message->room_id,
                        'user_id' => null,
                    ]);

                    event(new \App\Events\Message($reply));

                    return true;
                }

                $order->datetime = $dateTime;
                $order->save();

                $reply = Message::create([
                    'message' => "Your rental $orderId was rescheduled to " . $dateTime->format('Y-m-d H:i:s'),
                    'room_id' => $message->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                return true;
            }


            }

        $reply = Message::create([
            'message' => $queryResult->getFulfillmentText(),
            'room_id' => $message->room_id,
            'user_id' => null,
        ]);

        event(new \App\Events\Message($reply));

        return true;
    }

    public function canHandle(Message $message)
    {
        return strpos($message->message, self::KEYWORD . ' ') === 0;
    }
}