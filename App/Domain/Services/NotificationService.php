<?php

namespace App\Domain\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use App\Models\Notification;
use App\Services\EmailTemplateService;

/**
 * NotificationService
 * Handles email and in-app notifications for booking status changes
 */
class NotificationService
{
    private $mailConfig;
    private $emailTemplateService;
    private $notificationModel;
    private $logger;

    public function __construct()
    {
        $this->mailConfig = require BASE_PATH . '/Config/mail.php';
        $this->emailTemplateService = new EmailTemplateService();
        $this->notificationModel = new Notification();
        $this->initLogger();
    }

    /**
     * Send booking status change notification (email + in-app)
     * 
     * @param array $booking Booking details with user info
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @param bool $sendEmail Whether to send email notification
     * @param bool $createInApp Whether to create in-app notification
     * @return array ['success' => bool, 'email_sent' => bool, 'notification_created' => bool, 'message' => string]
     */
    public function notifyStatusChange($booking, $oldStatus, $newStatus, $sendEmail = true, $createInApp = true)
    {
        $result = [
            'success' => false,
            'email_sent' => false,
            'notification_created' => false,
            'message' => ''
        ];

        try {
            $userId = $booking['user_id'] ?? null;
            $email = $booking['email'] ?? null;

            // Send email notification
            if ($sendEmail && $email) {
                if ($this->sendStatusChangeEmail($booking, $oldStatus, $newStatus)) {
                    $result['email_sent'] = true;
                    $this->log("Email sent to {$email} for booking #{$booking['id']} status change");
                } else {
                    $this->log("Failed to send email to {$email} for booking #{$booking['id']}", 'warning');
                }
            }

            // Create in-app notification
            if ($createInApp && $userId) {
                if ($this->createStatusChangeNotification($booking, $oldStatus, $newStatus)) {
                    $result['notification_created'] = true;
                    $this->log("In-app notification created for user #{$userId}");
                } else {
                    $this->log("Failed to create in-app notification for user #{$userId}", 'warning');
                }
            }

            // Consider success if at least one notification method worked
            $result['success'] = $result['email_sent'] || $result['notification_created'];
            $result['message'] = $result['success']
                ? 'Notification sent successfully'
                : 'Failed to send notifications';
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            $this->log("Notification error: " . $e->getMessage(), 'error');
        }

        return $result;
    }

    /**
     * Send bookinhg status change email
     * 
     * @param array $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @return bool
     */
    public function sendStatusChangeEmail($booking, $oldStatus, $newStatus)
    {
        $email = $booking['email'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->log("Invalid email address: {$email}", 'warning');
            return false;
        }

        $template = $this->emailTemplateService->getStatusChangeEmailTemplate($booking, $oldStatus, $newStatus);

        return $this->send(
            to: $email,
            subject: $template['subject'],
            body: $template['body'],
            isHtml: true
        );
    }

    /**
     * Create in-app notification for status change
     * 
     * @param array $booking
     * @param string $oldStatus
     * @param string $newStatus
     * @return bool
     */
    public function createStatusChangeNotification($booking, $oldStatus, $newStatus)
    {
        $userId = $booking['user_id'] ?? null;

        if (!$userId) {
            return false;
        }

        try {
            $statusConfig = require BASE_PATH . '/Config/booking_status.php';
            $oldStatusLabel = $statusConfig[strtolower($oldStatus)]['label'] ?? ucfirst($oldStatus);
            $newStatusLabel = $statusConfig[strtolower($newStatus)]['label'] ?? ucfirst($newStatus);

            $subject = "Cập nhật trạng thái đặt phòng #{$booking['booking_code']}";
            $message = "Trạng thái của đặt phòng #{$booking['booking_code']} đã được cập nhật từ '$oldStatusLabel' thành '$newStatusLabel'.";

            return $this->notificationModel->create([
                'user_id' => $userId,
                'booking_id' => $booking['id'],
                'type' => 'status_change',
                'subject' => $subject,
                'message' => $message,
                'old_status' => strtolower($oldStatus),
                'new_status' => strtolower($newStatus),
                'is_read' => 0
            ]);
        } catch (\Exception $e) {
            $this->log("Failed to create notification: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Send generic email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param bool $isHtml Whether body is HTML
     * @param string|null $replyTo Reply-to address
     * @return bool
     */
    public function send($to, $subject, $body, $isHtml = true, $replyTo = null)
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->log("Invalid email: {$to}", 'warning');
            return false;
        }

        $driver = $this->mailConfig['driver'] ?? 'mail';

        if ($driver === 'smtp') {
            return $this->sendViaSmtp($to, $subject, $body, $isHtml, $replyTo);
        } else {
            return $this->sendViaMail($to, $subject, $body, $isHtml, $replyTo);
        }
    }

    /**
     * Send email via SMTP
     */
    private function sendViaSmtp($to, $subject, $body, $isHtml = true, $replyTo = null)
    {
        try {
            $mail = new PHPMailer(true);
            $smtpConfig = $this->mailConfig['smtp'];

            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtpConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtpConfig['username'];
            $mail->Password = $smtpConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtpConfig['port'];

            // Recipients
            $mail->setFrom($smtpConfig['from_address'], $smtpConfig['from_name']);
            $mail->addAddress($to);

            if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo);
            }

            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if ($isHtml) {
                $mail->AltBody = strip_tags($body); // Plain text version
            }

            // Send
            if ($mail->send()) {
                $this->log("Email sent successfully to {$to}");
                return true;
            }

            $this->log("Failed to send email to {$to}: " . $mail->ErrorInfo, 'warning');
            return false;
        } catch (PHPMailerException $e) {
            $this->log("PHPMailer error: {$e->errorMessage()}", 'error');
            return false;
        } catch (\Exception $e) {
            $this->log("Error sending email: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Send email via PHP mail() function
     */
    private function sendViaMail($to, $subject, $body, $isHtml = true, $replyTo = null)
    {
        try {
            $mailConfig = $this->mailConfig['mail'];

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: " . ($isHtml ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
            $headers .= "From: {$mailConfig['from_name']} <{$mailConfig['from_address']}>\r\n";

            if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $headers .= "Reply-To: {$replyTo}\r\n";
            }

            $result = mail($to, $subject, $body, $headers);

            if ($result) {
                $this->log("Email sent via mail() to {$to}");
                return true;
            }

            $this->log("Failed to send email via mail() to {$to}", 'warning');
            return false;
        } catch (\Exception $e) {
            $this->log("Error with mail(): " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        try {
            return $this->notificationModel->update($notificationId, [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            $this->log("Error marking notification as read: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications($userId)
    {
        try {
            return $this->notificationModel->findBy([
                'user_id' => $userId,
                'is_read' => 0
            ]);
        } catch (\Exception $e) {
            $this->log("Error fetching notifications: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Initialize logger
     */
    private function initLogger()
    {
        // Can be extended to use a proper logger
    }

    /**
     * Log message
     */
    private function log($message, $level = 'info')
    {
        $logFile = BASE_PATH . '/logs/notifications.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}\n";

        @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

