<?php


namespace App\Services;


use App\Message;
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

        $sessionId = self::KEYWORD . '::' . $message->creator_id;

        try {
            $replyText = $this->dialogFlowService->detectIntentText($message->message, $sessionId);
        } catch (Exception $exception) {
            throw  $exception;
            return false;
        };

        $reply = Message::create([
            'message' => $replyText,
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