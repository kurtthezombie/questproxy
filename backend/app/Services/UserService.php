<?php

namespace App\Services;

use App\Models\User;
use Hash;

class UserService
{
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Create a new user.
     *
     * @param array $params User data
     * @return User The created user instance
    */
    public function create(array $params): User 
    {
        $params['password'] = Hash::make($params['password']);

        return $this->user->create($params);
    }

    /**
     * Update a user.
     *
     * @param $id user id $data formdata
     * @return bool to confirm if updated or not
    */
    public function updateUser($id, $data)
    {
        $user = $this->user->findOrFail($id);

        return $user->update($data);
    }

    /**
     * Update a user.
     *
     * @param int $id
     * @return bool
    */
    public function deleteUser($id) {
        $user = $this->user->find($id);

        return $user->delete();
    }

}
