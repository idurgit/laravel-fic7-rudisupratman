<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Midtrans\CallbackService;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\User;

class CallbackController extends Controller
{
    public function sendNotificationToUser($userId, $message)
    {
        $user = User::find($userId);
        $token = $user->fcm_token;

        $messaging = app('firebase.messaging');
        $notification = Notification::create('Order Terbayar', $message);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);

        $messaging->send($message);
    }
    
    public function callback()
    {
        $callback = new CallbackService;

        // if ($callback->isSignatureKeyVerified()) {
        $notification = $callback->getNotification();
        $order = $callback->getOrder();

        if ($callback->isSuccess()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 2,
            ]);
        }

        if ($callback->isExpire()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 3,
            ]);
        }

        if ($callback->isCancelled()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 3,
            ]);
        }

        $this->sendNotificationToUser($order->seller_id, 'Order ' . $order->total_price . ' Telah Terbayar');

        return response()
            ->json([
                'success' => true,
                'message' => 'Notifikasi berhasil diproses',
            ]);
        //         } else {
        //             return response()
        //                 ->json([
        //                     'error' => true,
        //                     'message' => 'Signature key tidak terverifikasi',
        //                 ], 403);
        //         }
    }
}
