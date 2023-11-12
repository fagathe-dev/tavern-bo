<?php
namespace App\Enum;

interface EnumInterface
{
    /**
     * @param  mixed $value
     * @return string
     */
    public static function match(string|int $value = ''): string;


    /**
     * @return array
     */
    public static function cases(): array;
    
    /**
     * choices
     *
     * @return array
     */
    public static function choices(): array;
}