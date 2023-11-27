<?php
namespace App\Service\Import;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImportCsvService {

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param UploadedFile|null $file
     * 
     * @return array|null
     */
    private function parseCsv(?UploadedFile $file = null): ?array {
        $data = [];
        try {
            if(($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                $this->logger->info('Start parsing file...');
                $i = 0;
                while(($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $data[$i] = $row;
                    $i++;
                }
                fclose($handle);
                $this->logger->info('End parsing file.');
            }
        } catch (Exception $e) {
            $message = sprintf('An error occured when parsing the data from csv ! %s', $e->getMessage());
            $this->logger->error($message);
            throw new Exception($message);
        }

        return count($data) > 1 ? $data : null;
    }

    /**
     * [getDataFromCsv get data from parsed csv file] 
     *
     * @param UploadedFile|null $file
     * 
     * @return array|null
     */
    public function getDataFromCsv(?UploadedFile $file = null): ?array {
        $rawData = $this->parseCsv($file);

        if(is_null($rawData)) {
            return $rawData;
        }

        $columns = $rawData[0];
        unset($rawData[0]);
        $rows = $rawData;

        $data = [];

        foreach($rows as $i => $row) {
            $object = [];
            foreach($row as $k => $v) {
                $object[$columns[$k]] = $v;
            }
            $data[$i] = (object)$object;
        }

        return $data;
    }
}