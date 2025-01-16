<?php

namespace App\Listeners;

use App\Events\SuccessfulTransaction;
use App\Mail\NotifyCreatedTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailToUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SuccessfulTransaction $event): void
    {
        $transaction = $event->getTransaction();
        $send = $this->notify($transaction);
    }


    /**
     * Function to send email
     */
    private function notify($transaction)
    {
        try {
            Mail::to($transaction->customer_email)->send(new NotifyCreatedTransaction($transaction));

            return 'success';
        } catch (\Throwable $th) {
            return $th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile();
        }
    }
}
