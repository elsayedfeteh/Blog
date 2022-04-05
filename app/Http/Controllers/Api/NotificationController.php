<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ResponseJsonTrait;

    /**
     * Get All Notifications
     *
     * @group Notifications
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();

        $notifications = $user->notifications()->get();

        $notifications->markAsRead();

        return NotificationResource::collection($notifications);
    }

    /**
     * Display the specified Notification
     *
     * @group Notifications
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth('api')->user();

        $notification = $user->notifications()->find($id);

        if (! $notification)
        {
            return $this->responseJson(false, 'Sorry, notification with id ' . $id . ' cannot be found', null, 404);
        }

        $notification->markAsRead();

        if ($notification->data['url_api']) {
            return response()->json([
                'url' => $notification->data['url_api']
            ]);
        } else {
            return response()->json([
                'url' => null
            ]);
        }
    }
}
