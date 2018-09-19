<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\User;

class UserTest extends TestCase
{
    /** @var stores up the created user id for deletion on teardown */
    private $createdUserId;

    public function setUp()
    {
        parent::setUp();
        
        $this->userRepo = new \App\Repositories\UserRepository(new User);
    }

    /**
     * Test create function
     *
     * @return void
     */
    public function testCanCreateUser(): void
    {
        $data = [
            'name' => 'Matthew Palencia',
            'email' =>  'matthew.palencia@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];

        $created = $this->userRepo->create($data);
        $this->createdUserId = $created->id;

        $this->assertInstanceOf(User::class, $created);
        $this->assertEquals($data['name'], $created->name);
        $this->assertEquals($data['email'], $created->email);
        $this->assertEquals($data['role'], $created->role);
    }

    /**
     * Test update function
     *
     * @return void
     */
    public function testCanUpdateUser(): void
    {
        $userFactory = factory(User::class)->create();

        $data = [
            'name' => 'John Doe',
            'email' =>  'john.doe@mail.com',
            'role'   =>  'admin'
        ];

        $this->createdUserId = $userFactory->id;
        $updated = $this->userRepo->update($data, $userFactory->id);

        $this->assertEquals($data['name'], $updated->name);
        $this->assertEquals($data['email'], $updated->email); 
        $this->assertEquals($data['role'], $updated->role);
    }

    /**
     * Test show function
     *
     * @return void
     */
    public function testCanShowUser(): void
    {
        $userFactory = factory(User::class)->create();

        $this->createdUserId = $userFactory->id;
        $user = $this->userRepo->show($userFactory->id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userFactory->name, $user->name);
        $this->assertEquals($userFactory->email, $user->email);
        $this->assertEquals($userFactory->role, $user->role);
    }

    /**
     * Test delete function
     *
     * @return void
     */
    public function testCanDeleteUser(): void
    {
        $userFactory = factory(User::class)->create();

        $user = $this->userRepo->delete($userFactory->id);

        $this->assertEquals(1, $user);
    }

    /**
     * Test function to get user id using api_token
     *
     * @return void
     */
    public function testCanGetIdByToken(): void
    {
        $userFactory = factory(User::class)->create();
        
        $this->createdUserId = $userFactory->id;
        $user = $this->userRepo->getTokenUserId($userFactory->api_token);

        $this->assertEquals($userFactory->id, $user);
    }

    /**
     * Test function to get user details using api_token
     *
     * @return void
     */
    public function testCanGetUserDetailsByToken(): void
    {
        $userFactory = factory(User::class)->create();
        
        $this->createdUserId = $userFactory->id;
        $user = $this->userRepo->getTokenUserDetails($userFactory->api_token);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userFactory->name, $user->name);
        $this->assertEquals($userFactory->email, $user->email);
        $this->assertEquals($userFactory->role, $user->role);
    }

    public function tearDown()
    {
        //delete test data in `users` table
        $this->userRepo->delete($this->createdUserId);
    }

}
