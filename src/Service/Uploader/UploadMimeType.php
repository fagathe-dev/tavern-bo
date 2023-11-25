<?php
namespace App\Service\Uploader;

final class UploadMimeType {



    /**
     * getMimeTypes
     *
     * @return array
     */
    public static function getMimeTypes(): array {
        return [
            'archive' => [
                'application/zip',
                'application/x-7z-compressed',
                'extensions' => ['.zip', '.7z',],
            ],
            'code' => [
                'text/css',
                'text/html',
                'application/javascript',
                'text/plain',
                'application/xhtml+xml',
                'application/xml',
                'extensions' => ['.css', '.htm', '.html', '.js', '.xml', '.xhtml',],
            ],
            'document' => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/pdf',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.presentation',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'extensions' => ['.doc', '.docx', '.odp', '.ods', '.odt', '.pdf', '.ppt', '.pptx', '.xls', '.xlsx',],
            ],
            'image' => [
                'image/bmp',
                'image/gif',
                'image/x-icon',
                'image/jpeg',
                'image/jpeg',
                'image/png',
                'image/svg+xml',
                'image/tiff',
                'image/tiff',
                'image/webp',
                'extensions' => ['.bmp', '.gif', '.ico', '.jpeg', '.jpg', '.png', '.svg', '.tif', '.tiff', '.webp',],
            ],
            'music' => [
                'extensions' => [''],
            ],
            'text' => [
                'text/plain',
                'text/csv',
                'text/calendar',
                'text/html',
                'text/plain',
                'extensions' => ['.txt', '.csv', '.html', '.html', '.ics',]
            ],
            'video' => [
                'extensions' => [''],
            ],
        ];
    }

    /**
     * getDocumentMimeType
     *
     * @param  mixed $fileType : 'image' | 'code'
     * @return array
     */
    public static function getDocumentMimeType(?string $fileType = null): ?array {
        if($fileType === null || !array_key_exists($fileType, self::getMimeTypes())) {
            return null;
        }

        return self::getMimeTypes()[$fileType];
    }

    public static function getMimeTypeExtensions(?string $fileType = null): ?array {
        if($fileType === null || !array_key_exists($fileType, self::getMimeTypes())) {
            return null;
        }
        return self::getMimeTypes()[$fileType]['extensions'];
    }

}