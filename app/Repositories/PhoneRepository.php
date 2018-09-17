<?php 

namespace App\Repositories;

use \App\Repositories\Interfaces\UserInterface;
use \App\Repositories\Interfaces\PhoneInterface;
use \App\Models\UserPhone;
use App\Transformers\PhoneTransformer;

class PhoneRepository implements PhoneInterface
{
    /** @var \App\Models\UserPhone  */
    protected $phone;

    /** @var \App\Repositories\Interfaces\UserInterface */
    protected $user;

    /**
     * PhoneRepository constructor
     *
     * @param \App\Models\UserPhone $phone
     * @param App\Repositories\Interfaces\UserInterface $user
     */
    public function __construct(UserPhone $phone, UserInterface $user)
    {
        $this->phone = $phone;
        $this->user = $user;
    }

    /**
     * Save data into database
     *
     * @param array $data
     * @return array
     */
    public function create(array $data)
    {
        $userId = $this->user->getTokenUserId($data['api_token']);

        $phone = $this->phone->create([
            'user_id'            => $userId,
            'phone_number' => $data['phone_number'],
        ]);

        return $phone;
    }

    /**
     * Display data from database
     *
     * @param int $id
     * @return array
     */
    public function show($id)
    {
        return $this->phone->find($id);
    }

    /**
     * Display data from database
     *
     * @param int $id
     * @return array
     */
    public function showAll(array $data, $userId)
    {
        return $this->phone->where('user_id', $userId)->paginate(10);
    }

    /**
     * Update data in database
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $phone = $this->phone->find($id);
        $phone->update($data);

        return $phone;
    }

    /**
     * Delete data from database
     *
     * @param int $id
     * @return array
     */
    public function delete($id)
    {
        return $this->phone->destroy($id);
    }

    /**
     * Get details from database
     *
     * @param int $id
     * @return array
     */
    public function getDetails($id)
    {
        $phone = $this->phone->find($id);
        return $phone;
    }

    /**
     * Get the associated model
     *
     * @return array
     */
    public function getModel()
    {
        return $this->phone;
    }


}