<?php

namespace App\Http\Controllers\Admin;

use App\Av;
use App\Booking;
use App\Comment;
use App\Config;
use App\Notification;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    /**
     * @param Request $request
     * @return array
     */
    public function registerNotification(Request $request)
    {
        $notification = new Notification();
        $notificationEncoded = json_encode($request->notification);
        $notification->data = $notificationEncoded;
        $notification->type = json_decode($notificationEncoded, true)['type'];
        $notification->isRead = 0;
        $notification->object= json_encode(json_decode($notificationEncoded, true)['object']);
        $notification->save();

        return ['notification' => $notification];
    }

    /**
     * @param Request $request
     */
    public function markNotificationAsRead(Request $request)
    {
        $id = $request->id;
        $notification = Notification::findOrFail($id);
        $notification->isRead = 1;
        $notification->save();
    }

    /**
     * @return array
     */
    public function markAllAsRead()
    {
        $notifications = Notification::where('isRead', '=', 0)->get();
        foreach ($notifications as $notification) {
            $notification->isRead = 1;
            $notification->save();
        }

        return ['notifications' => $notifications];
    }

    /**
     * Deletes all notifications
     */
    public function deleteAllNotifications()
    {
        $notifications = Notification::where('softDelete', '=', 0)->get();
        foreach ($notifications as $notification) {
            $notification->softDelete = 1;
            $notification->save();
        }
    }

    /**
     * Deletes selected notification
     *
     * @param Request $request
     */
    public function deleteNotification(Request $request)
    {
        $notification = Notification::findOrFail($request->id);
        $notification->softDelete = 1;
        $notification->save();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notificationDetails($id)
    {
        $notification = Notification::findOrFail($id);
        $notificationType = $notification->type;
        $objectID = json_decode($notification->object, true)['id'];
        $config = Config::where('userID', '=', -1)->first();
        if ($notificationType == 'USER_REGISTER' || $notificationType == 'COMPANY_REGISTER') {
            $object = User::findOrFail($objectID);
        } else if ($notificationType == 'CITYZORE_BOOKING' || $notificationType == 'GYG_BOOKING' || $notificationType == 'BOKUN_BOOKING') {
            $object = Booking::findOrFail($objectID);
        } else if ($notificationType == 'AVAILABILITY_EXPIRED') {
            $object = Av::findOrFail($objectID);
        } else if ($notificationType == 'NEW_COMMENT') {
            $object = Comment::findOrFail($objectID);
        } else if ($notificationType = 'TICKET_ALERT') {
            $object = Av::findOrFail($objectID);
        } else {
            abort(404);
        }

        return view('panel.notifications.details', ['config' => $config, 'notificationType' =>$notificationType, 'object' => $object]);
    }

}
