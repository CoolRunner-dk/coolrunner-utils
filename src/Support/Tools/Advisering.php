<?php

namespace CoolRunner\Utils\Support\Tools;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class Advisering
{
    /**
     * Creates a mail entity, which will be sent by mailer service
     *
     * @param string $from_name name of the sender. Could be CoolRunner, HomeRunner, etc.
     * @param string $from_email email of the sender. Could be CoolRunner@no-reply.dk, HomeRunner@no-reply.dk, etc.
     * @param string $to_email - recipient email
     * @param string $to_name -  recipient name
     * @param string $subject - subject of the mail
     * @param array $data key/value array
     * @param array $attachment 
     * @param string $view_name - name of the blade view to send. Must be in the mailservice repository.
     * @param ?string $customer
     * @param ?int $customer_id 
     * @param string $locale - language code, "da" for danish, "no" for norwegian
     *
     */
    public static function sendMail(
        string $from_name,
        string $from_email,
        string $to_email,
        string $to_name,
        string $subject,
        array $data,
        array $attachment,
        string $view_name,
        ?string $customer,
        ?int $customer_id,
        string $locale = "da",
    ) {

        return DB::connection("advisering")
            ->table('cr_mails')
            ->insert([
                "from_email" => $from_email,
                "from_name" => $from_name,
                "email" => $to_email,
                "to" => $to_name,
                "subject" => $subject,
                "data" => json_encode($data),
                "attachment" => $attachment,
                "view" => $view_name,
                "tracking_uuid" => Str::uuid(),
                "locale" => $locale,
                "customer" => $customer,
                "customer_id" => $customer_id,
                "created_at" => now(),
                "updated_at" => now(),
            ]);

        return $mail;
    }

    /**
     * Creates a sms entity, which will be sent by mailer service
     *
     * @param array $recipients array of numbers which should get the sms
     * @param string $message message which should be sent. Must be in mailservice repository, sms localization file.
     * @param string $sender - Sender of the sms, most likely CoolRunner or HomeRunner
     * @param string $locale - locale of the sms
     * @param array $data -  key/value
     *
     */
    public static function sendSMS(
        array $recipients,
        string $message,
        string $sender = "CoolRunner",
        string $locale = "da",
        array $data = [],
    ) {


        return DB::connection("advisering")
            ->table('cr_sms')
            ->insert([
                "recipients" => json_encode($recipients),
                "message" => $message,
                "sender" => $sender,
                "tracking_uuid" => Str::uuid(),
                "data" => json_encode($data),
                "locale" => $locale,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
    }
}
