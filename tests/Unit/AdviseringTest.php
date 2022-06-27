<?php

namespace Tests\Unit;

use CoolRunner\Utils\Support\Tools\Advisering;
use CoolRunner\Utils\Support\Tools\AdviseringMail;
use CoolRunner\Utils\Tests\TestCase;

class AdviseringTest extends TestCase
{
    public function test_that_send_sms_works()
    {
        $now = now();
        $res = Advisering::sendSMS(
            ["53381465"],
            "message_coolrunner_$now",
            "sender_coolrunner_$now",
            "da",
        );


        $this->assertTrue($res);
        $this->assertDatabaseHas("sms", [
            "sender" => "sender_coolrunner_$now"
        ], "advisering");
    }

    public function test_that_send_mail_works()
    {
        $now = now();
        $res = Advisering::sendMail(
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


        $this->assertTrue($res);
        $this->assertDatabaseHas("mails", [
            "from_name" => "from_name_$now"
        ], "advisering");
    }

    public function test_that_send_mail_chaining_works()
    {
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

        $this->assertDatabaseHas("mails", [
            "subject" => "chaining_mail_test",
            "customer" => "testrunner_$now",
        ], "advisering");
    }

    public function test_that_send_mail_chaining_works_with_multiple_receivers()
    {
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

        $this->assertDatabaseHas("mails", [
            "subject" => "chaining_mail_test",
            "customer" => "testrunner_$now",
            "from_name" => $now,
        ], "advisering");
    }
}
