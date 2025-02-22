<?php

namespace App\Jobs;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;
use App\Libraries\Converter;
use App\Models\FileModel;
use Config\FileConversion;

class ConvertFiles extends BaseJob implements JobInterface
{
    public function process()
    {
        $converter = new Converter();

        $fileModel = new FileModel();

        $fileModel->update($this->data['id'], ['status' => FileConversion::PROCESSING]);


        $from = $this->data['from'];
        $to = $this->data['to'];
        $filePath = $this->data['filePath'];

        $response = $converter->convert($from, $to, $filePath);

        if ($response['status'] === 'error') {
            $fileModel->update($this->data['id'], ['status' => FileConversion::FAILED, 'error_message' => $response['error_message']]);
            return;
        }

        $fileModel->update($this->data['id'], ['status' => FileConversion::COMPLETE, 'converted_file_path' => $response['file']]);
    }
}
