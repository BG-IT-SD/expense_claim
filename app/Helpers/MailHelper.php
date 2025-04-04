<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class MailHelper
{
    public static function sendExternalMail($sToEmail, $sMailTitle, $view, $viewData, $sFrom)
{
    $sUrl = 'https://user-request.bgiglass.com/user_request/?r=API/Mail';

    //render blade view เป็น string
    $sMailDescription = view($view, $viewData)->render();

    $aData = [
        'sToEmail' => $sToEmail,
        'sSubject' => $sMailTitle,
        'sDescription' => $sMailDescription,
        'sFrom' => $sFrom,
    ];

    $oCh = curl_init($sUrl);
    curl_setopt($oCh, CURLOPT_POST, 1);
    curl_setopt($oCh, CURLOPT_POSTFIELDS, $aData);
    curl_setopt($oCh, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($oCh, CURLOPT_HEADER, 0);
    curl_setopt($oCh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCh, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($oCh);
    $error = curl_error($oCh);
    curl_close($oCh);

    if ($error) {
        // \Log::error('SendMail Error', [
        //     'error' => $error,
        //     'to' => $sToEmail,
        //     'subject' => $sMailTitle,
        //     'from' => $sFrom,
        // ]);
        return false;
    }

    return $response;
}
}
