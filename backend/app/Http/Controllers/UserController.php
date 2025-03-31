<?php

namespace App\Http\Controllers;

use App\Events\RegisteredUser;
use App\Mail\AccountDeletionMail;
use App\Models\User;
use App\Services\UserService;
use App\Traits\RankOperations;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mail;


class UserController extends Controller
{
    use RankOperations, ApiResponseTrait;

    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return $this->successResponse("User {$id} is found.",200,['user' => $user]);
        } catch(ModelNotFoundException $e){
            return $this->failedResponse("User {$id} not found.",404);
        } catch(Exception $e){
            return $this->failedResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function index()
    {
        $users = User::all();

        return $this->successResponse('User records retrieved successfully.', 200, ['users' => $users]);
    }

    public function create(Request $request)
    {
        //validate inputs
        $data = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'contact_number' => 'required|string|max:15',
            'role' => 'required|string',
        ]);

        try {
            //call service
            $user = $this->userService->create($data);

            //call event for email
            event(new RegisteredUser($user));
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage(),500);
        }
    
        return $this->successResponse('User created successfully',201,['user' => $user],);
    }

    public function edit(int $id){
        $user = User::find($id);

        if (!$user) {
            return $this->failedResponse("User account {$id} not found.", 404);
        }

        return $this->successResponse("User account {$id} found.", 200, ['user' => $user]);
    }

    public function update(Request $request, $id) {
        $formData = $request->validate([
            'email' => 'required|email',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'contact_number' => 'required|string|max:15',
        ]);

        try {
            $this->userService->updateUser($id, $formData);

            return $this->successResponse("User account has been updated successfully.",200);

        } catch (Exception $error) {
            return $this->failedResponse("Error {$error->getMessage()}", 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->userService->deleteUser($id);

            return $this->successResponse("User deleted successfully",200);
        } catch (Exception $e){
            return $this->failedResponse("Error {$e->getMessage()}",500);
        }
    }

    public function showByUsername(string $username)
    {
        try {
            $user = $this->userService->getUserByUsername($username);

            return $this->successResponse("User {$username} found.",200,['data' => $user]);
        } catch (Exception $e) {
            return $this->failedResponse("Error {$e->getMessage()}",500);
        } 
    }

    public function requestAccountDeletion(Request $request )
    {
        try {
            $user = $request->user();
            
            Mail::to($user->email)->send(new AccountDeletionMail($user));

            return $this->successResponse("Deletion email sent. Please check your inbox.",200);
        } catch (Exception $error) {
            return $this->failedResponse($error,500);
        }
    }

    //for testing, not a major function
    public function checklogin()
    {
        return $this->successResponse("I'm logged in.", 200);
    }
}
