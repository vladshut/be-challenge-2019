<?php


namespace App\Services;


use App\Message;
use App\User;
use Exception;

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
        if ($queryResult->getIntent() && $queryResult->getIntent()->getDisplayName() === 'switch-lang') {
            $message->creator->switchLang()->save();
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