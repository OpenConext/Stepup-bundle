<?php
namespace Surfnet\StepupBundle\Tests;

use DateTimeInterface;
use Surfnet\StepupBundle\DateTime\DateTime;

class DateTimeMock extends DateTime
{

    public static function setTime(?DateTimeInterface $date): void
    {
        self::$now = $date;
    }
}