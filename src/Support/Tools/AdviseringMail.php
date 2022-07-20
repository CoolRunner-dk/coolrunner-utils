<?php

namespace CoolRunner\Utils\Support\Tools;


use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdviseringMail
{
    const TEMPORARY_BUCKET = "cr-temporary";

    const EMAIL_DELIMITERS = '/(;|,)/';

    private string $from_name, $from_email, $email_subject;
    private array $to, $cc, $bcc;

    private array $data, $attachment, $header, $local_attachments;

    private ?string $customer, $customer_id;


    public static function create(): AdviseringMail
    {
        return new AdviseringMail();
    }

    public function __construct()
    {
        $this->from_email = "noreply@coolrunner.dk";
        $this->from_name = $this->email_subject = "Coolrunner";
        $this->attachment = $this->header = $this->local_attachments = $this->data = $this->cc = $this->bcc = [];
    }

    public function from($from_email, $from_name = "")
    {
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * Attaches receivers\n 
     * for appending names, see: https://stackoverflow.com/questions/26584904/laravel-mailsend-sending-to-multiple-to-or-bcc-addresses
     */
    public function to(array|string $to)
    {
        $this->to = $this->formatEmails($to);
        return $this;
    }

    public function bcc(array $bcc)
    {
        $this->bcc = $this->formatEmails($bcc);
        return $this;
    }

    public function cc(array $cc)
    {
        $this->cc = $this->formatEmails($cc);
        return $this;
    }

    public function subject($subject)
    {
        $this->email_subject = $subject;
        return $this;
    }

    public function withAttachment(array $attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

    public function withLocalAttachment(array $local_attachments)
    {
        if (!array_key_exists("s3", config("filesystems.disks"))) {
            throw new Exception("Invalid s3 disk - can't find s3 disk in config files. please add filesystems.disks.s3");
        }


        $this->local_attachments = $local_attachments;
        return $this;
    }



    public function withData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function withHeader(array $header)
    {
        $this->header = $header;
        return $this;
    }

    /** 
     * Use for tracking and analytics purposes.
     */
    public function withCustomer($customer, $customer_id)
    {
        $this->customer = $customer;
        $this->customer_id = $customer_id;
        return $this;
    }

    public function send($view, $locale = "da")
    {

        if ($this->to == null || empty($this->to)) {
            throw new Exception("To parameter cant be empty, please call the ->to() function before calling ->send()!");
        }

        if ($this->local_attachments != null && !empty($this->local_attachments)) {
            foreach ($this->local_attachments as $attachment) {
                $bucket = isset($attachment["bucket"]) ? $attachment["bucket"] : self::TEMPORARY_BUCKET;
                $this->addS3BucketConfig($bucket);

                $uuid = Str::uuid();
                $path = str_replace(["-", "."], "_", "$view/$uuid/{$attachment['filename']}");
                Storage::disk($bucket)->put($path, $attachment["content"]);

                $this->attachment[] = [
                    "name" => $path,
                    "bucket" => $bucket,
                    "filename" => $attachment["filename"]
                ];
            }
        }

        Advisering::sendMail(
            from_name: $this->from_name ?? "",
            from_email: $this->from_email ?? "",
            to_email: $this->to,
            subject: $this->email_subject ?? "",
            data: $this->data ?? [],
            attachment: $this->attachment ?? [],
            view_name: $view,
            customer: $this->customer ?? "",
            customer_id: $this->customer_id ?? -1,
            locale: $locale,
            cc: $this->cc ?? [],
            bcc: $this->bcc ?? [],
            header: $this->header ?? [],
        );
    }

    private function addS3BucketConfig($bucket)
    {
        // Add temporary bucket, if it doesnt already exists
        if (config("filesystems.disks." . $bucket) == null) {

            return config([
                ("filesystems.disks." . $bucket) => array_merge(config('filesystems.disks.s3'), ['bucket' => $bucket]),
            ]);
        }
    }

    private function formatEmails(array|string $subjects): array
    {
        $emails = is_array($subjects) ? $subjects : [$subjects];
        $formatted = [];

        foreach ($emails as $email) {

            if (is_string($email)) {

                foreach ($this->formatEmail($email) as $formatted_email) {
                    $formatted[] = $formatted_email;
                }
            }


            if (is_array($email)) {

                foreach ($this->formatEmail($email["email"]) as $formatted_email) {
                    $formatted[] = [
                        "name" => $email["name"],
                        "email" => $formatted_email
                    ];
                }
            }
        }

        return $formatted;
    }

    private function formatEmail(string $subject): array
    {
        $cleaned = str_replace(" ", "", $subject);
        return preg_split(self::EMAIL_DELIMITERS, $cleaned);
    }
}
