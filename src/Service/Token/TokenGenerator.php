<?php
namespace App\Service;

final class TokenGenerator
{

    private const NUMERIC = '1234567890';

    private const ALPHANUMERIC = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890';

    private const UPPERCASE = 'AZERTYUIOPQSDFGHJKLMWXCVBN';

    private const LOWERCASE = 'azertyuiopqsdfghjklmwxcvbn';
    
    /**
     * generate
     *
     * @param  mixed $length
     * @param  mixed $chars
     * @param  mixed $unique
     * @return string
     */
    public function generate(?int $length = 10, ?string $chars = self::ALPHANUMERIC, ?bool $unique = false): string
    {
        if ($unique) {
            return uniqid($this->generateRandomString($length, $chars), true);
        }

        return $this->generateRandomString($length, $chars);
    }
    
    /**
     * generateRandomString
     *
     * @param  mixed $length
     * @param  mixed $chars
     * @return string
     */
    public function generateRandomString(?int $length = 10, ?string $chars = self::ALPHANUMERIC): string
    {
        return substr(str_shuffle($chars), 0, $length);
    }

}
