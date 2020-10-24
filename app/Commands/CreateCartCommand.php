<?php

namespace App\Commands;

use App\Core\CartService;
use LaravelZero\Framework\Commands\Command;

class CreateCartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create
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
     * @return void
     */
    public function handle(CartService $cartService): void
    {
        $output = $cartService->create($this->argument('items'), $this->option('bill-currency'));

        $this->info("Subtotal: {$output->subtotal}");
        $this->info("Taxes: {$output->taxes}");
        foreach ($output->discounts as $line) {
            $this->info("   {$line}");
        }
        $this->info("Total: {$output->total}");
    }
}
