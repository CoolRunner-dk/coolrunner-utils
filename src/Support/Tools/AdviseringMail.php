<?php

namespace CoolRunner\Utils\Support\Tools;

use DateTime;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdviseringMail
{
    const TEMPORARY_BUCKET = "cr-temporary";

    private string $from_name, $from_email, $email_subject;
    private array $to, $cc, $bcc;

    private array $data, $attachment, $header, $local_attachments = [];

    private ?string $customer, $customer_id;


    public static function create()
    {
        return new AdviseringMail();
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
        $this->to = is_array($to) ? $to : [$to];
        return $this;
    }

    public function bcc(array $bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function cc(array $cc)
    {
        $this->cc = $cc;
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
        if (!array_key_exists("s3", config(["filesystems.disks"]))) {
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

        if ($this->local_attachments != null && !empty($this->local_attachments)) {
            foreach ($this->local_attachments as $attachment) {
                $uuid = str_replace("-", "", Str::uuid());
                $path = "{$this->view}/$uuid/{$attachment['filename']}";
                Storage::disk(self::TEMPORARY_BUCKET)->put($path, $attachment["content"]);

                $this->attachment[] = [
                    "name" => $path,
                    "bucket" => isset($attachment["bucket"]) ? $attachment["bucket"] : self::TEMPORARY_BUCKET,
                    "filename" => $attachment["filename"]
                ];
            }
        }

        Advisering::sendMail(
            $this->from_name ?? "",
            $this->from_email ?? "",
            $this->to,
            $this->email_subject ?? "",
            $this->data ?? [],
            $this->attachment ?? [],
            $view,
            $this->customer ?? "",
            $this->customer_id ?? -1,
            $locale,
            $this->bcc ?? [],
            $this->cc ?? [],
            $this->header ?? [],
        );
    }

    private function addS3BucketConfig()
    {
        // Add temporary bucket, if it doesnt already exists
        if (config(["filesystems.disks." . self::TEMPORARY_BUCKET])) {
            return config([
                ("filesystems.disks." . self::TEMPORARY_BUCKET) => array_merge(config('filesystems.disks.s3'), ['bucket' => self::TEMPORARY_BUCKET]),
            ]);
        }
    }
}
