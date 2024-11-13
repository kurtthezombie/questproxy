<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    //to do: revise this one since there is pilot notification
    //but maybe think about centralizing stuff
    public function index (Request $request){
        $user = $request->user();

        $notifications = $user->notifications()->latest()->get();
        //revise response
        return $this->successResponse('Notifications retrieved',200,['notifications' => $notifications]);
    }

    public function markAsRead($id){
        $notification = auth()->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['status' => 'Notification marked as read.']);
    }

    public function markAllAsRead() {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'All notifications marked as read.']);
    }
}
