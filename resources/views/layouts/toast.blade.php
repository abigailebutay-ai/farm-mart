{{-- Toast Notification Component --}}
<div x-data="toastManager()" x-cloak>
    <div class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-full"
                :class="getToastClasses(toast.type)"
                class="pointer-events-auto rounded-lg shadow-lg p-4 max-w-sm"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span x-text="getToastIcon(toast.type)" class="text-xl"></span>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-medium" :class="getToastTextClasses(toast.type)" x-text="toast.message"></p>
                    </div>
                    <button
                        @click="removeToast(toast.id)"
                        type="button"
                        class="ml-3 inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            nextId: 1,

            show(message, type = 'info', duration = 5000) {
                const id = this.nextId++;
                const toast = {
                    id,
                    message,
                    type,
                    visible: true,
                };

                this.toasts.push(toast);

                if (duration > 0) {
                    setTimeout(() => {
                        this.removeToast(id);
                    }, duration);
                }

                return id;
            },

            success(message, duration = 5000) {
                return this.show(message, 'success', duration);
            },

            error(message, duration = 5000) {
                return this.show(message, 'error', duration);
            },

            info(message, duration = 5000) {
                return this.show(message, 'info', duration);
            },

            warning(message, duration = 5000) {
                return this.show(message, 'warning', duration);
            },

            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            },

            getToastClasses(type) {
                const baseClasses = 'border';
                const types = {
                    success: 'bg-green-50 dark:bg-green-900 border-green-200 dark:border-green-700',
                    error: 'bg-red-50 dark:bg-red-900 border-red-200 dark:border-red-700',
                    info: 'bg-blue-50 dark:bg-blue-900 border-blue-200 dark:border-blue-700',
                    warning: 'bg-yellow-50 dark:bg-yellow-900 border-yellow-200 dark:border-yellow-700',
                };
                return `${baseClasses} ${types[type] || types.info}`;
            },

            getToastTextClasses(type) {
                const types = {
                    success: 'text-green-800 dark:text-green-200',
                    error: 'text-red-800 dark:text-red-200',
                    info: 'text-blue-800 dark:text-blue-200',
                    warning: 'text-yellow-800 dark:text-yellow-200',
                };
                return types[type] || types.info;
            },

            getToastIcon(type) {
                const icons = {
                    success: '✓',
                    error: '✕',
                    info: 'ℹ',
                    warning: '⚠',
                };
                return icons[type] || icons.info;
            },
        };
    }

    // Make toast globally available
    window.toast = {
        show(message, type = 'info', duration = 5000) {
            const event = new CustomEvent('toast:show', {
                detail: { message, type, duration }
            });
            document.dispatchEvent(event);
        },
        success(message, duration = 5000) {
            this.show(message, 'success', duration);
        },
        error(message, duration = 5000) {
            this.show(message, 'error', duration);
        },
        info(message, duration = 5000) {
            this.show(message, 'info', duration);
        },
        warning(message, duration = 5000) {
            this.show(message, 'warning', duration);
        }
    };
</script>
