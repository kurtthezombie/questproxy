<?php

namespace App\Services;

use App\Models\User;
use Exception;
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

    public function cancelAccountDeletion($email)
    {
        $user = $this->user->where('email', $email)->first();

        if(!$user) {
            throw new Exception('User not found');
        }  

        //delete happens here
        if (!$user->delete()) {
            throw new Exception('Failed to delete the user');
        }

        return true;
    }

    public function getUserByUsername($username) {
        return $this->user->where('username', $username)->first();
    }

}
