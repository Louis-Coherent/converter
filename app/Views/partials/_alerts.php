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