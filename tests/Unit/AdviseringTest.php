<?php

namespace Tests\Unit;

use CoolRunner\Utils\Support\Tools\Advisering;
use CoolRunner\Utils\Support\Tools\AdviseringMail;
use CoolRunner\Utils\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

class AdviseringTest extends TestCase
{
    public function test_that_send_sms_works()
    {
        Advisering::fake();

        $now = now();
        $res = Advisering::sendSMS(
            ["53381465"],
            "message_coolrunner_$now",
            "sender_coolrunner_$now",
            "da",
        );

        Advisering::assertHas("sms", [
            "sender" => "sender_coolrunner_$now"
        ]);
    }

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
