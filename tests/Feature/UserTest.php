<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\User;

class UserTest extends TestCase
{
    /** @var stores up the created user id for deletion on teardown */
    private $createdUserId;

    /** @var stores up admin token */
    private $adminToken;

    public function setUp()
    {
        parent::setUp();
        $this->userRepo = new \App\Repositories\UserRepository(new User);

        //create admin type user
        $adminData = [
            'name' => 'User Tester',
            'email' =>  'admin@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];
        $admin =  $this->userRepo->create($adminData);

        $this->createdAdminId = $admin->id;
        $this->adminToken = $admin->api_token;

    }

    /** @test */
    public function create_user_successfully(): void
    {
       //create user
        $data = [
            'name' => 'User Tester',
            'email' =>  'create@mail.com',
            'password' => 'secret!',
            'role' => 'admin',
            'api_token' =>  $this->adminToken
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/user-create', $data);

        //delete created users
        $content = $response->decodeResponseJson();
       // $this->userRepo->delete($admin->id);
        $this->userRepo->delete($content['data']['id']);

        $response->assertStatus(201)
                        ->assertJson([
                            "data" => [
                                "name"=> "User Tester",
                                "email"=> "create@mail.com",
                                "role"=> "admin",
                            ]
                        ]);
    }

    /** @test */
    public function create_admin_role_from_non_admin_user()
    {
        //create non-admin type user
        $adminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($adminData);
       
        $data = [
            'name' => 'User Tester',
            'email' =>  'create-admin@mail.com',
            'password' => 'secret!',
            'role' => 'admin',
            'api_token' =>  $nonAdmin->api_token
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('POST', '/api/user-create', $data);

        //delete created user
        $this->userRepo->delete($nonAdmin->id);

        $response->assertStatus(403)
                        ->assertJson([
                            "message" => "Unauthorized user.",
                            "error" => [
                                "api_token" => [
                                    "Invalid token."
                                ]
                            ]
                        ]);
    }

    /** @test */
    public function update_user_successfully(): void
    {
        //update user
        $updateData = [
            'name' => 'User Update',
            'email' =>  'update@mail.com',
            'role' => 'admin',
            'api_token' =>  $this->adminToken
        ];
        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/user-update/'.$this->createdAdminId.'', $updateData);

        $responseUpdate->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name"=> "User Update",
                                        "email"=> "update@mail.com",
                                        "role"=> "admin",
                                    ]
                                ]);
    }



    public function tearDown()
    {
         $this->userRepo->delete($this->createdAdminId);
    }

}
