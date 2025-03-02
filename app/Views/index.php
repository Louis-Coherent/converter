<?php



?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="nrb8qhwo5gcr71w">
</script>


<section x-data="fileUpload()" id="convert" class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg border rounded-lg">
    <h2 class="text-3xl font-bold mb-8 text-gray-900">
        File <span class="text-blue-600">Upload</span> & <span class="text-blue-600">Convert</span>
    </h2>
    <?php if (!empty($from) && !empty($to)): ?>
        <h2 class="text-lg font-light mt-1 opacity-90 my-2">Convert <?= strtoupper($from) ?> to <?= strtoupper($to) ?></h2>
    <?php endif; ?>

    <div data-umami-event="{Upload Form}" class=" border border-dashed border-gray-300 p-6 py-12 text-center rounded-lg hover:bg-gray-100
        cursor-pointer" @click="$refs.fileInput.click()">
        <?php if (!empty($from) && !empty($to)): ?>
            <p class="text-gray-500">Click to upload <?= strtoupper($from) ?> files or drag & drop</p>
        <?php else: ?>
            <p class="text-gray-500 text-lg">Click to upload files or drag & drop</p>
        <?php endif; ?>
        <input type="file" x-ref="fileInput" accept="<?= implode(', ', $allowedMimeType) ?>" @change="handleFileSelect"
            multiple class="hidden" />

        <div class="flex justify-center gap-4 mt-4">
            <button type="button" @click="uploadFromDropbox" class="bg-blue-500 text-white px-3 py-2 rounded">Upload
                from Dropbox</button>
            <!-- <button type="button" @click="uploadFromOneDrive" class="bg-green-500 text-white px-3 py-2 rounded">Upload
                from OneDrive</button>
            <button type="button" @click="uploadFromGoogleDrive" class="bg-red-500 text-white px-3 py-2 rounded">Upload
                from Google Drive</button> -->
        </div>
    </div>

    <template x-if="selectedFiles.length > 0">
        <div class="w-full mt-8">
            <!-- Header (Hidden on Mobile) -->
            <div class="hidden md:grid grid-cols-[2fr_1.5fr_1.5fr_2fr_0.5fr] bg-gray-100 p-3 font-bold text-gray-800 border-b">
                <div>File Name</div>
                <div>Conversion</div>
                <div>Status</div>
                <div>Progress</div>
                <div></div>
            </div>

            <!-- File Items -->
            <template x-for="(file, index) in selectedFiles" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-[2fr_1.5fr_1.5fr_2fr_0.5fr] gap-3 p-3 border-b items-center">
                    <!-- File Name -->
                    <div class="flex items-center">
                        <span class="font-medium md:hidden text-blue-600">File:</span>
                        <span class="ml-2 truncate" x-bind:title="file.name" x-text="truncate(file.name, 20)"></span>
                    </div>

                    <!-- Conversion Selection -->
                    <div>
                        <span class="font-medium md:hidden text-blue-600">Conversion:</span>
                        <template x-if="file.allowedConversions.length > 0 && file.status === 'pending'">
                            <div x-data="{ dropdownOpen: false }" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen; file.selectedConversion = ''"
                                    class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                    <span x-text="file.selectedConversion || 'Select conversion'"></span>
                                </button>
                                <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                    class="absolute mt-2 bg-white shadow-lg border rounded-md z-50 w-64">
                                    <div class="max-h-64 overflow-y-auto max-h-[400px] overflow-y-auto
  [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:rounded-full
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:rounded-full
  [&::-webkit-scrollbar-thumb]:bg-gray-300
  dark:[&::-webkit-scrollbar-track]:bg-neutral-700
  dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                                        <template x-for="group in file.allowedConversions" :key="group.groupName">
                                            <div>
                                                <div class="font-semibold text-gray-800 p-2 bg-gray-200">
                                                    <span class="text-blue-600" x-text="group.groupName"></span>
                                                </div>
                                                <div class="space-y-1 p-2">
                                                    <template x-for="extension in group.extensions" :key="extension">
                                                        <button @click="file.selectedConversion = extension; dropdownOpen = false"
                                                            class="w-full text-left p-2 hover:bg-gray-200 hover:text-blue-600">
                                                            <span class="uppercase " x-text="extension"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="file.allowedConversions.length === 0">
                            <span class="text-red-500">Unsupported</span>
                        </template>
                        <template x-if="file.status !== 'pending'">
                            <div class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                <span class="uppercase" x-text="file.selectedConversion"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Status -->
                    <div class="my-3 md-my-0">
                        <span class="font-medium px-2 py-1 rounded-md md:ml-0" :class="{
                    'bg-yellow-100 text-yellow-600': file.status === 'pending' || file.status === 'uploading',
                    'bg-blue-100 text-blue-600': file.status === 'uploaded' || file.status === 'queued' || file.status === 'processing',
                    'bg-green-100 text-green-600': file.status === 'complete',
                    'bg-red-100 text-red-600': file.status === 'failed'
                }" x-text="file.status">
                        </span>
                    </div>

                    <!-- Progress -->
                    <div>
                        <template x-if="file.status !== 'complete' && file.status !== 'failed' && file.status !== 'pending' && file.status !== 'uploading'">
                            <div class="relative w-full h-4 bg-gray-300 rounded overflow-hidden">
                                <div class="absolute top-0 left-0 h-full transition-all duration-500 ease-in-out"
                                    :class="{
                            'bg-blue-500 animate-pulse': file.progress > 0 && file.progress < 100,
                            'bg-blue-500': file.progress === 100
                        }" :style="'width: ' + file.progress + '%'">
                                </div>
                            </div>
                        </template>
                        <template x-if="file.status === 'pending' || file.status === 'uploading'">
                            <button data-umami-event="{Single Convert}" @click="handleSingleFileConvert(file)"
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition">
                                <span x-show="!file.isConverting">Convert</span>
                                <span x-show="file.isConverting" class="flex items-center justify-center">
                                    <svg aria-hidden="true" class="w-6 h-6 text-white animate-spin fill-blue-500" viewBox="0 0 100 101"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill="currentColor" d="M100 50.5908C100 78.2051..."></path>
                                    </svg>
                                </span>
                            </button>
                        </template>
                        <template x-if="file.status === 'complete'">
                            <a data-umami-event="{Single Download}" :href="'/file/download/' + file.id"
                                class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">Download</a>
                        </template>
                    </div>

                    <!-- Remove Button -->
                    <div class="text-right">
                        <template x-if="file.status === 'complete' || file.status === 'failed' || file.status === 'pending'">
                            <i data-umami-event="{Single Remove}" @click="handleRemove(file)"
                                class="fa-solid fa-xmark cursor-pointer hover:opacity-50"></i>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <div class="flex justify-between mt-4">
        <!-- Convert Files button -->
        <template x-if="selectedFiles.length > 0 && selectedFiles.some(file => file.id === null && file.file != null)">
            <button @click="handleBulkFileConvert" data-umami-event="Bulk Convert"
                class="bg-blue-500 text-white px-6 py-2 rounded mt-4 me-4 hover:bg-blue-600 transition relative">
                <span x-show="!isConverting">Convert Files</span>
                <span x-show="isConverting" class="absolute inset-0 flex items-center justify-center">
                    <div role="status">
                        <svg aria-hidden="true"
                            class="w-6 h-6 text-gray-200 animate-spin dark:text-white-600 fill-blue-500"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </span>
            </button>
        </template>

        <!-- Download All button -->
        <template
            x-if="selectedFiles.length > 0 && selectedFiles.some(file => file.id !== null && file.status === 'complete')">
            <a href="/file/download-zip" data-umami-event="Bulk Download"
                class="bg-blue-500 text-white px-6 py-2 rounded mt-4 hover:bg-blue-600 transition relative">
                <span>Download All</span>
            </a>
        </template>

        <!-- Remove All button -->
        <template x-if="selectedFiles.length > 0">
            <button @click="removeFiles(selectedFiles)" data-umami-event="Bulk Remove"
                class="bg-red-500 text-white px-6 py-2 rounded mt-4 hover:bg-red-600 transition ml-auto">
                Remove All
            </button>
        </template>
    </div>
</section>

<section class="py-20 my-12 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-8 text-gray-900">
            How <span class="text-blue-600">It Works</span>
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="p-8 bg-white border border-gray-200 rounded-2xl shadow-lg transition hover:shadow-xl">
                <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full mx-auto">
                    <span class="text-2xl font-bold">1</span>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mt-4">Upload Your File</h4>
                <p class="text-gray-600 mt-3">
                    Drag & drop your file into the upload box or click to browse from your device.
                    We support **images, documents, audio, and archives** up to <strong>100MB</strong>.
                    <a href="supported-files" class="text-blue-600 hover:underline">View supported formats</a>.
                </p>
            </div>
            <!-- Step 2 -->
            <div class="p-8 bg-white border border-gray-200 rounded-2xl shadow-lg transition hover:shadow-xl">
                <div class="flex items-center justify-center w-16 h-16 bg-green-600 text-white rounded-full mx-auto">
                    <span class="text-2xl font-bold">2</span>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mt-4">Choose a Format</h4>
                <p class="text-gray-600 mt-3">
                    Select your desired output format from our **extensive list**.
                    Not sure which format to pick? We suggest the **best option** for quality and compatibility.
                </p>
            </div>
            <!-- Step 3 -->
            <div class="p-8 bg-white border border-gray-200 rounded-2xl shadow-lg transition hover:shadow-xl">
                <div class="flex items-center justify-center w-16 h-16 bg-purple-600 text-white rounded-full mx-auto">
                    <span class="text-2xl font-bold">3</span>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mt-4">Download Your File</h4>
                <p class="text-gray-600 mt-3">
                    Our <strong>fast online file converter</strong> processes your file within seconds.
                    Once complete, click the <strong>Download</strong> button to securely save your file.
                </p>
            </div>
        </div>
        <div class="mt-10">
            <a href="#convert" class="px-6 py-3 bg-blue-600 text-white text-lg font-medium rounded-lg shadow-md hover:bg-blue-700 transition">
                Convert Your File Now
            </a>
        </div>
    </div>
</section>


<section class="max-w-6xl mx-auto mt-16 p-10 bg-gray-50 shadow-xl border border-gray-200 rounded-2xl">
    <h2 class="text-3xl font-bold mb-8 text-gray-900 text-center">
        Why Choose Our <span class="text-blue-600">Free Online File Converter?</span>
    </h2>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="flex items-start space-x-4 p-5 bg-white rounded-lg shadow-md">
            <span class="text-blue-600 text-3xl">⚡</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Fast & Efficient Processing</h3>
                <p class="text-gray-600 mt-1">
                    Our conversion engine, powered by <strong>PHP & FFmpeg</strong>, ensures rapid processing
                    with optimized performance, reducing wait times.
                </p>
            </div>
        </div>
        <div class="flex items-start space-x-4 p-5 bg-white rounded-lg shadow-md">
            <span class="text-green-600 text-3xl">📄</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">High-Quality Output</h3>
                <p class="text-gray-600 mt-1">
                    Convert <strong>PDFs, Word documents, images, and audio files</strong> without losing quality.
                    Our <strong>advanced compression algorithms</strong> maintain clarity and sharpness.
                </p>
            </div>
        </div>
        <div class="flex items-start space-x-4 p-5 bg-white rounded-lg shadow-md">
            <span class="text-red-600 text-3xl">🔒</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Secure & Private</h3>
                <p class="text-gray-600 mt-1">
                    Your files are <strong>encrypted</strong> during upload and <strong>automatically deleted</strong>
                    after conversion. We never store or share your data.
                </p>
            </div>
        </div>
        <div class="flex items-start space-x-4 p-5 bg-white rounded-lg shadow-md">
            <span class="text-purple-600 text-3xl">🔄</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Wide Format Support</h3>
                <p class="text-gray-600 mt-1">
                    Our <strong>best file converter</strong> supports <strong>over 50 formats</strong>, including
                    <strong>MP3, MP4, PDF, JPG, PNG, ZIP, DOCX</strong>, and more.
                </p>
            </div>
        </div>
        <div class="flex items-start space-x-4 p-5 bg-white rounded-lg shadow-md md:col-span-2">
            <span class="text-orange-600 text-3xl">🖥️</span>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">User-Friendly & Free</h3>
                <p class="text-gray-600 mt-1">
                    No technical skills required! Our <strong>intuitive drag-and-drop interface</strong> makes it easy
                    for anyone to convert files quickly. Plus, it's <strong>completely free</strong>!
                </p>
            </div>
        </div>
    </div>
    <div class="mt-8 text-center">
        <a href="#convert" class="px-6 py-3 bg-blue-600 text-white text-lg font-medium rounded-lg shadow-md hover:bg-blue-700 transition">
            Start Converting Now
        </a>
    </div>
</section>

<?= $this->include('partials/_faq') ?>

<script>
    function fileUpload() {
        return {
            selectedFiles: <?= json_encode($files ?? []) ?? [] ?>,
            pollingInterval: null,
            isConverting: false, // Track conversion state

            async handleFileSelect(event) {
                const newFiles = Array.from(event.target.files).map(file => ({
                    file: file,
                    id: null,
                    name: file.name,
                    mimeType: file.type,
                    progress: 0,
                    selectedConversion: null,
                    isConverting: false,
                    errorMessage: '',
                    status: 'pending',
                    allowedConversions: []
                }));

                this.selectedFiles = [...this.selectedFiles, ...newFiles]; // Merge new files with the existing ones
                await this.updateAllowedConversions();
            },
            uploadFromDropbox() {
                Dropbox.choose({
                    success: async (files) => {
                        const response = await fetch(files[0].link, {
                            method: 'GET',
                            mode: 'cors' // Ensure CORS mode is enabled
                        });

                        const contentType = response.headers.get('Content-Type');
                        const blob = await response.blob();

                        // Create a File object from the Blob
                        const file = new File([blob], files[0].name, {
                            type: blob.type
                        });

                        file.mimeType = contentType

                        // Trigger the normal file upload process
                        this.handleDropboxFile(file);
                    },
                    linkType: "direct",
                    multiselect: false,
                });
            },

            async handleDropboxFile(file) {
                const newFile = {
                    file: file,
                    id: null,
                    name: file.name,
                    mimeType: file.mimeType,
                    progress: 0,
                    selectedConversion: null,
                    isConverting: false,
                    errorMessage: '',
                    status: 'pending',
                    allowedConversions: []
                };

                // Add the file to the list and update conversions
                this.selectedFiles = [...this.selectedFiles, newFile];
                await this.updateAllowedConversions();
            },
            truncate(string, n) {
                return string.substr(0, n - 1) + (string.length > n ? '...' : '');
            },
            async updateAllowedConversions() {
                // Filter files where id is null
                const mimeTypes = [...new Set(this.selectedFiles.filter(f => f.id === null).map(f => f.mimeType))];

                try {
                    const response = await fetch('/file/allowed-conversions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            mime_types: mimeTypes
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.selectedFiles.forEach(file => {
                            if (file.id === null) {
                                // Get the allowed conversions for the specific mime type
                                const conversions = data[file.mimeType] || {};

                                file.allowedConversions = Object.keys(conversions).map(groupName => ({
                                    groupName,
                                    extensions: conversions[groupName]
                                }));

                                // Set default conversion if allowedConversions has any values
                                if (file.allowedConversions.length > 0) {
                                    // Default to first conversion option
                                    file.selectedConversion = file.allowedConversions[0].extensions[0];
                                }
                            }
                        });
                    }
                } catch (error) {
                    showAlert('Error fetching allowed conversions', 'error');
                }
            },
            async handleSingleFileConvert(file) {
                const uploadPromises = await this.uploadFile(file)
                this.pollStatuses();
            },
            async handleBulkFileConvert() {
                this.isConverting = true;
                const uploadPromises = this.selectedFiles.map(fileObj => this.uploadFile(fileObj));
                await Promise.all(uploadPromises);
                this.isConverting = false;

                this.pollStatuses();
            },
            async handleRemove(file) {
                this.removeFiles([file])
            },
            async removeFiles(files) {
                // Handle if files is a single file (not an array)
                if (!Array.isArray(files)) {
                    files = [files];
                }

                // Remove files with id === null directly from selectedFiles
                this.selectedFiles = this.selectedFiles.filter(file => !files.some(f => f.id === null && f === file));

                // Filter out files with id === null from the passed files
                const filesToRemove = files.filter(file => file.id !== null);
                const fileIds = filesToRemove.map(file => file.id);

                if (fileIds.length === 0) {
                    return; // No files to remove via API, early return
                }

                const response = await fetch('/file/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        files: fileIds
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Remove files from selectedFiles if their id is found in the response
                    this.selectedFiles = this.selectedFiles.filter(
                        file => !data.files.some(status => status.id === file.id)
                    );

                    showAlert('Files removed successfully', 'success');
                    return;
                }

                showAlert('Error removing files', 'error');
            },

            async uploadFile(fileObj) {
                if (!fileObj.selectedConversion || fileObj.id != null || fileObj.file == null || fileObj.status ==
                    'uploading')
                    return;
                const formData = new FormData();
                formData.append('file', fileObj.file);
                formData.append(
                    'convert_to', fileObj.selectedConversion);
                fileObj.isConverting = true;
                fileObj.status = 'uploading'

                try {
                    const response = await fetch('/file/upload', {
                        method: 'POST',
                        body: formData
                    });
                    if (response.ok) {
                        const data = await response.json();
                        fileObj.id = data.unique_id;
                        fileObj.status = 'uploaded';
                        fileObj.progress = 10;
                        fileObj.isConverting = false;

                        showAlert('File uploaded successfully', 'success');

                    } else {
                        const data = await response.json();

                        showAlert(data.message, 'error');

                        fileObj.file = null
                        fileObj.status = 'failed';
                    }
                } catch (error) {
                    showAlert('Error during upload', 'error');
                    fileObj.errorMessage = 'Error during upload';
                    fileObj.status = 'error';
                }
            },
            async pollStatuses() {
                if (!this.pollingInterval) {
                    this.pollingInterval = setInterval(() => this.getFileStatus(), 2000);
                }
            },
            async getFileStatus() {
                const fileIds = this.selectedFiles
                    .filter(file => file.id && file.status !== "complete" && file.status !== "failed")
                    .map(file => file.id);
                if (fileIds.length === 0) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
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
                        data.forEach(statusObj => {
                            const file = this.selectedFiles.find(f => f.id === statusObj.id);
                            if (file) {
                                file.status = statusObj.status;
                                file.progress = statusObj.progress;
                            }
                        });
                        if (this.selectedFiles.every(file => file.status === 'completed' || file.status ===
                                'error')) {
                            clearInterval(this.pollingInterval);
                            this.pollingInterval = null;
                        }
                    } else {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                } catch (error) {
                    console.error('Error fetching status:', error);
                }
            },
            init() {
                this.pollStatuses();
            }
        };
    }
</script>

<?= $this->endSection('content') ?>