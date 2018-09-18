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

        //create admin type user (for token use)
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
        $nonAdminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);
       
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

    /** @test */
    public function update_user_using_other_users_account(): void
    {
        //create non-admin type user
        $nonAdminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        //update user
        $updateData = [
            'name' => 'User Update',
            'email' =>  'update@mail.com',
            'role' => 'admin',
            'api_token' =>  $nonAdmin->api_token
        ];
        $responseUpdate = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('PUT', '/api/user-update/'.$this->createdAdminId.'', $updateData);

        //delete created user
        $this->userRepo->delete($nonAdmin->id);

        $responseUpdate->assertStatus(403)
                                ->assertJson([
                                    "message" => "Unauthorized user.",
                                    "error"=> [
                                        "api_token" =>[
                                            "Invalid role."
                                        ]
                                    ]
                                ]);
    }

    /** @test */
    public function show_user_successfully(): void
    {
        $showData = [
            'api_token' => $this->adminToken
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/user/'.$this->createdAdminId.'', $showData);

        $responseShow->assertStatus(201)
                                ->assertJson([
                                    "data" => [
                                        "name" => "User Tester",
                                        "email" => "admin@mail.com",
                                        "role" => "admin"
                                    ]
                                ]);
    }

    /** @test */
    public function show_user_using_other_users_account(): void
    {
        //create non-admin type user
        $nonAdminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $showData = [
            'api_token' => $nonAdmin->api_token
        ];

        $responseShow = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('GET', '/api/user/'.$this->createdAdminId.'', $showData);

        //delete created user
        $this->userRepo->delete($nonAdmin->id);

        $responseShow->assertStatus(403)
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
    public function delete_user_successfully(): void
    {
        //create non-admin type user
        $nonAdminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $deleteData = [
            'api_token' => $nonAdmin->api_token
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/user-delete/'.$nonAdmin->id.'', $deleteData);

        $responseDelete->assertStatus(200)
                                ->assertJson([
                                    "message" => "User successfully deleted"
                                ]);
    }

    /** @test */
    public function delete_user_using_other_users_account(): void
    {
        //create non-admin type user
        $nonAdminData = [
            'name' => 'User Tester',
            'email' =>  'non-admin@mail.com',
            'password' => 'secret!',
            'role' => 'non-admin'
        ];
        $nonAdmin =  $this->userRepo->create($nonAdminData);

        $deleteData = [
            'api_token' => $nonAdmin->api_token
        ];

        $responseDelete = $this->withHeaders([
            'Accept' => 'application/json',
        ])->json('DELETE', '/api/user-delete/'.$this->createdAdminId.'', $deleteData);

        $responseDelete->assertStatus(403)
                                ->assertJson([
                                    "message" => "Unauthorized user.",
                                    "error" => [
                                        "api_token" => [
                                            "Invalid token."
                                        ]
                                    ]
                                ]);
    }

    public function tearDown()
    {
         $this->userRepo->delete($this->createdAdminId);
    }

}
