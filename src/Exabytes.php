<?php

namespace Premgthb\ExabytesSms;

use Exception;
use Illuminate\Support\Facades\Http;

class Exabytes
{
    /**
     * Generate OTP function
     */
    public function generateOtp()
    {
        $otp = sprintf("%04d", mt_rand(0, 9999));

        return $otp;
    }

    /**
     * Retrieve message
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }


    /**
     * Send message function
     */
    public function sendMessage($data)
    {

        if (app()->environment('production') || app()->environment('staging')) {

            $message = $data['message'];
            $to = $data['to'];

            $response = Http::get('https://smsportal.exabytes.my/isms_send.php', [
                'un' => env('EXABYTES_SMS_USERNAME'),
                'pwd' => env('EXABYTES_SMS_PASSWORD'),
                'dstno' => $to,
                'msg' => $message,
                'type' => 1,
                'agreedterm' => 'YES'
            ]);

            if ($response->body() === '-1004 = INSUFFICIENT CREDITS') {
                Bugsnag::notifyError('Exabyte SMS Endpoint Failure', 'Error Code: ' . $response->getStatusCode() . ',Message: ' . $response->body());
            }

            return response()->json(['message' => 'SMS sent successfully to' . $data['dstno']], 200);
        }
 
    }
}
