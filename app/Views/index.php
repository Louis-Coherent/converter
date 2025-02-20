<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="fileUpload()">
    <div class="flex">
        <!-- Sidebar (Optional) -->

        <!-- Main Content -->
        <div class="flex-1 p-4">
            <h2 class="text-2xl mb-4">File Upload and Conversion</h2>

            <!-- File Upload Form -->
            <input type="file" x-ref="fileInput" @change="handleFileSelect" multiple class="block mb-4" />

            <select>
                <option value="pdf">PDF</option>
                <option value="docx">Word</option>
                <option value="txt">Text</option>
                <!-- Add more options as needed -->
            </select>

            <!-- Table to Show Files and Options -->
            <table class="min-w-full table-auto">
                <thead>
                    <tr>
                        <th class="px-4 py-2">File Name</th>
                        <th class="px-4 py-2">Conversion Type</th>
                        <th class="px-4 py-2">Progress</th>
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
                                    <!-- Add more options as needed -->
                                </select>
                            </td>
                            <td class="px-4 py-2">
                            <td class="px-4 py-2">
                                <!-- Only show progress bar if there's no error message -->
                                <div class="w-full bg-gray-300 h-4 mb-4" x-show="!file.errorMessage">
                                    <div class="bg-green-500 h-4" :style="'width: ' + file.progress + '%'"></div>
                                </div>

                                <!-- Show error message if there is one -->
                                <div class="text-red-500" x-show="file.errorMessage" x-text="file.errorMessage"></div>
                            </td>

                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Button to Convert Files -->
            <button @click="handleFileConvert" class="bg-blue-500 text-white px-4 py-2 mt-4">Convert Files</button>
        </div>
    </div>
</div>

<script>
    function fileUpload() {
        return {
            selectedFiles: [],
            handleFileSelect(event) {
                // When files are selected, populate selectedFiles with file information
                this.selectedFiles = Array.from(event.target.files).map((file) => ({
                    file: file,
                    name: file.name,
                    progress: 0,
                    selectedConversion: 'pdf', // Default conversion type
                    isConverting: false,
                    errorMessage: '', // Initially, no error
                    status: 'pending',
                }));

                // Automatically upload files when selected
            },
            handleFileConvert() {
                this.selectedFiles.forEach(fileObj => {
                    this.uploadFile(fileObj);
                });
            },
            async uploadFile(fileObj) {
                const formData = new FormData();
                formData.append('file', fileObj.file);
                formData.append('conversion_type', fileObj.selectedConversion); // Use the selected conversion type

                try {
                    // Simulate file upload (replace with actual server API call)
                    const response = await fetch('/file/convert', {
                        method: 'POST',
                        body: formData,
                    });

                    // Update progress on the frontend
                    fileObj.isConverting = true;
                    if (response.ok) {
                        // If file upload succeeds, handle success (you can update the progress bar to 100% if necessary)
                        fileObj.progress = 100;
                        console.log('File uploaded successfully!');
                    } else {
                        // If the server returns an error, handle it
                        const errorData = await response.json();
                        fileObj.errorMessage = errorData.message || 'Unknown error occurred.';
                        fileObj.progress = 0; // Reset progress
                        fileObj.status = 'error';
                    }
                } catch (error) {
                    console.error('Error during file upload:', error);
                    fileObj.errorMessage = 'Error during file upload';
                    fileObj.progress = 0; // Reset progress
                }
            }
        };
    }
</script>

<?= $this->endSection('content') ?>