<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FileModel;
use Config\Services;
use Config\FileConversion;
use Config\Logger as LoggerConfig;
use CodeIgniter\Log\Logger as Logger;

use function PHPUnit\Framework\fileExists;

class File extends Controller
{

    private $session;
    private $allowedConversions;

    function __construct()
    {
        $this->session = Services::session();

        $this->allowedConversions = FileConversion::mimeTypes;
    }

    public function index($from = null, $to = null)
    {
        $currentFiles = $this->session->get('files');

        $allowedMimeType = array_keys($this->allowedConversions);

        $title = (!empty($from) ? strtoupper($from) . ' to ' . strtoupper($to) . ' ' : '') . 'Fast & Secure File Conversion Platform';
        $metaTitle = (!empty($from) ? strtoupper($from) . ' to ' . strtoupper($to) . ' ' : '') . 'Fast & Secure File Conversion Platform';

        return view('index', ['files' => $currentFiles, 'allowedMimeType' => $allowedMimeType, 'from' => $from, 'to' => $to, 'title' => $title, 'metaTitle' => $metaTitle]);
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

        if (ENVIRONMENT == 'production') {
            if ($this->checkUserConversions()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'maxedconversions', 'message' => 'You have reached the maximum number of conversions for today.']);
            }
        }


        $currentFiles = $this->session->get('files');

        $fileModel = new FileModel();

        $allowedFileTypes = array_keys($this->allowedConversions);

        $validationRule = [
            'file' => [
                'label' => 'File',
                'rules' => [
                    'uploaded[file]',
                    'max_size[file,204800]',
                ],
            ],
            'convert_to' => [
                'label' => 'Convert To',
                'rules' => 'required',
            ],
        ];

        $file = $this->request->getFile('file');

        $uploadedFileMimeType = $file->getMimeType() ?? '';
        $convertFileType = $this->request->getPost('convert_to');

        // Validate file type and size
        if (!$file->isValid() || !$this->validate($validationRule) || !in_array($uploadedFileMimeType, $allowedFileTypes)) {
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

        $response = service('queue')->push('convert-files', 'file-converter', ['id' => $fileModel->getInsertID(), 'from' => $uploadedFileMimeType, 'to' => $convertFileType, 'filePath' => $filePath]);

        if (!$response) {
            $fileModel->update($fileModel->getInsertID(), ['status' => FileConversion::FAILED, 'error_message' => 'Failed to queue file for conversion.']);
            return $this->response->setStatusCode(500)->setJSON(['status' => FileConversion::FAILED, 'message' => 'Failed to queue file for conversion.']);
        }

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

        if (auth()->loggedIn() && auth()->user()->toRawArray()['is_premium'] == 1) {
            return false;
        }

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
            return redirect()->back()->with('alert', ['message' => 'File not found.', 'type' => 'error']);
        }

        if ($file['status'] != FileConversion::COMPLETE || empty($file['converted_file_path'])) {
            return redirect()->back()->with('alert', ['message' => 'File not ready for download.', 'type' => 'error']);
        }

        $filePath = WRITEPATH . 'converted_files/' . $file['converted_file_path'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('alert', ['message' => 'File not found.', 'type' => 'error']);
        }

        return $this->response->download($filePath, null)->setFileName($file['og_file_name'] . '.' . $file['format_to']);
    }

    public function downloadMultiple()
    {
        $fileModel = new FileModel();

        $currentFiles = $this->session->get('files');

        if (empty($currentFiles)) {
            return redirect()->back()->with('alert', ['message' => 'No files to download.', 'type' => 'error']);
        }

        foreach ($currentFiles as $file) {
            if ($file['status'] == FileConversion::COMPLETE && !empty($file['converted_file_path']) && file_exists(WRITEPATH . 'converted_files/' . $file['converted_file_path'])) {
                $fileIds[] = $file['id'];
            }
        }

        if (empty($fileIds)) {
            return redirect()->back()->with('alert', ['message' => 'No files ready to download.', 'type' => 'error']);
        }

        $tempDir = WRITEPATH . 'temp/';

        $tmp_file = $tempDir . 'file-shift-' . time() . '.zip';

        $zip = new \ZipArchive();
        $zipOpenResult = $zip->open($tmp_file, \ZipArchive::CREATE);

        if ($zipOpenResult !== true) {
            return redirect()->back()->with('alert', ['message' => "Could not create ZIP file.", 'type' => 'error']);
        }

        $filesAdded = 0;
        foreach ($fileIds as $fileId) {
            $file = $fileModel->findUuid($fileId);

            if (!$file) {
                continue; // Skip to next file
            }

            $filePath = WRITEPATH . 'converted_files/' . $file['converted_file_path'];

            if (!file_exists($filePath)) {
                continue; // Skip to next file
            }

            $zip->addFile($filePath, $file['og_file_name'] . '.' . $file['format_to']);
            $filesAdded++;
        }

        $zip->close();

        if ($filesAdded === 0 || !file_exists($tmp_file)) {
            unlink($tmp_file); // Cleanup empty zip file
            return redirect()->back()->with('alert', ['message' => 'No valid files to download.', 'type' => 'error']);
        }

        $zipFile = file_get_contents($tmp_file);
        unlink($tmp_file); // Cleanup empty zip file

        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="file-shift.zip"')
            ->setBody($zipFile)
            ->send();
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
