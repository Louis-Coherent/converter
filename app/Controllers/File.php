<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use Config\Services;

class File extends Controller
{

    private $session;

    function __construct()
    {
        $this->session = Services::session();
    }

    public function index()
    {
        $currentFiles = $this->session->get('files');

        return view('index', ['files' => $currentFiles]);
    }

    public function upload()
    {
        $currentFiles = $this->session->get('files');

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

        $filePath = $file->store();

        $uuid = service('uuid');
        $uuid4 = $uuid->uuid4()->toString();

        $fileModel->insert([
            'file_name' => $file->getClientName(),
            'file_path' => $filePath,
            'status' => 'pending', // Initial status
            'file_id' => $uuid4,  // Insert binary UUID into DB
        ]);

        $currentFiles[] = [
            'id' => $uuid4,
            'name' => $file->getClientName(),
            'progress' => 0,
            'selectedConversion' => $convert,
            'isConverting' => false,
            'errorMessage' => '',
            'status' => 'pending',
        ];

        $this->session->set('files', $currentFiles);

        return $this->response->setJSON(['status' => 'success', 'message' => 'File uploaded successfully!', 'unique_id' => $uuid4]);
    }

    public function status()
    {
        $validation = Services::validation();

        $validation->setRules([
            'files.*' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid file IDs.']);
        }

        $fileModel = new FileModel();

        $fileIds = $this->request->getJSON(true)['files'];

        $files = $fileModel->whereIn('file_id', $fileIds)->findAll();

        $response = [];

        foreach ($files as $file) {

            //Update session file status
            $currentFiles = $this->session->get('files');

            foreach ($currentFiles as $key => $currentFile) {
                if ($currentFile['id'] == $file['file_id']) {
                    $currentFiles[$key]['status'] = $file['status'];
                    $this->session->set('files', $currentFiles);
                    break;
                }
            }

            $response[] = [
                'id' => $file['file_id'],
                'status' => $file['status'],
            ];
        }

        return $this->response->setJSON($response);
    }
}
