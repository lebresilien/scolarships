<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_register()
    {
        $faker = \Faker\Factory::create();
        $email = $faker->email;
        $password = $faker->password;

        $data = [
            'email' => $email,
            'lname' => $faker->userName,
            'fname' => $faker->name,
            'phone' => $faker->e164PhoneNumber,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/v1/auth/sign-up', $data);
        $response->assertStatus(200);
        $this->assertArrayHasKey('message',$response->json());
       
        User::where('email', $email)->delete();
    }

    public function test_login()
    {
        $faker = \Faker\Factory::create();
        $data = [
            'email' => 'tapodndjoddu@gmail.com',
            'password' => '12345678',
        ];
        $response = $this->postJson('/api/v1/auth/sign-in', $data);
        $response->assertStatus(200);
        $this->assertArrayHasKey('success',$response->json());
    }
}
