<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName', 'lastName', 'email', 'phoneNumber', 'password', 'otp', 'otp_verified', 'deviceOS', 'deviceToken', 'userCode', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function sendPush($deviceToken, $message_array) {
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', 'AAAAL0mUelo:APA91bElw-V8UB2A9UObwwT9sUob1Vg4VdhqEwSKHUVbjdrA6UUIDSC51KP9LltZy0Sg4ADhNgl39QYQF2Vd8xmNnbGb03YhzoqFYf2le5vV0wk3FF6y-gIBfI0IGhVIqmaqKshnDd0u');
        }
// generated via the cordova phonegap-plugin-push using "senderID" (found in FCM App Console)
// this was generated from my phone and outputted via a console.log() in the function that calls the plugin
// my phone, using my FCM senderID, to generate the following registrationId 
        $registrationIDs = array(
        );

// prep the bundle
// to see all the options for FCM to/notification payload: 
// https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support 
// 'vibrate' available in GCM, but not in FCM
        $fcmMsg = $message_array;
// I haven't figured 'color' out yet.  
// On one phone 'color' was the background color behind the actual app icon.  (ie Samsung Galaxy S5)
// On another phone, it was the color of the app icon. (ie: LG K20 Plush)
// 'to' => $singleID ;  // expecting a single ID
// 'registration_ids' => $registrationIDs ;  // expects an array of ids
// 'priority' => 'high' ; // options are normal and high, if not set, defaults to high.
        $fcmFields = array(
            'to' => $deviceToken,
            'priority' => 'normal',
            'data' => $fcmMsg,
            'notification' => $fcmMsg
        );

        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result . "\n\n";
    }

    public function sendMultiPush($deviceTokens, $message_array) {
        if (!defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', 'AAAAL0mUelo:APA91bElw-V8UB2A9UObwwT9sUob1Vg4VdhqEwSKHUVbjdrA6UUIDSC51KP9LltZy0Sg4ADhNgl39QYQF2Vd8xmNnbGb03YhzoqFYf2le5vV0wk3FF6y-gIBfI0IGhVIqmaqKshnDd0u');
        }
// generated via the cordova phonegap-plugin-push using "senderID" (found in FCM App Console)
// this was generated from my phone and outputted via a console.log() in the function that calls the plugin
// my phone, using my FCM senderID, to generate the following registrationId 
        $registrationIDs = $deviceTokens;

// prep the bundle
// to see all the options for FCM to/notification payload: 
// https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support 
// 'vibrate' available in GCM, but not in FCM
        $fcmMsg = $message_array;
// I haven't figured 'color' out yet.  
// On one phone 'color' was the background color behind the actual app icon.  (ie Samsung Galaxy S5)
// On another phone, it was the color of the app icon. (ie: LG K20 Plush)
// 'to' => $singleID ;  // expecting a single ID
// 'registration_ids' => $registrationIDs ;  // expects an array of ids
// 'priority' => 'high' ; // options are normal and high, if not set, defaults to high.
        $fcmFields = array(
            'registration_ids' => $registrationIDs,
            'priority' => 'normal',
            'data' => $fcmMsg,
            'notification' => $fcmMsg
        );

        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result . "\n\n";
    }

}
