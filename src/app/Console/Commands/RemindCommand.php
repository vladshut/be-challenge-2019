<?php

namespace App\Console\Commands;

use App\Message;
use App\Order;
use Illuminate\Console\Command;

class RemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = Order::all()->where('remind_in', '!=', 0)
            ->where('is_reminded', '=', 0);
        $now = time() + 3600 * 3;
        /** @var Order $order */
        foreach ($orders as $order) {
            $remindInSeconds = $order->remind_in * 60;
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $order->datetime)->getTimestamp();

            if ($datetime - $now < $remindInSeconds) {
                $reply = Message::create([
                    'message' => 'Rental reminder: ' . $order->id . ' ' . $order->datetime,
                    'room_id' => $order->room_id,
                    'user_id' => null,
                ]);

                event(new \App\Events\Message($reply));

                $order->is_reminded = true;
                $order->save();
            }
        }
    }
}
