<?php

namespace Tests\Unit;

use CoolRunner\Utils\Support\Tools\Advisering;
use CoolRunner\Utils\Tests\TestCase;

class AdviseringSMSTest extends TestCase
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
}
