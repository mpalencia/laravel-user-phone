<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhoneTest extends TestCase
{

     public function setUp()
    {
        parent::setUp();
    }

     /** @test */
    public function testCreatePhoneSuccessfully()
    {

        $payload = ['phone_number' => '345553',
                          'api_token'=> '$2y$10$Yd5yDeJ6cellzW4zpW/bpuIr5cVNdomiA18ziBmo8ZHjUXb13hrau'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/phone-create', $payload);


        $response
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => 4,
                    "user_id" => 1,
                    "phone_number" => "345553"
                ]
            ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }


    public function tearDown()
    {

    }

}
