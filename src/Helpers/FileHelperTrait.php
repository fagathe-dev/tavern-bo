<?php
namespace App\Helpers;

trait FileHelperTrait
{

    /**
     * formatSizeUnits
     *
     * @param  mixed $bytes
     * @return string
     */
    public function convertFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * convertBytesToMB
     *
     * @param  mixed $bytes
     * @return int
     */
    public function convertBytesToMB(int $bytes): float
    {
        return $bytes >= 0 ? round($bytes / pow(1024, 2), 2) : 0;
    }
}
