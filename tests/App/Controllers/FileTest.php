<?php

namespace Tests\App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use App\Models\FileModel;

class FileTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testIndexReturnsView()
    {
        $result = $this->call('get', 'file');
        
        $result->assertStatus(200);
        $result->assertSee('File Shift - Fast & Secure File Conversion Platform');
    }

    public function testAllowedConversionsReturnsJson()
    {
        $response = $this->postJson('file/allowedConversions', [
            'mime_types' => ['image/png']
        ]);
        
        $response->assertStatus(200);
        $response->assertJSONFragment(['image/png']);
    }

    public function testRemoveInvalidFiles()
    {
        $response = $this->postJson('file/remove', ['files' => []]);
        
        $response->assertStatus(400);
        $response->assertJSONFragment(['status' => 'error']);
    }

    public function testUploadInvalidFile()
    {
        $response = $this->post('file/upload', []);
        
        $response->assertStatus(400);
        $response->assertJSONFragment(['status' => 'error']);
    }

    public function testDownloadSingleInvalidFile()
    {
        $response = $this->get('file/downloadSingle/invalid-id');
        
        $response->assertRedirect();
    }

    public function testDownloadMultipleWithoutFiles()
    {
        $response = $this->get('file/downloadMultiple');
        
        $response->assertRedirect();
    }

    public function testStatusInvalidFiles()
    {
        $response = $this->postJson('file/status', ['files' => []]);
        
        $response->assertStatus(400);
        $response->assertJSONFragment(['status' => 'error']);
    }
}