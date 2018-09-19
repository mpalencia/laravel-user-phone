<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Repositories\UserRepository;
use \App\Models\UserPhone;
use \App\Models\User;

class UserPhoneTest extends TestCase
{
    /** @var stores up the created user phone id for deletion on teardown */
    private $createdUserPhoneId;

    public function setUp()
    {
        parent::setUp();

        $this->userPhoneRepo = new \App\Repositories\PhoneRepository(new UserPhone);
    }

    /**
     * Test create function
     *
     * @return void
     */
    public function testCanCreateUserPhone(): void
    {
        $dataUser = [
            'name' => 'Tester Tester',
            'email' =>  'tester@mail.com',
            'password' => 'secret!',
            'role' => 'admin'
        ];

        $userRepo = new UserRepository(new User);
        $user = $userRepo->create($dataUser);

        $dataPhone = [
            'api_token' => $user->api_token,
            'phone_number' => '8737878'
        ];

        $userPhone = $this->userPhoneRepo->create($dataPhone);
        $this->createdUserPhoneId = $userPhone->id;

        $userRepo->delete($user->id);

        $this->assertInstanceOf(UserPhone::class, $userPhone);
        $this->assertEquals($dataPhone['phone_number'], $userPhone->phone_number);
    }

    /**
     * Test update function
     *
     * @return void
     */
    public function testCanUpdateUserPhone(): void
    {
        $userPhoneFactory = factory(UserPhone::class)->create();

        $data = [
            'user_id' => 1,
            'phone_number' =>  '649174669444'
        ];

        $this->createdUserPhoneId = $userPhoneFactory->id;
        $updated = $this->userPhoneRepo->update($data, $userPhoneFactory->id);

        $this->assertEquals($data['user_id'], $updated->user_id);
        $this->assertEquals($data['phone_number'], $updated->phone_number);
    }

    /**
     * Test show function
     *
     * @return void
     */
    public function testCanShowUser(): void
    {
        $userPhoneFactory = factory(UserPhone::class)->create();

        $this->createdUserPhoneId = $userPhoneFactory->id;
        $userPhone = $this->userPhoneRepo->show($userPhoneFactory->id);

        $this->assertInstanceOf(UserPhone::class, $userPhone);
        $this->assertEquals($userPhoneFactory->user_id, $userPhone->user_id);
        $this->assertEquals($userPhoneFactory->phone_number, $userPhone->phone_number);
    }

    /**
     * Test delete function
     *
     * @return void
     */
    public function testCanDeleteUser(): void
    {
        $userPhoneFactory = factory(UserPhone::class)->create();

        $userPhone = $this->userPhoneRepo->delete($userPhoneFactory->id);

        $this->assertEquals(1, $userPhone);
    }

    public function tearDown()
    {
        //delete test data in `user_phones` table
        $this->userPhoneRepo->delete($this->createdUserPhoneId);
    }

}
