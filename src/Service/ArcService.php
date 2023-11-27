<?php
namespace App\Service;

use App\Service\Import\ImportCsvService;
use App\Utils\ServiceTrait;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ArcService {
    use ServiceTrait;

    public function __construct(
        private ImportCsvService $importCsvService
    ) {
    }

    public function import(Form $form): array {
        $arcName = $form->get("name")->getData();
        $file = $form->get("file")->getData();
        $data = $this->importCsvService->getDataFromCsv($file);

        return [];
    }

}
