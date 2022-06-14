<?php

namespace CoolRunner\Utils\Support\Tools;

use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class Advisering
{
    /**
     * Creates a mail entity, which will be sent by mailer service. For multiple recipients, use AdviseringMail which utilises method chaining
     *
     * @param string $from_name name of the sender. Could be CoolRunner, HomeRunner, etc.
     * @param string $from_email email of the sender. Could be CoolRunner@no-reply.dk, HomeRunner@no-reply.dk, etc.
     * @param string $to_email - recipient email
     * @param string $subject - subject of the mail
     * @param array $data key/value array
     * @param array $attachment 
     * @param string $view_name - name of the blade view to send. Must be in the advisering repository.
     * @param ?string $customer
     * @param ?int $customer_id 
     * @param string $locale - language code, "da" for danish, "no" for norwegian
     * @param array $cc 
     * @param array $bcc 
     */
    public static function sendMail(
        string $from_name,
        string $from_email,
        array $to_email,
        string $subject,
        array $data,
        array $attachment,
        string $view_name,
        ?string $customer,
        ?int $customer_id,
        string $locale = "da",
        ?array $cc = [],
        ?array $bcc = [],
        ?array $header = [],
    ) {
        return DB::connection("advisering")
            ->table('mails')
            ->insert([
                "from_email" => $from_email,
                "from_name" => $from_name,
                "emails" => json_encode($to_email),
                "subject" => $subject,
                "data" => json_encode($data),
                "attachment" => json_encode($attachment),
                "view" => $view_name,
                "tracking_uuid" => Str::uuid(),
                "locale" => $locale,
                "customer" => $customer ?? "",
                "customer_id" => $customer_id ?? -1,
                "created_at" => now(),
                "updated_at" => now(),
                "header" => json_encode($header ?? []),
                "cc" => json_encode($cc ?? []),
                "bcc" => json_encode($bcc ?? []),
            ]);
    }

    /**
     * Creates a sms entity, which will be sent by mailer service
     *
     * @param array $recipients array of numbers which should get the sms
     * @param string $message message which should be sent. Must be in advisering repository, sms localization file.
     * @param string $sender - Sender of the sms, most likely CoolRunner or HomeRunner
     * @param string $locale - locale of the sms
     * @param array $data -  key/value
     * @param DateTime $send_at
     *
     */
    public static function sendSMS(
        array $recipients,
        string $message,
        string $sender = "CoolRunner",
        string $locale = "da",
        array $data = [],
        ?DateTime $send_at = null,
    ) {
        return DB::connection("advisering")
            ->table('sms')
            ->insert([
                "recipients" => json_encode($recipients),
                "message" => $message,
                "sender" => $sender,
                "tracking_uuid" => Str::uuid(),
                "data" => json_encode($data),
                "locale" => $locale,
                "send_at" => $send_at ?? now(),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
    }
}
