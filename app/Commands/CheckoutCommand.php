<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class CheckoutCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'checkout
                            {--bill-currency=USD : currency type EGP,USD (optional)}
                            {items* : a list of item names (required)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Creates a bill using item names & offers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
