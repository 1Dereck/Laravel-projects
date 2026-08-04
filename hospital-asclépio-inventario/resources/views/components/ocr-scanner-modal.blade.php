<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<div x-data="ocrScannerModal()" x-on:open-ocr-scanner.window="triggerCamera($event.detail)"
    x-on:keydown.escape.window="closeOcr()">

    <!-- Input de câmera nativa direto -->
    <input x-ref="directFileInput" type="file" accept="image/*" capture="environment" @change="processOcrFile($event)" class="hidden">

    <div x-show="isOpen"
        class="fixed inset-0 z-100 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto"
        style="display: none;" x-cloak>

        <div @click.outside="if(!isLoading) closeOcr()"
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col my-auto transition-all">

            <!-- Header -->
            <div
                class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80 shrink-0">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold border border-teal-500/20 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 truncate">Leitor por Foto (OCR)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Reconhecimento óptico exclusivo de
                            números</p>
                    </div>
                </div>
                <button type="button" @click="closeOcr()" :disabled="isLoading"
                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition cursor-pointer shrink-0 disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 flex flex-col items-center">

                <!-- Estado Inicial: Selecionar / Tirar Foto -->
                <template x-if="step === 'select'">
                    <div class="w-full space-y-4 text-center">
                        <!-- Botão de Upload / Câmera Nativa -->
                        <button type="button"
                            @click="$refs.directFileInput ? $refs.directFileInput.click() : null"
                            class="w-full py-4 px-5 rounded-2xl bg-teal-500 hover:bg-teal-600 text-slate-950 font-extrabold text-sm flex items-center justify-center gap-3 cursor-pointer shadow-lg shadow-teal-500/20 active:scale-95 transition-all">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="text-left">
                                <span class="block leading-tight text-sm font-bold">📷 Tirar Foto da Etiqueta</span>
                                <span class="block text-[11px] font-normal text-slate-900/80">Leitura Exclusiva de Números
                                    por Foto</span>
                            </div>
                        </button>

                        <div
                            class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                            Tire uma foto focada do número de série gravado ou impresso na etiqueta. O leitor óptico
                            filtrará apenas os dígitos numéricos (sem letras).
                        </div>
                    </div>
                </template>

                <!-- Estado de Recorte Manual & Processamento OCR -->
                <template x-if="step === 'crop'">
                    <div class="w-full space-y-3">
                        <p
                            class="text-xs font-semibold text-slate-600 dark:text-slate-300 text-center flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                            </svg>
                            Ajuste o retângulo sobre o número e clique em "Ler Número Selecionado"
                        </p>

                        <!-- Container do Cropper -->
                        <div
                            class="relative w-full max-h-350px min-h-220px overflow-hidden rounded-2xl bg-slate-950 flex items-center justify-center border border-slate-200 dark:border-slate-800">
                            <img x-ref="cropImage" :src="imageUrl" @load="initCropper()" class="max-w-full block"
                                alt="Foto para recorte">

                            <!-- Overlay Spinner durante processamento OCR -->
                            <div x-show="isLoading"
                                class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center gap-3 z-30 transition-all">
                                <div
                                    class="w-10 h-10 border-4 border-teal-500/20 border-t-teal-400 rounded-full animate-spin">
                                </div>
                                <div class="text-center space-y-1 px-4">
                                    <p class="text-xs font-bold text-white"
                                        x-text="ocrStatusText || 'Reconhecendo números...'"></p>
                                    <template x-if="ocrProgress > 0">
                                        <div class="w-40 bg-slate-800/80 rounded-full h-1.5 overflow-hidden mx-auto">
                                            <div class="bg-teal-400 h-1.5 transition-all duration-200"
                                                :style="'width: ' + ocrProgress + '%'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação na tela de Crop -->
                        <div class="flex items-center justify-between gap-2 pt-1">
                            <label
                                class="text-xs text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 font-semibold cursor-pointer flex items-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Trocar foto
                                <input type="file" accept="image/*" capture="environment"
                                    @change="processOcrFile($event)" class="hidden">
                            </label>

                            <button type="button" @click="runOcrOnCrop()" :disabled="isLoading"
                                class="py-2.5 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-slate-950 font-extrabold text-xs flex items-center gap-2 shadow-md shadow-teal-500/20 transition cursor-pointer disabled:opacity-50">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Ler Número Selecionado</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Estado de Confirmação do Número Lido -->
                <template x-if="step === 'confirm'">
                    <div class="w-full space-y-4 text-center py-2">
                        <div class="p-4 rounded-2xl bg-teal-500/10 border border-teal-500/30 space-y-2">
                            <span class="text-xs font-semibold text-teal-600 dark:text-teal-400 block uppercase tracking-wider">Número Identificado</span>
                            <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-wide font-mono select-all" x-text="extractedText"></div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">É este o número que deseja utilizar?</p>

                        <div class="flex flex-col sm:flex-row gap-2 pt-2">
                            <button type="button" @click="goBackToCrop()"
                                class="w-full py-3 px-4 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                </svg>
                                <span>Ajustar Seleção</span>
                            </button>
                            <button type="button" @click="confirmNumber()"
                                class="w-full py-3 px-4 rounded-xl bg-teal-500 hover:bg-teal-600 active:scale-95 text-slate-950 font-extrabold text-xs transition cursor-pointer flex items-center justify-center gap-2 shadow-md shadow-teal-500/20">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Usar este Número</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Mensagem de Erro se houver -->
                <template x-if="ocrErrorMessage">
                    <div
                        class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-medium text-center w-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span x-text="ocrErrorMessage"></span>
                    </div>
                </template>
            </div>

            <!-- Rodapé -->
            <div
                class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 flex justify-end bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
                <button type="button" @click="closeOcr()" :disabled="isLoading"
                    class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition cursor-pointer disabled:opacity-50">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        function initOcrComponent() {
            if (typeof Alpine !== 'undefined' && !Alpine.data('ocrScannerModal')) {
                Alpine.data('ocrScannerModal', () => ({
                    isOpen: false,
                    targetInput: null,
                    isLoading: false,
                    ocrProgress: 0,
                    ocrStatusText: '',
                    extractedText: '',
                    ocrErrorMessage: '',
                    step: 'select',
                    imageUrl: null,
                    cropper: null,
                    pendingCrop: false,

                    triggerCamera(detail) {
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                        this.targetInput = detail ? detail.target : null;
                        this.resetState();
                        if (this.$refs.directFileInput) {
                            this.$refs.directFileInput.click();
                        }
                    },

                    resetState() {
                        this.destroyCropper();
                        this.isLoading = false;
                        this.ocrProgress = 0;
                        this.ocrStatusText = '';
                        this.extractedText = '';
                        this.ocrErrorMessage = '';
                        this.step = 'select';
                        this.imageUrl = null;
                        this.pendingCrop = false;
                    },

                    destroyCropper() {
                        if (this.cropper) {
                            try {
                                this.cropper.destroy();
                            } catch (e) {}
                            this.cropper = null;
                        }
                    },

                    goBackToCrop() {
                        this.destroyCropper();
                        this.step = 'crop';
                        this.ocrErrorMessage = '';
                        this.$nextTick(() => {
                            this.initCropper();
                        });
                    },

                    confirmNumber() {
                        if (this.extractedText) {
                            this.onSuccess(this.extractedText);
                        }
                    },

                    processOcrFile(event) {
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                        const files = event.target.files;
                        if (!files || files.length === 0) return;
                        const file = files[0];

                        if (typeof Tesseract === 'undefined') {
                            this.ocrErrorMessage =
                                'O leitor por foto não foi carregado. Verifique sua conexão com a internet.';
                            return;
                        }

                        if (typeof Cropper === 'undefined') {
                            this.ocrErrorMessage =
                                'O leitor de foto precisa da biblioteca de recorte (Cropper.js). Verifique a conexão.';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.destroyCropper();
                            this.imageUrl = e.target.result;
                            this.step = 'crop';
                            this.ocrErrorMessage = '';
                            this.isOpen = true;
                            this.$nextTick(() => {
                                this.initCropper();
                            });
                        };
                        reader.readAsDataURL(file);
                        event.target.value = '';
                    },

                    initCropper() {
                        const imageEl = this.$refs.cropImage;
                        if (!imageEl) return;

                        const startCropper = () => {
                            if (this.cropper) return;
                            try {
                                this.cropper = new Cropper(imageEl, {
                                    viewMode: 1,
                                    dragMode: 'crop',
                                    autoCropArea: 0.6,
                                    restore: false,
                                    guides: true,
                                    center: true,
                                    highlight: true,
                                    cropBoxMovable: true,
                                    cropBoxResizable: true,
                                    toggleDragModeOnDblclick: false
                                });
                            } catch (err) {
                                console.error("Erro ao inicializar Cropper:", err);
                            }
                        };

                        if (imageEl.complete && imageEl.naturalWidth > 0) {
                            startCropper();
                        } else {
                            imageEl.onload = () => startCropper();
                            setTimeout(() => startCropper(), 150);
                        }
                    },

                    async runOcrOnCrop() {
                        if (!this.cropper) {
                            this.initCropper();
                            if (!this.cropper) return;
                        }

                        if (this.isLoading) return;

                        this.isLoading = true;
                        this.ocrErrorMessage = '';
                        this.ocrProgress = 0;
                        this.ocrStatusText = 'Isolando área selecionada...';

                        try {
                            const croppedCanvas = this.cropper.getCroppedCanvas({
                                maxWidth: 1024,
                                maxHeight: 1024,
                            });

                            if (!croppedCanvas) {
                                throw new Error('Não foi possível gerar a área recortada.');
                            }

                            const result = await Tesseract.recognize(
                                croppedCanvas,
                                'eng', {
                                    tessedit_char_whitelist: '0123456789',
                                    logger: m => {
                                        if (m.status === 'recognizing text') {
                                            this.ocrProgress = Math.round((m.progress ||
                                                0) * 100);
                                            this.ocrStatusText =
                                                `Reconhecendo apenas números... (${this.ocrProgress}%)`;
                                        } else if (m.status) {
                                            this.ocrStatusText = m.status;
                                        }
                                    }
                                }
                            );

                            const rawText = result?.data?.text || '';
                            const cleanText = this.sanitizeOcrText(rawText);

                            if (cleanText) {
                                this.extractedText = cleanText;
                                this.playBeep();
                                this.step = 'confirm';
                            } else {
                                throw new Error(
                                    'Nenhuma sequência numérica foi localizada no recorte. Ajuste a seleção e tente novamente.'
                                );
                            }
                        } catch (err) {
                            console.error("Erro no processamento OCR:", err);
                            this.ocrErrorMessage = err.message ||
                                "Não foi possível ler números nesta área. Ajuste o recorte para enquadrar melhor o número.";
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    // Filtro numérico exclusivo: descarta todas as letras e símbolos, retornando apenas dígitos
                    sanitizeOcrText(rawText) {
                        if (!rawText) return '';

                        const digitsOnly = rawText.replace(/[^0-9]/g, ' ').trim();
                        const numbers = digitsOnly.split(/\s+/).filter(n => n.length >= 1);

                        if (numbers.length > 0) {
                            return numbers.reduce((max, curr) => curr.length > max.length ? curr : max,
                                '');
                        }

                        return '';
                    },

                    onSuccess(cleanText) {
                        if (this.targetInput) {
                            let el = null;
                            if (typeof this.targetInput === 'string') {
                                el = document.getElementById(this.targetInput);
                            } else if (this.targetInput instanceof HTMLElement) {
                                el = this.targetInput;
                            }

                            if (el) {
                                el.value = cleanText;
                                el.dispatchEvent(new Event('input', {
                                    bubbles: true
                                }));
                                el.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                                el.blur();
                            }
                        }
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                        this.closeOcr();
                    },

                    closeOcr() {
                        if (this.isLoading) return;
                        this.resetState();
                        this.isOpen = false;
                    },

                    playBeep() {
                        try {
                            const ctx = new(window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = "sine";
                            osc.frequency.value = 1400;
                            gain.gain.value = 0.15;
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.12);
                        } catch (e) {}
                    }
                }));
            }
        }

        if (typeof Alpine !== 'undefined') {
            initOcrComponent();
        }
        document.addEventListener('alpine:init', initOcrComponent);
        document.addEventListener('livewire:init', initOcrComponent);
    })();

    window.lerTextoOCR = function(target) {
        if (document.activeElement && typeof document.activeElement.blur === 'function') {
            document.activeElement.blur();
        }
        window.dispatchEvent(new CustomEvent('open-ocr-scanner', {
            detail: {
                target: target
            }
        }));
    };
</script>
