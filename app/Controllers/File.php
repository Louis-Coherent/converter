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

        $fileModel = new FileModel();
        $filePath = $file->store(); // Save the file to the server

        // Insert the file record into the database
        $fileModel->insert([
            // 'file_name' => $file->getName(),
            'file_path' => $filePath,
            'status' => 'pending', // Initial status
            // 'queue_id' => null,
            // 'original_mime_type' => $uploadedFileMimeType,
            // 'converted_mime_type' => null,
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'File uploaded successfully!', 'unique_id' => $uniqueId]);
    }

    // Function to process the file in the queue (dummy example)
    private function processFileInQueue($uniqueId)
    {
        // Here, you'd use a real queue system (e.g., Redis or RabbitMQ)
        // This is a mock function simulating the processing of a file

        // Example: You would normally queue the file conversion job with its unique identifier
        // Example: Queue job with unique ID and file path, which will be processed in the background.
    }
}
