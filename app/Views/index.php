<?php



?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="nrb8qhwo5gcr71w">
</script>


<div x-data="fileUpload()" class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg border rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">File Upload & Conversion</h2>
    <?php if (!empty($from) && !empty($to)): ?>
    <h2 class="text-lg font-light mt-1 opacity-90 my-2">Convert <?= strtoupper($from) ?> to <?= strtoupper($to) ?></h2>
    <?php endif; ?>

    <div data-umami-event="{Upload Form}" class=" border border-dashed border-gray-300 p-6 text-center rounded-lg
        cursor-pointer" @click="$refs.fileInput.click()">
        <?php if (!empty($from) && !empty($to)): ?>
        <p class="text-gray-500">Click to upload <?= strtoupper($from) ?> files or drag & drop</p>
        <?php else: ?>
        <p class="text-gray-500">Click to upload files or drag & drop</p>
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
        <div class="mt-8">
            <div class="hidden md:block">
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
                                <td class="p-3" x-bind:title="file.name" x-text="truncate(file.name, 20)"></td>
                                <td class="p-3">
                                    <template x-if="file.allowedConversions.length > 0 && file.status === 'pending'">
                                        <div x-data="{ dropdownOpen: false }">
                                            <button @click="dropdownOpen = !dropdownOpen; file.selectedConversion = ''"
                                                class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                                <span x-text="file.selectedConversion || 'Select conversion'"></span>
                                            </button>
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
                                <td class="p-3">
                                    <span class="font-medium px-2 py-1 rounded-md" :class="{
                                        'bg-yellow-100 text-yellow-600': file.status === 'pending',
                                        'bg-blue-100 text-blue-600': file.status === 'uploaded' || file.status === 'queued' || file.status === 'processing',
                                        'bg-green-100 text-green-600': file.status === 'complete',
                                        'bg-red-100 text-red-600': file.status === 'failed'
                                    }" x-text="file.status">
                                    </span>
                                </td>
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
                                        <button data-umami-event="{Single Convert}"
                                            @click="handleSingleFileConvert(file)"
                                            class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition relative w-32 h-10">
                                            <span x-show="!file.isConverting">Convert</span>
                                            <span x-show="file.isConverting"
                                                class="absolute inset-0 flex items-center justify-center">
                                                <div role="status">
                                                    <svg aria-hidden="true"
                                                        class="w-6 h-6 text-gray-200 animate-spin dark:text-white-600 fill-blue-500"
                                                        viewBox="0 0 100 101" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
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



                                    <template x-if="file.status === 'complete'">
                                        <a data-umami-event="{Single Download}" :href="'/file/download/' + file.id"
                                            class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition relative">Download</a>
                                    </template>
                                </td>

                                <td>
                                    <template
                                        x-if="file.status === 'complete' || file.status === 'failed' || file.status === 'pending'">
                                        <i data-umami-event="{Single Remove}" @click="handleRemove(file)"
                                            class="fa-solid fa-xmark hover:opacity-50"></i>
                                    </template>

                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>

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
                                    <button @click="dropdownOpen = !dropdownOpen; file.selectedConversion = ''"
                                        class="w-full p-2 border rounded bg-gray-100 text-gray-700 text-left">
                                        <span x-text="file.selectedConversion || 'Select conversion'"></span>
                                    </button>
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
                                <button data-umami-event="{Single Convert}" @click="handleSingleFileConvert(file)"
                                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 transition relative w-32 h-10">
                                    <span x-show="!file.isConverting">Convert</span>
                                    <span x-show="file.isConverting"
                                        class="absolute inset-0 flex items-center justify-center">
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

                            <template x-if="file.status === 'complete'">
                                <a data-umami-event="{Single Download}" :href="'/file/download/' + file.id"
                                    class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition relative">Download</a>
                            </template>
                        </div>

                        <div class="mt-4">
                            <template
                                x-if="file.status === 'complete' || file.status === 'failed' || file.status === 'pending'">
                                <i data-umami-event="{Single Remove}" @click="handleRemove(file)"
                                    class="fa-solid fa-xmark hover:opacity-50"></i>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <div class="flex justify-between mt-4">
        <!-- Convert Files button -->
        <button @click="handleBulkFileConvert" data-umami-event="{Bulk Convert}"
            class="bg-blue-500 text-white px-6 py-2 rounded mt-4 me-4 hover:bg-blue-600 transition relative"
            x-show="selectedFiles.length > 0 && selectedFiles.some(file => file.id === null && file.file != null)">
            <span x-show="!isConverting">Convert Files</span>
            <span x-show="isConverting" class="absolute inset-0 flex items-center justify-center">
                <div role="status">
                    <svg aria-hidden="true" class="w-6 h-6 text-gray-200 animate-spin dark:text-white-600 fill-blue-500"
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
        <a href="/file/download-zip" data-umami-event="{Bulk Download}"
            x-show="selectedFiles.length > 0 && selectedFiles.some(file => file.id !== null && file.status === 'complete')"
            class="bg-blue-500 text-white px-6 py-2 rounded mt-4 hover:bg-blue-600 transition relative"
            x-show="selectedFiles.length > 0 && selectedFiles.some(file => file.id != null)">
            <span>Download All</span>
        </a>

        <!-- Remove All button -->
        <button @click="removeFiles(selectedFiles)" data-umami-event="{Bulk Remove}"
            class="bg-red-500 text-white px-6 py-2 rounded mt-4 hover:bg-red-600 transition ml-auto"
            x-show="selectedFiles.length > 0">
            Remove All
        </button>
    </div>

</div>

<section class="py-12 my-12 bg-gray-100">
    <div class="max-w-5xl mx-auto px-10">
        <h2 class="text-xl font-semibold mb-4">How It Works</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-4 border rounded-lg shadow-md bg-gray-50">
                <h4 class="text-lg font-medium">1. Upload Your File</h4>
                <p class="text-gray-600 mt-2">Drag & drop or click to upload your file from your device. <a
                        href="supported-files" class="text-blue-600 hover:text-blue-800">Supported files</a></p>
            </div>
            <div class="p-4 border rounded-lg shadow-md bg-gray-50">
                <h4 class="text-lg font-medium">2. Choose Conversion Format</h4>
                <p class="text-gray-600 mt-2">Select the desired format for conversion from the available options.</p>
            </div>
            <div class="p-4 border rounded-lg shadow-md bg-gray-50">
                <h4 class="text-lg font-medium">3. Download Your File</h4>
                <p class="text-gray-600 mt-2">Click the download button once the conversion is complete.</p>
            </div>
        </div>
    </div>

</section>

<div class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg border rounded-lg">
    <h2 class="text-2xl font-semibold mb-4">Why Choose Our Converter?</h2>
    <ul class="list-disc list-inside text-gray-700 space-y-2">
        <li><strong>Fast & Efficient:</strong> Our advanced conversion engine ensures quick processing times for all
            file types.</li>
        <li><strong>High-Quality Output:</strong> Maintain the best quality possible when converting documents, images,
            audio, and more.</li>
        <li><strong>Secure & Private:</strong> Your uploaded files are encrypted and automatically deleted after
            conversion for your privacy.</li>
        <li><strong>Wide Format Support:</strong> Convert between multiple file formats, including images, documents,
            audio, and archives.</li>
        <li><strong>User-Friendly Interface:</strong> Our simple and intuitive design makes file conversion hassle-free,
            even for beginners.</li>
    </ul>
</div>


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
                    console.log(files)
                    // Download the file as a Blob
                    const response = await fetch(files[0].link, {
                        method: 'GET',
                        mode: 'cors' // Ensure CORS mode is enabled
                    });

                    // Log all response headers
                    for (let [key, value] of response.headers.entries()) {
                        console.log(`${key}: ${value}`);
                    }

                    // Try getting Content-Type
                    const contentType = response.headers.get('Content-Type');
                    console.log("Detected MIME Type:", contentType);
                    const blob = await response.blob();

                    // Create a File object from the Blob
                    const file = new File([blob], files[0].name, {
                        type: blob.type
                    });

                    console.log(file)

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
                mimeType: null,
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