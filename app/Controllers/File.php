<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use Config\Services;
use Config\FileConversion;
use Config\Logger as LoggerConfig;
use CodeIgniter\Log\Logger as Logger;

class File extends Controller
{

    private $session;
    private $allowedConversions;

    function __construct()
    {
        $this->session = Services::session();

        $this->allowedConversions = FileConversion::mimeTypes;
    }

    public function index()
    {
        $currentFiles = $this->session->get('files');

        $allowedMimeType = array_keys($this->allowedConversions);


        return view('index', ['files' => $currentFiles, 'allowedMimeType' => $allowedMimeType]);
    }

    public function allowedConversions()
    {
        $request = $this->request->getJSON();
        $mimeTypes = $request->mime_types ?? [];

        // Initialize an empty response array
        $response = [];

        $extGrouped = FileConversion::extGrouped;


        // Loop through the mime types provided in the request
        foreach ($mimeTypes as $mime) {
            // If mime type has allowed conversions, proceed
            if (isset($this->allowedConversions[$mime])) {
                // Get the allowed extensions for this mime type
                $allowedExtensions = $this->allowedConversions[$mime];

                // Group the allowed extensions
                foreach ($allowedExtensions as $extension) {
                    foreach ($extGrouped as $group => $extensions) {
                        // If the extension belongs to a specific group, add it to that group
                        if (in_array($extension, $extensions)) {
                            $groupedExtensions[$group][] = $extension;
                        }
                    }
                }

                // Add the grouped extensions to the response
                $response[$mime] = $groupedExtensions;
            } else {
                $response[$mime] = []; // If no allowed conversions, return an empty array
            }
        }

        // Return the grouped extensions as a JSON response
        return $this->response->setJSON($response);
    }


    public function remove()
    {
        $validation = Services::validation();

        $validation->setRules([
            'files.*' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Failed to remove: Invalid files.']);
        }

        $files = $this->request->getJsonVar('files');

        $currentFiles = $this->session->get('files');

        $removedFiles = [];
        foreach ($files as $file) {
            $fileModel = new FileModel();
            $fileModel->delete(['id' => $file]);

            // Use a new array instead of modifying the current one by reference
            $currentFiles = array_filter($currentFiles, function ($currentFile) use ($file, &$removedFiles) {
                if ($currentFile['id'] == $file) {
                    $removedFiles[] = $currentFile; // Track the removed file
                    return false; // This will remove the file from the currentFiles
                }
                return true; // Keep the file in the currentFiles
            });
        }

        // Optionally reindex the array after the filter
        $currentFiles = array_values($currentFiles);

        $this->session->set('files', $currentFiles);

        return $this->response->setJSON(['status' => 'success', 'message' => 'File removed successfully!', 'files' => $removedFiles]);
    }

    public function upload()
    {

        // if ($this->checkUserConversions()) {
        //     return $this->response->setStatusCode(400)->setJSON(['status' => 'maxedconversions', 'message' => 'You have reached the maximum number of conversions for today.']);
        // }

        $currentFiles = $this->session->get('files');

        $fileModel = new FileModel();

        $validation = Services::validation();

        $allowedFileTypes = array_keys($this->allowedConversions);
        $validation->setRules([
            'file' => 'uploaded[file]|mime_in[file,' . implode(',', $allowedFileTypes) . ']|max_size[file,10240]',
            'convert_to' => 'required',
        ]);
        $file = $this->request->getFile('file');

        $uploadedFileMimeType = $file->getMimeType();
        $convertFileType = $this->request->getPost('convert_to');

        // Validate file type and size
        if (!$file->isValid() || !$validation->withRequest($this->request)->run()) {
            $this->logConversions('Upload type: ' . $uploadedFileMimeType . ' - To type: ' . $convertFileType, 'error');
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Uploaded file not supported.']);
        }

        if (!in_array($convertFileType, $this->allowedConversions[$uploadedFileMimeType])) {
            $this->logConversions('Upload type: ' . $uploadedFileMimeType . ' - To type: ' . $convertFileType, 'error');
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Conversion type not supported.']);
        }

        $fileName = $file->getRandomName();

        $filePath = $file->store('files', $fileName);

        $uuid = service('uuid');
        $uuid4 = $uuid->uuid4()->toString();

        $fileModel->insert([
            'og_file_name' => $file->getClientName(),
            'file_name' => $fileName,
            'ip' => $this->request->getIPAddress(),
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

    private function checkUserConversions()
    {
        $ipAddress = $this->request->getIPAddress();
        $fileModel = new FileModel();

        $todayDate = date('Y-m-d');

        $userFiles = $fileModel->selectCount('id')
            ->where('ip', $ipAddress)  // Filter by user IP address
            ->where("DATE(created_at)", $todayDate)  // Filter by today's date (ignores the time)
            ->first();

        return $userFiles['id'] >= 5;
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

    private function logConversions($message, $level = 'info')
    {
        $loggerConfig = new LoggerConfig();
        $customPath = 'logs/unsupported-conversions/';
        $loggerConfig->handlers['CodeIgniter\Log\Handlers\FileHandler']['path'] = WRITEPATH . $customPath;

        $logger = new Logger($loggerConfig);
        $logger->log($level, $message);
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
