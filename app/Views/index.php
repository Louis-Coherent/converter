<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fileUpload()" x-init="init()">
    <div class="flex">
        <div class="flex-1 p-4">
            <h2 class="text-2xl mb-4">File Upload and Conversion</h2>

            <!-- File Upload Form -->
            <input type="file" x-ref="fileInput" @change="handleFileSelect" multiple class="block mb-4" />

            <!-- (Optional) Conversion Type Selector -->
            <select>
                <option value="pdf">PDF</option>
                <option value="docx">Word</option>
                <option value="txt">Text</option>
            </select>

            <!-- Table to show file status -->
            <table class="min-w-full table-auto mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2">File Name</th>
                        <th class="px-4 py-2">Conversion Type</th>
                        <th class="px-4 py-2">Progress</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(file, index) in selectedFiles" :key="index">
                        <tr>
                            <td class="px-4 py-2" x-text="file.name"></td>
                            <td class="px-4 py-2">
                                <select x-model="file.selectedConversion" class="block w-full p-2 border rounded">
                                    <option value="pdf">PDF</option>
                                    <option value="docx">Word</option>
                                    <option value="txt">Text</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <div class="w-full bg-gray-300 h-4 mb-1">
                                    <div class="bg-green-500 h-4" :style="'width: ' + file.progress + '%'"></div>
                                </div>
                                <span x-text="file.progress + '%'"></span>
                            </td>
                            <td class="px-4 py-2">
                                <span x-text="file.status"></span>
                                <div class="text-red-500" x-show="file.errorMessage" x-text="file.errorMessage"></div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Button to start conversion -->
            <button @click="handleFileConvert" class="bg-blue-500 text-white px-4 py-2 mt-4">
                Convert Files
            </button>
        </div>
    </div>
</div>

<script>
    function fileUpload() {
        return {
            selectedFiles: <?= json_encode($files) ?? [] ?>,
            pollingInterval: null,


            // Called when the user selects files
            handleFileSelect(event) {
                // Create an object for each selected file
                this.selectedFiles = Array.from(event.target.files).map(file => ({
                    file: file,
                    fileid: null, // This will be set after upload
                    name: file.name,
                    progress: 0,
                    selectedConversion: 'pdf',
                    isConverting: false,
                    errorMessage: '',
                    status: 'pending'
                }));
                console.log('Files selected:', this.selectedFiles);
            },

            // Called when "Convert Files" is clicked
            async handleFileConvert() {
                // Upload all files in parallel
                const uploadPromises = this.selectedFiles.map(fileObj => this.uploadFile(fileObj));
                await Promise.all(uploadPromises);
                console.log('All files uploaded. Starting polling...');

                await this.pollStatuses();
            },

            async pollStatuses() {
                // Start polling every 5 seconds if not already polling
                if (!this.pollingInterval) {
                    this.pollingInterval = setInterval(() => {
                        this.getFileStatus();
                    }, 5000);
                }
            },

            // Upload a single file to the backend
            async uploadFile(fileObj) {
                const formData = new FormData();
                formData.append('file', fileObj.file);
                formData.append('conversion_type', fileObj.selectedConversion);
                try {
                    const response = await fetch('/file/upload', {
                        method: 'POST',
                        body: formData
                    });
                    if (response.ok) {
                        const data = await response.json();
                        // Update file object with returned file ID
                        fileObj.fileid = data.unique_id;
                        fileObj.status = 'pending';
                        fileObj.isConverting = true;
                        console.log('File uploaded successfully:', data);
                    } else {
                        const errorData = await response.json();
                        fileObj.errorMessage = errorData.message || 'Unknown error occurred.';
                        fileObj.status = 'error';
                        console.error('Upload error:', errorData);
                    }
                } catch (error) {
                    console.error('Error during file upload:', error);
                    fileObj.errorMessage = 'Error during file upload';
                    fileObj.status = 'error';
                }
            },

            // Poll the backend for status of all uploaded files
            async getFileStatus() {
                const fileIds = this.selectedFiles
                    .filter(file => file.fileid != null)
                    .map(file => file.fileid);
                if (fileIds.length === 0) {
                    console.log('No file IDs available for polling.');
                    return;
                }
                try {
                    const response = await fetch('/file/status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            files: fileIds
                        })
                    });
                    if (response.ok) {
                        const data = await response.json();
                        console.log('Polling response:', data);
                        // Update each file's status and progress
                        data.forEach(statusObj => {
                            const file = this.selectedFiles.find(f => f.fileid === statusObj.id);
                            if (file) {
                                file.status = statusObj.status;
                                file.progress = statusObj.progress;
                            }
                        });
                        // If all files are either completed or in error, stop polling
                        if (this.allFilesComplete()) {
                            console.log('All files complete or error. Stopping polling.');
                            clearInterval(this.pollingInterval);
                            this.pollingInterval = null;
                        }
                    } else {
                        console.error('Error fetching file status:', response.status);
                    }
                } catch (error) {
                    console.error('Error fetching file status:', error);
                }
            },

            // Returns true if every file is either "completed" or "error"
            allFilesComplete() {
                return this.selectedFiles.every(file =>
                    file.status === 'completed' || file.status === 'error'
                );
            },

            init() {
                console.log('File upload component initialized.');
            }
        };
    }



    document.addEventListener('alpine:init', () => {
        Alpine.start();


    });
</script>

<?= $this->endSection('content') ?>