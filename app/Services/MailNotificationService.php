<?php

namespace App\Services;

use App\Mail\HgnlNotificationMail;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailNotificationService
{
    public function send(
        ?string $toEmail,
        string $subject,
        string $eventType,
        array $mailData,
        ?int $userId = null
    ): bool {
        if (empty($toEmail)) {
            Log::warning('Email skipped because recipient email is empty', [
                'event_type' => $eventType,
                'user_id' => $userId,
            ]);

            return false;
        }

        $mailData['subject'] = $subject;

        $emailLog = EmailLog::create([
            'user_id' => $userId,
            'email' => $toEmail,
            'event_type' => $eventType,
            'subject' => $subject,
            'status' => 'pending',
            'payload' => $mailData,
        ]);

        try {
            Mail::to($toEmail)->send(new HgnlNotificationMail($mailData));

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Email sending failed', [
                'event_type' => $eventType,
                'user_id' => $userId,
                'email' => $toEmail,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}