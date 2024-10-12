<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        \DB::table('users')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $users = User::factory(100)->create([
            'password' => Hash::make('12345678')
        ]);

        $output = '';

        foreach($users as $user){
            $output .= "{$user->email}\n";
        }

        Storage::put('emails.txt', $output);

        $this->command->info("Done");
    }
}
