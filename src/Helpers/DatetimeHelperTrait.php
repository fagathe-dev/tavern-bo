<?php
namespace App\Helpers;

use DateTimeImmutable;

trait DateTimeHelperTrait
{
    /**
     * now
     *
     * @return DateTimeImmutable
     */
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable;
    }
}