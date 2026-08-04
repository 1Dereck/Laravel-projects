<style>
    #barcode-reader-view video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 1rem;
    }
    #barcode-reader-view {
        border: none !important;
    }
    #barcode-reader-view__scan_region {
        background: transparent !important;
    }
</style>

<div x-data="barcodeScannerModal()"
     x-show="isOpen"
     x-on:open-barcode-scanner.window="openScanner($event.detail)"
     x-on:keydown.escape.window="closeScanner()"
     class="fixed inset-0 z-100 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-md overflow-y-auto"
     style="display: none;"
     x-cloak>

    <!-- Input de câmera nativa direto -->
    <input x-ref="directBarcodeFileInput" type="file" accept="image/*" capture="environment" @change="scanFromFile($event)" class="hidden">

    <div @click.outside="closeScanner()"
         class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col my-auto transition-all max-h-[90vh]">

        <!-- Header do Modal -->
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-500/20 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 truncate">Leitor de Código de Barras</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Suporte a Alumínio, Metal e Papel</p>
                </div>
            </div>
            <button type="button" @click="closeScanner()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl transition cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Seletor de Câmeras (Se houver vídeo ao vivo e múltiplas câmeras) -->
        <template x-if="liveStreamActive && availableCameras.length > 1">
            <div class="px-5 py-2.5 bg-slate-100/70 dark:bg-slate-950/60 border-b border-slate-200/60 dark:border-slate-800 flex items-center justify-between gap-3 text-xs shrink-0">
                <span class="font-bold text-slate-600 dark:text-slate-400 shrink-0">Selecionar Câmera:</span>
                <select x-model="selectedCameraId" @change="switchCamera()" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <template x-for="(cam, idx) in availableCameras" :key="cam.id">
                        <option :value="cam.id" x-text="cam.label || ('Câmera ' + (idx + 1))"></option>
                    </template>
                </select>
            </div>
        </template>

        <!-- Corpo do Modal -->
        <div class="p-5 space-y-4 overflow-y-auto flex-1">

            <!-- Vídeo Ao Vivo (quando disponível) -->
            <div x-show="liveStreamActive" class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-inner flex items-center justify-center">
                <div id="barcode-reader-view" class="w-full h-full"></div>
            </div>

            <!-- Botão Principal Destaque Verde (Tirar Foto da Câmera) -->
            <button type="button"
                @click="$refs.directBarcodeFileInput ? $refs.directBarcodeFileInput.click() : null"
                class="w-full py-4 px-5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold text-sm flex items-center justify-center gap-3 cursor-pointer shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="text-left">
                    <span class="block leading-tight text-sm font-bold">📷 Tirar Foto do Código de Barras</span>
                    <span class="block text-[11px] font-normal text-slate-900/80">Lê etiquetas normais, metálicas, alumínio e gravadas a laser</span>
                </div>
            </button>

            <!-- Mensagem de Alerta (caso a foto tirada não seja legível) -->
            <template x-if="photoErrorMessage">
                <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-medium text-center">
                    <span x-text="photoErrorMessage"></span>
                </div>
            </template>

            <div class="text-center text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                <strong>Filtros Ativos:</strong> Anti-reflexo de alumínio e inversão automática para placas escuras/laser.
            </div>

        </div>

        <!-- Rodapé do Modal -->
        <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 flex justify-end bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
            <button type="button" @click="closeScanner()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition cursor-pointer">
                Cancelar
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        function initBarcodeComponent() {
            if (!window.barcodeComponentRegistered) {
                window.barcodeComponentRegistered = true;
                Alpine.data('barcodeScannerModal', () => ({
            isOpen: false,
            targetInput: null,
            html5QrCode: null,
            liveStreamActive: false,
            photoErrorMessage: '',
            isScanning: false,
            availableCameras: [],
            selectedCameraId: '',

            getBarcodeFormats() {
                if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
                    return [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.CODE_93,
                        Html5QrcodeSupportedFormats.CODABAR,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E,
                        Html5QrcodeSupportedFormats.UPC_EAN_EXTENSION,
                        Html5QrcodeSupportedFormats.ITF
                    ];
                }
                return undefined;
            },

            openScanner(detail) {
                if (document.activeElement && typeof document.activeElement.blur === 'function') {
                    document.activeElement.blur();
                }
                this.targetInput = detail.target;
                this.photoErrorMessage = '';
                this.liveStreamActive = false;
                this.availableCameras = [];
                this.selectedCameraId = '';
                this.isOpen = false;
                if (this.$refs.directBarcodeFileInput) {
                    this.$refs.directBarcodeFileInput.click();
                }
            },

            async fetchCameras() {
                try {
                    const cameras = await Html5Qrcode.getCameras();
                    if (cameras && cameras.length > 0) {
                        this.availableCameras = cameras;
                        if (!this.selectedCameraId) {
                            const backCamera = cameras.find(c => /back|rear|traseira|environment/i.test(c.label));
                            if (backCamera) {
                                this.selectedCameraId = backCamera.id;
                            } else if (cameras.length > 1) {
                                this.selectedCameraId = cameras[cameras.length - 1].id;
                            } else {
                                this.selectedCameraId = cameras[0].id;
                            }
                        }
                    }
                } catch (e) {
                    console.warn("Aviso ao enumerar câmeras:", e);
                }
            },

            async switchCamera() {
                if (this.selectedCameraId) {
                    await this.startCameraWithConfig(this.selectedCameraId);
                }
            },

            async tryStartLiveCamera() {
                if (typeof Html5Qrcode === 'undefined') return;

                await this.fetchCameras();

                let primaryConfig = { facingMode: "environment" };
                if (this.selectedCameraId) {
                    primaryConfig = this.selectedCameraId;
                }

                await this.startCameraWithConfig(primaryConfig);
            },

            async startCameraWithConfig(camConfig) {
                try {
                    if (this.html5QrCode) {
                        await this.stopCamera();
                    }

                    const formats = this.getBarcodeFormats();
                    const options = {
                        formatsToSupport: formats,
                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true
                        }
                    };

                    this.html5QrCode = new Html5Qrcode("barcode-reader-view", options);
                    const config = {
                        fps: 10,
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                            const width = Math.max(Math.floor(minEdge * 0.85), 220);
                            const height = Math.max(Math.floor(minEdge * 0.45), 110);
                            return { width, height };
                        },
                        aspectRatio: 1.777778
                    };

                    this.isScanning = true;

                    const onScanSuccess = (decodedText) => {
                        if (!this.isScanning) return;
                        this.isScanning = false;
                        this.playBeep();
                        this.onSuccess(decodedText);
                    };

                    const onScanError = () => {};

                    try {
                        await this.html5QrCode.start(camConfig, config, onScanSuccess, onScanError);
                        this.liveStreamActive = true;
                    } catch (firstErr) {
                        try {
                            await this.html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError);
                            this.liveStreamActive = true;
                        } catch (secErr) {
                            try {
                                await this.html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanError);
                                this.liveStreamActive = true;
                            } catch (thirdErr) {
                                this.liveStreamActive = false;
                            }
                        }
                    }
                } catch (err) {
                    this.liveStreamActive = false;
                    this.isScanning = false;
                }
            },

            // Gera 3 variantes da imagem:
            // 1. Grayscale + Alto Contraste (para alumínio/prata refletivo)
            // 2. Invertido (para placas pretas com gravação a laser clara)
            // 3. Binarizado P&B Adaptativo (remove glare e iluminação irregular)
            async processImageVariants(file) {
                return new Promise((resolve) => {
                    const img = new Image();
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        img.onload = () => {
                            const MAX_WIDTH = 1200;
                            let width = img.width;
                            let height = img.height;

                            if (width > MAX_WIDTH) {
                                height = Math.round((height * MAX_WIDTH) / width);
                                width = MAX_WIDTH;
                            }

                            // Canvas 1: Grayscale + Alto Contraste (Alumínio)
                            const canvas1 = document.createElement('canvas');
                            canvas1.width = width;
                            canvas1.height = height;
                            const ctx1 = canvas1.getContext('2d');
                            ctx1.drawImage(img, 0, 0, width, height);

                            const imgData1 = ctx1.getImageData(0, 0, width, height);
                            const d1 = imgData1.data;
                            let sum = 0;
                            for (let i = 0; i < d1.length; i += 4) {
                                let gray = 0.299 * d1[i] + 0.587 * d1[i + 1] + 0.114 * d1[i + 2];
                                // Aumento de contraste
                                gray = gray < 128 ? Math.max(0, gray - 25) : Math.min(255, gray + 25);
                                d1[i] = gray;
                                d1[i + 1] = gray;
                                d1[i + 2] = gray;
                                sum += gray;
                            }
                            ctx1.putImageData(imgData1, 0, 0);

                            // Canvas 2: Inversão de Cores (Placas Pretas com gravação laser prateada/branca)
                            const canvas2 = document.createElement('canvas');
                            canvas2.width = width;
                            canvas2.height = height;
                            const ctx2 = canvas2.getContext('2d');
                            ctx2.drawImage(canvas1, 0, 0);
                            const imgData2 = ctx2.getImageData(0, 0, width, height);
                            const d2 = imgData2.data;
                            for (let i = 0; i < d2.length; i += 4) {
                                d2[i] = 255 - d2[i];
                                d2[i + 1] = 255 - d2[i + 1];
                                d2[i + 2] = 255 - d2[i + 2];
                            }
                            ctx2.putImageData(imgData2, 0, 0);

                            // Canvas 3: Binarizado P&B Limpo (Threshold para reflexo/glare de alumínio)
                            const canvas3 = document.createElement('canvas');
                            canvas3.width = width;
                            canvas3.height = height;
                            const ctx3 = canvas3.getContext('2d');
                            const imgData3 = ctx1.getImageData(0, 0, width, height);
                            const d3 = imgData3.data;
                            const avgThreshold = sum / (d3.length / 4);
                            for (let i = 0; i < d3.length; i += 4) {
                                const bw = d3[i] > avgThreshold ? 255 : 0;
                                d3[i] = bw;
                                d3[i + 1] = bw;
                                d3[i + 2] = bw;
                            }
                            ctx3.putImageData(imgData3, 0, 0);

                            Promise.all([
                                new Promise(res => canvas1.toBlob(b => res(new File([b], "contrast.jpg", { type: "image/jpeg" })), "image/jpeg", 0.9)),
                                new Promise(res => canvas2.toBlob(b => res(new File([b], "inverted.jpg", { type: "image/jpeg" })), "image/jpeg", 0.9)),
                                new Promise(res => canvas3.toBlob(b => res(new File([b], "binarized.jpg", { type: "image/jpeg" })), "image/jpeg", 0.9))
                            ]).then(([f1, f2, f3]) => {
                                resolve([f1, f2, f3]);
                            });
                        };
                        img.onerror = () => resolve([]);
                        img.src = e.target.result;
                    };
                    reader.onerror = () => resolve([]);
                    reader.readAsDataURL(file);
                });
            },

            async scanFromFile(event) {
                        const files = event.target.files;
                        if (!files || files.length === 0) return;
                        const file = files[0];

                        this.photoErrorMessage = '';

                        if (typeof Html5Qrcode === 'undefined') {
                            this.photoErrorMessage = 'O leitor de código de barras não foi carregado. Verifique sua conexão.';
                            return;
                        }

                        try {
                            const html5QrCode = new Html5Qrcode("barcode-reader-view");
                            const decodedText = await html5QrCode.scanFile(file, true);

                            if (decodedText) {
                                this.playBeep();
                                this.onSuccess(decodedText);
                            } else {
                                throw new Error("Código não reconhecido na verificação simples.");
                            }
                        } catch (err) {
                            console.warn("Tentando com pré-processamento avançado para metal/alumínio...", err);
                            await this.scanFileWithAdvancedFilters(file);
                        }
                    },

                    async scanFileWithAdvancedFilters(file) {
                        try {
                            const img = await this.loadImage(file);

                            const canvasNorm = this.processImageCanvas(img, false);
                            let code = await this.tryDecodeFromCanvas(canvasNorm);

                            if (!code) {
                                const canvasInverted = this.processImageCanvas(img, true);
                                code = await this.tryDecodeFromCanvas(canvasInverted);
                            }

                            if (code) {
                                this.playBeep();
                                this.onSuccess(code);
                            } else {
                                this.photoErrorMessage = 'Não foi possível ler o código de barras nesta foto. Tente tirar a foto com mais iluminação, sem reflexo direto no metal/alumínio.';
                                this.isOpen = true;
                                this.$nextTick(() => this.tryStartLiveCamera());
                            }
                        } catch (e) {
                            console.error("Erro ao decodificar foto metálica:", e);
                            this.photoErrorMessage = 'Não foi possível ler o código de barras nesta foto. Tente tirar a foto com mais iluminação, sem reflexo direto no metal/alumínio.';
                            this.isOpen = true;
                            this.$nextTick(() => this.tryStartLiveCamera());
                        }
                    },

                    loadImage(file) {
                        return new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onload = e => {
                                const img = new Image();
                                img.onload = () => resolve(img);
                                img.onerror = reject;
                                img.src = e.target.result;
                            };
                            reader.onerror = reject;
                            reader.readAsDataURL(file);
                        });
                    },

                    processImageCanvas(img, invertColors = false) {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        const maxDim = 1200;
                        let width = img.width;
                        let height = img.height;
                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;

                        ctx.drawImage(img, 0, 0, width, height);
                        const imageData = ctx.getImageData(0, 0, width, height);
                        const data = imageData.data;

                        for (let i = 0; i < data.length; i += 4) {
                            const r = data[i];
                            const g = data[i + 1];
                            const b = data[i + 2];

                            let gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
                            gray = (gray - 128) * 1.5 + 128;
                            gray = Math.min(255, Math.max(0, gray));

                            if (invertColors) {
                                gray = 255 - gray;
                            }

                            data[i] = gray;
                            data[i + 1] = gray;
                            data[i + 2] = gray;
                        }

                        ctx.putImageData(imageData, 0, 0);
                        return canvas;
                    },

                    async tryDecodeFromCanvas(canvas) {
                        if (typeof Html5Qrcode === 'undefined') return null;

                        return new Promise((resolve) => {
                            canvas.toBlob(async (blob) => {
                                if (!blob) return resolve(null);
                                const tempFile = new File([blob], "temp_scan.png", { type: "image/png" });
                                const html5QrCode = new Html5Qrcode("barcode-reader-view");
                                try {
                                    const text = await html5QrCode.scanFile(tempFile, false);
                                    resolve(text);
                                } catch (e) {
                                    resolve(null);
                                }
                            }, 'image/png');
                        });
                    },

                    async onSuccess(decodedText) {
                        let cleanText = (decodedText || '').trim();

                        if (cleanText.startsWith('http://') || cleanText.startsWith('https://')) {
                            try {
                                const url = new URL(cleanText);
                                const pathSegments = url.pathname.split('/').filter(Boolean);
                                if (pathSegments.length > 0) {
                                    cleanText = pathSegments[pathSegments.length - 1];
                                }
                            } catch(e) {}
                        }

                        await this.stopCamera();
                        this.isOpen = false;

                        if (this.targetInput) {
                            let el = null;
                            if (typeof this.targetInput === 'string') {
                                el = document.getElementById(this.targetInput);
                            } else if (this.targetInput instanceof HTMLElement) {
                                el = this.targetInput;
                            }

                            if (el) {
                                el.value = cleanText;
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                                el.dispatchEvent(new Event('change', { bubbles: true }));
                                el.blur();
                            }
                        }
                        if (document.activeElement && typeof document.activeElement.blur === 'function') {
                            document.activeElement.blur();
                        }
                    },

                    async stopCamera() {
                        this.isScanning = false;
                        this.liveStreamActive = false;
                        if (this.html5QrCode) {
                            try {
                                if (this.html5QrCode.isScanning) {
                                    await this.html5QrCode.stop();
                                }
                                this.html5QrCode.clear();
                            } catch (e) {
                                console.warn("Aviso ao encerrar leitor de código de barras:", e);
                            }
                            this.html5QrCode = null;
                        }
                    },

                    async closeScanner() {
                        await this.stopCamera();
                        this.isOpen = false;
                    },

                    playBeep() {
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.type = "sine";
                            osc.frequency.value = 1200;
                            gain.gain.value = 0.15;
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.12);
                        } catch (e) {
                            // Silencioso se áudio não for suportado
                        }
                    }
                }));
            }
        }

        if (typeof Alpine !== 'undefined') {
            initBarcodeComponent();
        }
        document.addEventListener('alpine:init', initBarcodeComponent);
        document.addEventListener('livewire:init', initBarcodeComponent);
    })();

    window.escanearCodigoBarras = function(target) {
        if (document.activeElement && typeof document.activeElement.blur === 'function') {
            document.activeElement.blur();
        }
        window.dispatchEvent(new CustomEvent('open-barcode-scanner', { detail: { target: target } }));
    };
</script>
