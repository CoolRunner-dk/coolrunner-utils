<?php

namespace CoolRunner\Utils\Support\Tools;

use DateTime;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AdviseringMail
{

    private string $from_name, $from_email, $email_subject;
    private array $to, $cc, $bcc;

    private array $data, $attachment, $header;

    private ?string $customer, $customer_id;


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
}
