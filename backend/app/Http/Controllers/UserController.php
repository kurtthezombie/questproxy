<?php

namespace App\Http\Controllers;

use App\Events\RegisteredUser;
use App\Mail\AccountDeletionMail;
use App\Models\Booking;
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
    protected $booking;

    public function __construct(UserService $userService, Booking $booking){
        $this->userService = $userService;
        $this->booking = $booking;
    }

    public function show($id)
    {
        try {
            // Start by finding the user by their ID
            $user = User::find($id);

            // If the user is not found, return a 404 response
            if (!$user) {
                return $this->failedResponse("User {$id} not found.", 404);
            }

            
            if ($user->role === 'pilot' || $user->role === 'game pilot') {
                // Eager-load the pilot relationship for pilot roles
                $user->load('pilot');
            } elseif ($user->role === 'gamer') {
                // Eager-load the gamer relationship for gamer roles
                $user->load('gamer');
            }

           

            // Return a success response with the user data, including the eager-loaded relationships
            return $this->successResponse('User profile retrieved.', 200, ['user' => $user]);

        } catch (ModelNotFoundException $e) {
            // Catch ModelNotFoundException specifically for 404 errors
            return $this->failedResponse("User {$id} not found.", 404);
        } catch (Exception $e) {
            
            Log::error("Error fetching user profile for ID {$id}: " . $e->getMessage());
            return $this->failedResponse("An error occurred while retrieving the user profile.", 500);
        }
    }

    public function index()
    {
        $users = User::all();

        return $this->successResponse('User records retrieved successfully.', 200, ['users' => $users]);
    }

    public function create(Request $request)
    {
        try {
            //validate inputs
            $data = $request->validate([
                'username' => 'required|string|unique:users,username',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8',
                'f_name' => 'required|string',
                'l_name' => 'required|string',
                'contact_number' => 'required|string|max:15',
                'role' => 'required|string',
            ]);

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

            if ($this->hasUnfinishedActivities($user)){
                return $this->failedResponse("Cannot delete account with active services or unfinished bookings.", 500);
            }

            Mail::to($user->email)->send(new AccountDeletionMail($user));

            return $this->successResponse("Deletion email sent. Please check your inbox.",200);
        } catch (Exception $error) {
            return $this->failedResponse($error,500);
        }
    }

    private function hasUnfinishedActivities(User $user)
    {
        $hasAvailableServices = false;
        $hasActiveBookingsByClient = false;

        if ($user->pilot) {
            $pilot = $user->pilot;

            $hasAvailableServices = $pilot->services()->where('availability', true)->exists();
        }

        $hasActiveBookingsByClient = $this->booking->where('client_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        return $hasAvailableServices || $hasActiveBookingsByClient;
    }

    //for testing, not a major function
    public function checklogin()
    {
        return $this->successResponse("I'm logged in.", 200);
    }
}
