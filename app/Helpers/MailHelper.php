<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class MailHelper
{
    public static function sendExternalMail($sToEmail, $sMailTitle, $view, $viewData, $sFrom)
    {
        $url = 'https://user-request.bgiglass.com/user_request/?r=API/Mail';
        $description = view($view, $viewData)->render();
        $data = [
            'sToEmail' => $sToEmail,
            'sSubject' => $sMailTitle,
            'sDescription' => $description,
            'sFrom' => $sFrom,
        ];

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpStatus = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $blockedByWebApplicationFirewall = is_string($response)
            && (str_contains($response, '_Incapsula_Resource')
                || str_contains($response, 'incap_ses_'));
        $externalApiSucceeded = $error === ''
            && $httpStatus >= 200
            && $httpStatus < 300
            && !$blockedByWebApplicationFirewall;

        if ($externalApiSucceeded) {
            return $response;
        }

        Log::warning('External mail API unavailable; using SMTP fallback.', [
            'to' => self::maskEmail($sToEmail),
            'http_status' => $httpStatus,
            'curl_error' => $error ?: null,
            'blocked_by_waf' => $blockedByWebApplicationFirewall,
        ]);

        try {
            Mail::html($description, function ($message) use ($sToEmail, $sMailTitle) {
                $message->to($sToEmail)->subject($sMailTitle);
            });
        } catch (Throwable $exception) {
            $externalFailure = $blockedByWebApplicationFirewall
                ? 'Mail API ถูกระบบป้องกันเว็บปฏิเสธ'
                : 'Mail API ไม่พร้อมใช้งาน';

            Log::error('All mail delivery methods failed.', [
                'to' => self::maskEmail($sToEmail),
                'external_http_status' => $httpStatus,
                'external_error' => $error ?: ($blockedByWebApplicationFirewall ? 'Blocked by WAF' : null),
                'smtp_error' => $exception->getMessage(),
            ]);

            throw new RuntimeException(
                'ไม่สามารถส่งอีเมลได้: '.$externalFailure.' และ SMTP สำรองเชื่อมต่อไม่ได้',
                0,
                $exception
            );
        }

        return 'smtp';
    }

    private static function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return 'invalid-email';
        }

        return mb_substr($name, 0, 2).'***@'.$domain;
    }
}
