<?php

namespace Tests\Unit;

use CoolRunner\Utils\Support\Tools\Advisering;
use CoolRunner\Utils\Support\Tools\AdviseringMail;
use CoolRunner\Utils\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

class AdviseringMailTest extends TestCase
{

    public function test_that_send_mail_works()
    {
        Advisering::fake();


        $now = now();
        Advisering::sendMail(
            "from_name_$now",
            "from@email.com",
            ["to@email.com",],
            "subject",
            ["data" => true],
            [],
            "view",
            "testrunner",
            -1,
        );


        Advisering::assertHas("email", [
            "from_name" => "from_name_$now"
        ]);
    }

    public function test_that_emails_are_properly_formatted()
    {
        Advisering::fake();


        $to = "jb@coolrunner.dk";
        $bcc = [["name" => "bcc", "email" => "bcc@example.com"]];
        $cc = ["email1@example.dk; email2@example.io", "email3@example.dk"];

        $expected_to = ["jb@coolrunner.dk"];
        $expected_bcc = [["name" => "bcc", "email" => "bcc@example.com"]];
        $expected_cc = ["email1@example.dk", "email2@example.io", "email3@example.dk"];



        AdviseringMail::create()
            ->cc($cc)
            ->bcc($bcc)
            ->to($to)
            ->send("test");


        Advisering::assertHas("email", [
            "emails" => json_encode($expected_to),
            "cc" => json_encode($expected_cc),
            "bcc" => json_encode($expected_bcc)

        ]);
    }

    public function test_that_send_mail_chaining_works()
    {
        Advisering::fake();


        $now = now();
        $mailer = new AdviseringMail();
        $mailer->to("jb@coolrunner.dk")
            ->from("jb@coolrunner.dk", "Coolrunner")
            ->subject("chaining_mail_test")
            ->withData(["test" => true, "123" => "string"])
            ->withCustomer("testrunner_$now", 1)
            ->bcc(["jb@coolrunner.dk"])
            ->cc(["jb@coolrunner.dk"])
            ->send("emails.trustpilot");

        Advisering::assertHas("email", [
            "subject" => "chaining_mail_test",
            "customer" => "testrunner_$now",
        ]);
    }

    public function test_that_send_mail_chaining_works_with_multiple_receivers()
    {
        Advisering::fake();

        $now = now();
        $mailer = new AdviseringMail();
        $mailer->to(["jb@coolrunner.dk", "test@example.com"])
            ->from("jb@coolrunner.dk", $now)
            ->subject("chaining_mail_test")
            ->withData(["test" => true, "123" => "string"])
            ->withCustomer("testrunner_$now", 1)
            ->bcc(["jb@coolrunner.dk"])
            ->cc(["jb@coolrunner.dk"])
            ->send("emails.trustpilot");


        Advisering::assertHas("email", [
            "subject" => "chaining_mail_test",
            "customer" => "testrunner_$now",
            "from_name" => $now,
        ]);
    }
}
