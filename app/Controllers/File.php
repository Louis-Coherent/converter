<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use Config\Services;
use Config\FileConversion;

class File extends Controller
{

    private $session;
    private $allowedConversions;

    function __construct()
    {
        $this->session = Services::session();

        $this->allowedConversions = FileConversion::mimeTypes;

        $this->session->setFlashdata('alert', ['message' => 'Welcome to the File Converter!', 'type' => 'info']);
    }

    public function index()
    {
        $currentFiles = $this->session->get('files');

        return view('index', ['files' => $currentFiles]);
    }

    public function allowedConversions()
    {
        $request = $this->request->getJSON();
        $mimeTypes = $request->mime_types ?? [];

        $response = [];
        foreach ($mimeTypes as $mime) {
            $response[$mime] = $this->allowedConversions[$mime] ?? [];
        }

        return $this->response->setJSON($response);
    }

    public function upload()
    {
        $currentFiles = $this->session->get('files');

        $fileModel = new FileModel();

        $validation = Services::validation();

        $allowedFileTypes = array_keys($this->allowedConversions);
        $validation->setRules([
            'file' => 'uploaded[file]|mime_in[file,' . implode(',', $allowedFileTypes) . ']|max_size[file,10240]',
        ]);
        $file = $this->request->getFile('file');

        $uploadedFileMimeType = $file->getMimeType();
        // Validate file type and size
        if (!$file->isValid() || !$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Uploaded file not supported.']);
        }

        $convertFileType = $this->request->getPost('convert_to');

        if (!in_array($convertFileType, $this->allowedConversions[$uploadedFileMimeType])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Conversion type not supported.']);
        }

        $fileName = $file->getRandomName();

        $filePath = $file->store('files', $fileName);

        $uuid = service('uuid');
        $uuid4 = $uuid->uuid4()->toString();

        $fileModel->insert([
            'og_file_name' => $file->getClientName(),
            'file_name' => $fileName,
            'format_from' => $uploadedFileMimeType,
            'format_to' => $convertFileType,
            'file_path' => $filePath,
            'status' => FileConversion::QUEUED, // Initial status
            'file_id' => $uuid4,  // Insert binary UUID into DB
        ]);

        service('queue')->push('convert-files', 'file-converter', ['id' => $fileModel->getInsertID(), 'from' => $uploadedFileMimeType, 'to' => $convertFileType, 'filePath' => $filePath]);

        $currentFiles[] = [
            'id' => $uuid4,
            'name' => $file->getClientName(),
            'progress' => $this->getProgress(FileConversion::PENDING),
            'selectedConversion' => $convertFileType,
            'allowedConversions' => $this->allowedConversions[$uploadedFileMimeType],
            'isConverting' => false,
            'errorMessage' => '',
            'status' => FileConversion::UPLOADED,
        ];

        $this->session->set('files', $currentFiles);

        return $this->response->setJSON(['status' => 'success', 'message' => 'File uploaded successfully!', 'unique_id' => $uuid4]);
    }

    public function downloadSingle($fileId)
    {
        $fileModel = new FileModel();

        $file = $fileModel->findUuid($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $filePath = WRITEPATH . 'converted_files/' . $file['converted_file_path'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return $this->response->download($filePath, null)->setFileName($file['og_file_name'] . '.' . $file['format_to']);
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
                    $currentFiles[$key]['progress'] = $this->getProgress($file['status']);
                    $this->session->set('files', $currentFiles);
                    break;
                }
            }

            $response[] = [
                'id' => $file['file_id'],
                'status' => $file['status'],
                'progress' => $this->getProgress($file['status']),
            ];
        }

        return $this->response->setJSON($response);
    }

    private function getProgress($status)
    {
        switch ($status) {
            case FileConversion::PENDING:
                return 10;
            case FileConversion::QUEUED:
                return 30;
            case FileConversion::PROCESSING:
                return 50;
            case FileConversion::COMPLETE:
                return 100;
            case FileConversion::FAILED:
                return 100;
            default:
                return 0;
        }
    }
}
