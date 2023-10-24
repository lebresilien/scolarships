<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{ User, Account };
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john.doe@gmail.com',
                'password' => Hash::make('12345678'),
            ]);

            $user->assignRole('admin');

            $account = Account::create([
                'name' => 'univers school',
                'user_id' => $user->id,
                'slug' => Str::slug('univers school', '-')
            ]);

            $user->accounts()->attach($account->id); 

            event(new Registered($user));

            Auth::login($user);

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
