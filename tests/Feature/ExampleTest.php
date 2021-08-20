<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        /* $response = $this->get('/');

        $response->assertStatus(200); */

        $response = $this->getJson('/api/v1/schools/tests');
        $response->assertStatus(200)
        ->assertJsonFragment([
            'message' => 'helloworld',
        ]);
    }
}
