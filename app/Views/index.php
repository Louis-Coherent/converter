<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Alerts Section -->
<div x-data="alertHandler()" x-init="init()" class="fixed bottom-0 right-0 p-4 md:p-6 space-y-4 z-50">
    <template x-for="alert in alerts" :key="alert.id">
        <div :class="alertClasses(alert)" class="bg-blue-500 text-white p-4 rounded-lg shadow-lg relative opacity-75">
            <div class="flex justify-between items-center">
                <span class="px-2" x-text="alert.message"></span>
                <button @click="dismissAlert(alert.id)" class="text-white hover:text-gray-300">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="w-full h-1 bg-gray-500 opacity-20 mt-2">
                <div class="h-full bg-gray-800 opacity-80" :style="`width: ${alert.progress}%`"></div>
            </div>
        </div>
    </template>
</div>

<div x-data="fileUpload()" x-init="init()" class="max-w-5xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">File Upload & Conversion</h2>

    <div class="border border-dashed border-gray-300 p-6 text-center rounded-lg cursor-pointer"
        @click="$refs.fileInput.click()">
        <p class="text-gray-500">Click to upload files or drag & drop</p>
        <input type="file" x-ref="fileInput" accept="<?= implode(', ', $allowedMimeType) ?>" @change="handleFileSelect"
            multiple class="hidden" />
    </div>

    <template x-if="selectedFiles.length > 0">
        <div class="mt-4">
            <div class="hidden md:block">
                <!-- Table for larger screens -->
                <table class="w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-left">File Name</th>
                            <th class="p-3 text-left">Conversion</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Progress</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <tr class="border-b">
                                <!-- File Name -->
                                <td class="p-3" x-bind:title="file.name" x-text="truncate(file.name, 20)"></td>

                                <!-- File Conversions -->
                                <td class="p-3">
                                    <template x-if="file.allowedConversions.length > 0 && file.status === 'pending'">
                                        <div x-data="{ dropdownOpen: false }">
                                            <!-- Pass file into x-data -->
                                            <!-- Each row gets its own open state -->
                                            <!-- Toggle the dropdown open/close and reset conversion when opened -->
                                            <button @click="dropdownOpen = !dropdownOpen; file.selectedConversion = ''"
                                                class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                                <span x-text="file.selectedConversion || 'Select conversion'"></span>
                                            </button>

                                            <!-- Dropdown list -->
                                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                                class="absolute mt-2 bg-white shadow-lg border rounded-md z-50">
                                                <div class="p-2 max-h-64 overflow-y-auto">
                                                    <template x-for="group in file.allowedConversions"
                                                        :key="group.groupName">
                                                        <div>
                                                            <div class="font-semibold text-gray-800 p-1 bg-gray-200">
                                                                <span x-text="group.groupName"></span>
                                                            </div>
                                                            <div class="space-y-1 p-2">
                                                                <template x-for="extension in group.extensions"
                                                                    :key="extension">
                                                                    <button
                                                                        @click="file.selectedConversion = extension; dropdownOpen = false"
                                                                        class="w-full text-left p-2 hover:bg-gray-200">
                                                                        <span x-text="extension"></span>
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
                                            <span x-text="file.selectedConversion"></span>
                                        </div>
                                    </template>
                                </td>

                                <!-- File Status -->
                                <td class="p-3">
                                    <span class="font-medium px-2 py-1 rounded-md" :class="{
                                        'bg-yellow-100 text-yellow-600': file.status === 'pending',
                                        'bg-blue-100 text-blue-600': file.status === 'uploaded' || file.status === 'queued' || file.status === 'processing',
                                        'bg-green-100 text-green-600': file.status === 'complete',
                                        'bg-red-100 text-red-600': file.status === 'failed'
                                    }" x-text="file.status">
                                    </span>
                                </td>

                                <!-- File Progress -->
                                <td class="p-3">
                                    <template
                                        x-if="file.status !== 'complete' && file.status !== 'failed' && file.status !== 'pending'">
                                        <div class="relative w-full h-4 bg-gray-300 rounded overflow-hidden">
                                            <div class="absolute top-0 left-0 h-full transition-all duration-500 ease-in-out"
                                                :class="{
                                                'bg-blue-500 animate-pulse': file.progress > 0 && file.progress < 100,
                                                'bg-blue-500': file.progress === 100
                                            }" :style="'width: ' + file.progress + '%'">
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="file.status === 'pending'">
                                        <button @click="handleSingleFileConvert(file)"
                                            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition relative">
                                            <span x-show="!file.isConverting">Convert</span>
                                            <span x-show="file.isConverting"
                                                class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white animate-spin"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4" class="opacity-25"></circle>
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="4"
                                                        d="M4 12a8 8 0 0116 0a8 8 0 01-16 0z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </template>

                                    <template x-if="file.status === 'complete'">
                                        <a :href="'/file/download/' + file.id"
                                            class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition relative">Download</a>
                                    </template>
                                </td>

                                <td>
                                    <template
                                        x-if="file.status === 'complete' || file.status === 'failed' || file.status === 'pending'">
                                        <i @click="handleRemove(file)" class="fa-solid fa-xmark hover:opacity-50"></i>
                                    </template>

                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>

            <!-- Stacked layout for mobile -->
            <div class="md:hidden">
                <template x-for="(file, index) in selectedFiles" :key="index">
                    <div class="border-b p-4">
                        <div class="flex justify-between">
                            <span class="font-semibold">File Name:</span>
                            <span x-bind:title="file.name" x-text="truncate(file.name, 20)"></span>
                        </div>

                        <div class="flex justify-between mt-2">
                            <span class="font-semibold">Conversion:</span>
                            <template x-if="file.allowedConversions.length > 0 && file.status === 'pending'">
                                <div x-data="{ dropdownOpen: false }">
                                    <!-- Pass file into x-data -->
                                    <!-- Each row gets its own open state -->
                                    <!-- Toggle the dropdown open/close and reset conversion when opened -->
                                    <button @click="dropdownOpen = !dropdownOpen; file.selectedConversion = ''"
                                        class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                        <span x-text="file.selectedConversion || 'Select conversion'"></span>
                                    </button>

                                    <!-- Dropdown list -->
                                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                        class="absolute mt-2 bg-white shadow-lg border rounded-md z-50 right-10">
                                        <div class="p-2 max-h-64 overflow-y-auto">
                                            <template x-for="group in file.allowedConversions" :key="group.groupName">
                                                <div>
                                                    <div class="font-semibold text-gray-800 p-1 bg-gray-200">
                                                        <span x-text="group.groupName"></span>
                                                    </div>
                                                    <div class="space-y-1 p-2">
                                                        <template x-for="extension in group.extensions"
                                                            :key="extension">
                                                            <button
                                                                @click="file.selectedConversion = extension; dropdownOpen = false"
                                                                class="w-full text-left p-2 hover:bg-gray-200">
                                                                <span x-text="extension"></span>
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
                                <span x-text="file.selectedConversion"></span>
                            </template>
                        </div>

                        <div class="flex justify-between mt-2">
                            <span class="font-semibold">Status:</span>
                            <span class="font-medium px-2 py-1 rounded-md" :class="{
                                'bg-yellow-100 text-yellow-600': file.status === 'pending',
                                'bg-blue-100 text-blue-600': file.status === 'uploaded' ||file.status === 'queued' || file.status === 'processing',
                                'bg-green-100 text-green-600': file.status === 'complete',
                                'bg-red-100 text-red-600': file.status === 'failed'
                            }" x-text="file.status"></span>
                        </div>

                        <div class="mt-2">
                            <template
                                x-if="file.status !== 'complete' && file.status !== 'failed' && file.status !== 'pending'">
                                <div class="relative w-full h-4 bg-gray-300 rounded overflow-hidden">
                                    <div class="absolute top-0 left-0 h-full transition-all duration-500 ease-in-out"
                                        :class="{
                                                'bg-blue-500 animate-pulse': file.progress > 0 && file.progress < 100,
                                                'bg-blue-500': file.progress === 100
                                            }" :style="'width: ' + file.progress + '%'">
                                    </div>
                                </div>
                            </template>

                            <template x-if="file.status === 'pending'">
                                <button @click="handleSingleFileConvert(file)"
                                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition relative">
                                    <span x-show="!file.isConverting">Convert</span>
                                    <span x-show="file.isConverting"
                                        class="absolute inset-0 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                                class="opacity-25"></circle>
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="4" d="M4 12a8 8 0 0116 0a8 8 0 01-16 0z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </template>

                            <template x-if="file.status === 'complete'">
                                <a :href="'/file/download/' + file.id"
                                    class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition relative">Download</a>
                            </template>
                        </div>

                        <div class="mt-4">
                            <template
                                x-if="file.status === 'complete' || file.status === 'failed' || file.status === 'pending'">
                                <i @click="handleRemove(file)" class="fa-solid fa-xmark hover:opacity-50"></i>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <div class="flex justify-between mt-4">
        <!-- Convert Files button -->
        <button @click="handleBulkFileConvert"
            class="bg-blue-500 text-white px-6 py-2 rounded mt-4 hover:bg-blue-600 transition relative"
            x-show="selectedFiles.length > 0 && selectedFiles.some(file => file.id === null)">
            <span x-show="!isConverting">Convert Files</span>
            <span x-show="isConverting" class="absolute inset-0 flex items-center justify-center">
                <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                        d="M4 12a8 8 0 0116 0a8 8 0 01-16 0z"></path>
                </svg>
            </span>
        </button>

        <!-- Remove All button -->
        <button @click="removeFiles(selectedFiles)"
            class="bg-red-500 text-white px-6 py-2 rounded mt-4 hover:bg-red-600 transition ml-auto"
            x-show="selectedFiles.length > 0">
            Remove All
        </button>
    </div>

</div>


<script>
    function alertHandler() {
        return {
            alerts: [],
            alertId: 0,
            listenerAdded: false,
            init() {
                if (this.listenerAdded) {
                    document.addEventListener('alert', (event) => {
                        console.log('Alert received:', event.detail.message);
                        this.showAlert(event.detail.message, event.detail.type);
                    });
                    this.listenerAdded = true;

                } // Avoid adding listener multiple times
                document.addEventListener('alert', (event) => {
                    this.showAlert(event.detail.message, event.detail.type);
                });
                // Check for session alert
                const alertMessage =
                    <?= json_encode(session()->getFlashdata("alert")) ?>; // Adjust based on your framework's session flashdata
                if (alertMessage) {
                    this.showAlert(alertMessage.message, alertMessage.type);
                }
            },
            showAlert(message, type = 'info') {
                console.log('Showing alert:', message, type);
                const id = this.alertId++;
                this.alerts.push({
                    id,
                    message,
                    type: type,
                    progress: 0
                });

                let progress = 0;
                const interval = setInterval(() => {
                    progress += 1;
                    this.updateProgress(id, progress);
                    if (progress >= 100) {
                        clearInterval(interval);
                        setTimeout(() => {
                            this.dismissAlert(id);
                        }, 30); // Wait a little before dismissing
                    }
                }, 30); // Control speed of progress bar

            },
            dismissAlert(id) {
                this.alerts = this.alerts.filter(alert => alert.id !== id);
            },
            updateProgress(id, progress) {
                const alert = this.alerts.find(a => a.id === id);
                if (alert) {
                    alert.progress = progress;
                }
            },
            alertClasses(alert) {
                return {
                    'bg-green-500': alert.type === 'success',
                    'bg-red-500': alert.type === 'error',
                    'bg-yellow-500': alert.type === 'warning',
                    'bg-blue-500': alert.type === 'info',
                };
            }

        };
    }
</script>

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
                    console.log(error)
                    document.dispatchEvent(new CustomEvent('alert', {
                        detail: {
                            message: 'Error fetching allowed conversions',
                            type: 'error'
                        }
                    }));
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

                    document.dispatchEvent(new CustomEvent('alert', {
                        detail: {
                            message: 'Files removed successfully!',
                            type: 'success'
                        }
                    }));
                    return;
                }

                document.dispatchEvent(new CustomEvent('alert', {
                    detail: {
                        message: data.message,
                        type: 'error'
                    }
                }));
            },

            async uploadFile(fileObj) {
                if (!fileObj.selectedConversion || fileObj.id != null) return;
                const formData = new FormData();
                formData.append('file', fileObj.file);
                formData.append('convert_to', fileObj.selectedConversion);
                fileObj.isConverting = true;

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

                        console.log('File uploaded successfully:', data);

                        document.dispatchEvent(new CustomEvent('alert', {
                            detail: {
                                message: 'File uploaded successfully!',
                                type: 'success'
                            }
                        }));
                    } else {
                        const data = await response.json();

                        document.dispatchEvent(new CustomEvent('alert', {
                            detail: {
                                message: data.message,
                                type: 'error'
                            }
                        }));
                        fileObj.file = null
                        fileObj.status = 'failed';
                    }
                } catch (error) {
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
                        if (this.selectedFiles.every(file => file.status === 'completed' || file.status === 'error')) {
                            clearInterval(this.pollingInterval);
                            this.pollingInterval = null;
                        }
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