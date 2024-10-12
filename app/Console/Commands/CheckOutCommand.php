<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckOutCommand extends Command
{
    protected $signature = 'check:out';

    protected $description = 'if File state 1 we make it 0 after 1 hour';

    public function handle(): void
    {
        $files = File::query()->where('state', '=', 1)->get();
        foreach ($files as $file) {
            $createdDate = $file->created_date;
            $daysDifference = Carbon::now()->diffInDays($createdDate);
            if ($daysDifference > 1) {
                $file->update(['state' => 0]);
            }
        }
    }
}
