<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use Config\Services;

class File extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function convert()
    {

        $fileModel = new FileModel();

        $validation = Services::validation();

        $conversionConfig = config('FileConversion');

        $allowedFileTypes = array_keys($conversionConfig->mimeTypes);

        $validation->setRules([
            'file' => 'uploaded[file]|mime_in[' . implode(',', $allowedFileTypes) . ']',
        ]);
        $file = $this->request->getFile('file');

        $uploadedFileMimeType = $file->getMimeType();
        // Validate file type and size
        if (!$file->isValid() || !in_array($uploadedFileMimeType, $allowedFileTypes)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid file type.']);
        }

        $convert = $this->request->getPost('conversion_type');

        $uniqueId = uniqid('file_', true);

        $filePath = $file->store();

        $uuid = service('uuid');
        $uuid4 = $uuid->uuid4()->toString();

        $fileModel->insert([
            'file_name' => $file->getName(),
            'file_path' => $filePath,
            'status' => 'pending', // Initial status
            'file_id' => $uuid4,  // Insert binary UUID into DB
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'File uploaded successfully!', 'unique_id' => $uuid4]);
    }

    public function status()
    {
        $validation = Services::validation();

        $validation->setRules([
            'files.*.id' => 'required|uuid',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid file IDs.']);
        }

        $fileModel = new FileModel();

        $fileIds = $this->request->getJSON(true)['files'];

        $files = $fileModel->whereIn('file_id', $fileIds)->findAll();

        $response = [];

        foreach ($files as $file) {
            $response[] = [
                'id' => $file['file_id'],
                'status' => $file['status'],
            ];
        }

        return $this->response->setJSON($response);
    }
}
