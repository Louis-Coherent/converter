<?php

namespace App\Libraries;

use CodeIgniter\Files\File;
use Exception;
use App\Models\FileModel;

class FilesAdmin
{
    public  static function findAndSend()
    {
        $fileModel = new FileModel();
        $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $files = $fileModel->where('updated_at >=', $fiveMinutesAgo)->findAll();

        if (!empty($files)) {

            $email = \Config\Services::email();

            $email->setTo('louis@coherent-innovations.co.uk', 'admin');
            $email->setSubject('Files Converted in the Last 5 Minutes');
            $email->setMessage(view('email-templates/admin/found-files', ['files' => $files]));

            if ($email->send()) {
                echo 'Email sent successfully!';
            } else {
                echo 'Failed to send email.';
                print_r($email->printDebugger(['headers']));
            }
        }

        return null;
    }
}
