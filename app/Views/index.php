<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="alertHandler()" x-init="init()" class="fixed bottom-0 right-0 p-6 space-y-4 z-50">
    <!-- Alerts will go here -->
    <template x-for="alert in alerts" :key="alert.id">
        <div :class="alertClasses(alert)" class="bg-blue-500 text-white p-4 rounded-lg shadow-lg relative opacity-75">
            <div class="flex justify-between items-center">
                <span class="px-2" x-text="alert.message"></span>
                <button @click="dismissAlert(alert.id)" class="text-white hover:text-gray-300"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="w-full h-1 bg-blue-300 mt-2">
                <div class="h-full bg-blue-700" :style="`width: ${alert.progress}%`"></div>
            </div>
        </div>
    </template>
</div>

<div x-data="fileUpload()" x-init="init()" class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">File Upload & Conversion</h2>

    <div class="border border-dashed border-gray-300 p-6 text-center rounded-lg cursor-pointer"
        @click="$refs.fileInput.click()">
        <p class="text-gray-500">Click to upload files or drag & drop</p>
        <input type="file" x-ref="fileInput" @change="handleFileSelect" multiple class="hidden" />
    </div>

    <template x-if="selectedFiles.length > 0">
        <div class="mt-4">
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
                            <td class="p-3" x-text="file.name"></td>
                            <td class="p-3">
                                <template x-if="file.allowedConversions.length > 0">
                                    <select x-model="file.selectedConversion" class="w-full p-2 border rounded">
                                        <template x-for="conversion in file.allowedConversions">
                                            <option :value="conversion" x-text="conversion"></option>
                                        </template>
                                    </select>
                                </template>
                                <template x-if="file.allowedConversions.length === 0">
                                    <span class="text-red-500">Unsupported</span>
                                </template>
                            </td>


                            <td class="p-3">
                                <span class="font-medium px-2 py-1 rounded-md" :class="{
                                    'bg-yellow-100 text-yellow-600': file.status === 'pending',
                                    'bg-blue-100 text-blue-600': file.status === 'uploaded' ||file.status === 'queued' || file.status === 'processing',
                                    'bg-green-100 text-green-600': file.status === 'complete',
                                    'bg-red-100 text-red-600': file.status === 'failed'
                                }" x-text="file.status">
                                </span>
                                <div class="text-red-500 text-sm" x-show="file.errorMessage" x-text="file.errorMessage">
                                </div>
                            </td>

                            <td class="p-3">
                                <template x-if="file.status !== 'complete' && file.status !== 'failed'">
                                    <div class="relative w-full h-4 bg-gray-300 rounded overflow-hidden">
                                        <div class="absolute top-0 left-0 h-full transition-all duration-500 ease-in-out"
                                            :class="{
                                                'bg-blue-500 animate-pulse': file.progress > 0 && file.progress < 100,
                                                'bg-blue-500': file.progress === 100
                                            }" :style="'width: ' + file.progress + '%'">
                                        </div>
                                    </div>
                                </template>
                                <template x-if="file.status === 'complete'">
                                    <a :href="'/file/download/' + file.id" class="text-blue-500 underline">Download</a>
                                </template>

                            </td>
                            <td>
                                <template x-if="file.status === 'complete' || file.status === 'failed'">
                                    <i @click="handleRemove(file)" class="fa-solid fa-xmark hover:opacity-50"></i>
                                </template>
                            </td>


                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <button @click="handleFileConvert"
        class="bg-blue-500 text-white px-6 py-2 rounded mt-4 hover:bg-blue-600 transition"
        x-show="selectedFiles.length > 0">
        Convert Files
    </button>
</div>

<script>
function alertHandler() {
    return {
        alerts: [],
        alertId: 0,
        init() {
            // Check for session alert
            const alertMessage =
                '<?= json_encode(session()->getFlashdata("alert")) ?>'; // Adjust based on your framework's session flashdata
            if (alertMessage) {
                const session = JSON.parse(alertMessage);
                this.showAlert(session.message, session.type);
            }
        },
        showAlert(message, type = 'info') {
            const id = this.alertId++;
            this.alerts.push({
                id,
                message,
                type: type,
                progress: 0
            });

            console.log(this.alerts);

            // Start a progress bar animation
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
            }, 10); // Control speed of progress bar

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
                'bg-green-500': alert.type = ('success'),
                'bg-red-500': alert.type = ('error'),
                'bg-yellow-500': alert.type = ('warning'),
                'bg-blue-500': alert.type = ('info'),
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
        async handleFileSelect(event) {
            this.selectedFiles = []; // Clear existing files first

            this.selectedFiles = Array.from(event.target.files).map(file => ({
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
            await this.updateAllowedConversions();
        },
        async updateAllowedConversions() {
            const mimeTypes = [...new Set(this.selectedFiles.map(f => f.mimeType))];
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
                        file.allowedConversions = data[file.mimeType] || [];
                        if (file.allowedConversions.length > 0) {
                            file.selectedConversion = file.allowedConversions[
                                0]; // Set default conversion type
                        }
                    });
                }
            } catch (error) {
                console.error('Error fetching allowed conversions:', error);
            }
        },
        async handleFileConvert() {
            const uploadPromises = this.selectedFiles.map(fileObj => this.uploadFile(fileObj));
            await Promise.all(uploadPromises);
            this.pollStatuses();
        },
        async handleRemove(file) {
            console.log(file)
        },
        async uploadFile(fileObj) {
            if (!fileObj.selectedConversion || fileObj.id != null) return;
            const formData = new FormData();
            formData.append('file', fileObj.file);
            formData.append('convert_to', fileObj.selectedConversion);
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
                    fileObj.isConverting = true;
                } else {
                    fileObj.errorMessage = 'Upload failed';
                    fileObj.file = null
                    fileObj.status = 'error';
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
            console.log(this.selectedFiles)
        }
    };
}
</script>

<?= $this->endSection('content') ?>