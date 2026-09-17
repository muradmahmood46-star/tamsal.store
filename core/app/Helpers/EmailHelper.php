<?php

/**
 * Created by UniverseCode.
 */

namespace App\Helpers;

use App\{
    Models\EmailTemplate,
    Models\Setting
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\{
    PHPMailer,
    Exception
};

class EmailHelper
{

    public $mail;
    public $setting;

    public function __construct()
    {
        $this->setting = Setting::first();

        $this->mail = new PHPMailer(true);

        if ($this->setting && $this->setting->smtp_check == 1) {

            $this->mail->isSMTP();
            $this->mail->Host       = $this->setting->email_host;
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = trim($this->setting->email_user);
            $this->mail->Password   = trim($this->setting->email_pass);

            $encryption = strtolower(trim($this->setting->email_encryption ?? ''));
            $port = (int) $this->setting->email_port;

            if ($encryption == 'ssl' || $port == 465) {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption == 'tls' || $port == 587) {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $this->mail->SMTPSecure = false;
                $this->mail->SMTPAutoTLS = false;
            }

            $this->mail->Port       = $port ?: 587;
            $this->mail->CharSet    = 'UTF-8';
            $this->mail->Timeout    = 20;

            // Permissive SSL context options for cPanel / hosting environments
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        }
    }

    public function sendTemplateMail(array $emailData)
    {
        $template = EmailTemplate::whereType($emailData['type'])->first();
        if (!$template) {
            return false;
        }

        try {
            $email_body = preg_replace("/{user_name}/", $emailData['user_name'] ?? '', $template->body);
            $email_body = preg_replace("/{order_cost}/", $emailData['order_cost'] ?? '', $email_body);
            $email_body = preg_replace("/{transaction_number}/", $emailData['transaction_number'] ?? '', $email_body);
            $email_body = preg_replace("/{site_title}/", $this->setting->title ?? 'Namartzone', $email_body);

            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $fromEmail = $this->setting->email_from ?: ($this->setting->email_user ?: 'no-reply@namartzone.store');
            $fromName = $this->setting->email_from_name ?: ($this->setting->title ?: 'Namartzone');

            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addAddress($emailData['to']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $template->subject;
            $this->mail->Body = $email_body;
            $this->mail->send();

            if ($this->setting && $this->setting->order_mail == 1) {
                $this->adminMail($emailData);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Template email error: ' . $e->getMessage());
        }

        return true;
    }

    public function sendCustomMail(array $emailData)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $fromEmail = $this->setting->email_from ?: ($this->setting->email_user ?: 'no-reply@namartzone.store');
            $fromName = $this->setting->email_from_name ?: ($this->setting->title ?: 'Namartzone');

            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addAddress($emailData['to']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $emailData['subject'];
            $this->mail->Body = $emailData['body'];

            $this->mail->send();
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Custom Email error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendTestMail(string $toEmail)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $fromEmail = $this->setting->email_from ?: ($this->setting->email_user ?: 'no-reply@namartzone.store');
            $fromName = $this->setting->email_from_name ?: ($this->setting->title ?: 'Namartzone');

            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addAddress($toEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Test Email Notification - ' . ($this->setting->title ?? 'Namartzone');

            $siteTitle = htmlspecialchars($this->setting->title ?? 'Namartzone');
            $this->mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 10px; background-color: #ffffff; color: #1e293b;">
                <div style="text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; margin-bottom: 20px;">
                    <h2 style="color: #1e40af; margin: 0 0 5px 0;">' . $siteTitle . '</h2>
                    <p style="color: #10b981; font-weight: bold; margin: 0;">✓ SMTP Email Configuration Working Successfully!</p>
                </div>
                <p style="font-size: 15px; line-height: 1.6;">Hello Admin,</p>
                <p style="font-size: 14px; line-height: 1.6;">This is a test email sent from your website <strong>' . $siteTitle . '</strong>. If you are receiving this message, your Gmail/SMTP email settings are configured properly and working 100%.</p>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 13px;">
                    <p style="margin: 4px 0;"><strong>Host:</strong> ' . htmlspecialchars($this->setting->email_host ?? 'N/A') . '</p>
                    <p style="margin: 4px 0;"><strong>Port:</strong> ' . htmlspecialchars($this->setting->email_port ?? 'N/A') . '</p>
                    <p style="margin: 4px 0;"><strong>Encryption:</strong> ' . htmlspecialchars($this->setting->email_encryption ?? 'N/A') . '</p>
                    <p style="margin: 4px 0;"><strong>From:</strong> ' . htmlspecialchars($fromEmail) . '</p>
                    <p style="margin: 4px 0;"><strong>Sent At:</strong> ' . date('Y-m-d H:i:s') . '</p>
                </div>
                <p style="font-size: 13px; color: #64748b;">Order Confirmation, Delivery Updates, and other customer notifications will now be delivered automatically through this email service.</p>
            </div>';

            $this->mail->send();
            return ['status' => true, 'message' => __('Test email sent successfully to ') . $toEmail];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Test email error: ' . $e->getMessage());
            return ['status' => false, 'message' => __('SMTP Connection Failed: ') . $e->getMessage()];
        }
    }

    public static function getEmail()
    {
        $user = Auth::user();
        if (isset($user)) {
            $email = $user->email;
        } else {
            $email = Session::get('billing_address')['bill_email'] ?? null;
        }
        return $email;
    }

    public function adminMail(array $emailData)
    {
        try {
            $template = EmailTemplate::whereType('New Order Admin')->first();
            if (!$template) {
                return;
            }

            $email_body = preg_replace("/{user_name}/", $emailData['user_name'] ?? '', $template->body);
            $email_body = preg_replace("/{order_cost}/", $emailData['order_cost'] ?? '', $email_body);
            $email_body = preg_replace("/{transaction_number}/", $emailData['transaction_number'] ?? '', $email_body);
            $email_body = preg_replace("/{site_title}/", $this->setting->title ?? 'Namartzone', $email_body);

            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $fromEmail = $this->setting->email_from ?: ($this->setting->email_user ?: 'no-reply@namartzone.store');
            $fromName = $this->setting->email_from_name ?: ($this->setting->title ?: 'Namartzone');

            $this->mail->setFrom($fromEmail, $fromName);
            $this->mail->addAddress($this->setting->contact_email);
            $this->mail->isHTML(true);
            $this->mail->Subject = $template->subject;
            $this->mail->Body = $email_body;

            $this->mail->send();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Admin mail error: ' . $th->getMessage());
        }
    }
}
