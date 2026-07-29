@props([
    'placeholder' => 'Ex: BRJ123456',
    'wireModel' => null,
])

<div x-data="{
    loadingMode: null,
    errorMessage: '',
    successMessage: '',

    triggerCamera1() {
        this.$refs.fileInput1.click();
    },

    triggerCamera2() {
        this.$refs.fileInput2.click();
    },

    async loadTesseractScript() {
        if (window.Tesseract) return true;
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
            script.onload = () => resolve(true);
            script.onerror = () => reject(new Error('Não foi possível carregar a biblioteca Tesseract.js via CDN.'));
            document.head.appendChild(script);
        });
    },

    async compressImage(file, maxWidth = 1280, quality = 0.85) {
        return new Promise((resolve) => {
            if (!file.type.startsWith('image/')) {
                resolve(file);
                return;
            }
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob) {
                            resolve(new File([blob], file.name || 'camera_photo.jpg', { type: 'image/jpeg' }));
                        } else {
                            resolve(file);
                        }
                    }, 'image/jpeg', quality);
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
        });
    },

    extractCleanSerial(rawText) {
        if (!rawText) return '';

        const lines = rawText.split(/[\r\n]+/);
        const candidates = [];

        for (let line of lines) {
            const digitMatches = line.match(/\d+/g) || [];
            for (let match of digitMatches) {
                // Descarta ruídos de código de barras (ex: 4 ou mais '1's repetidos como 11111)
                if (/1{4,}/.test(match)) {
                    continue;
                }
                // Descarta dígitos idênticos repetidos 4+ vezes (ex: 0000, 1111)
                if (/(\d)\1{3,}/.test(match)) {
                    continue;
                }
                // Tamanho válido para número de serial/patrimônio (3 a 15 dígitos)
                if (match.length >= 3 && match.length <= 15) {
                    candidates.push(match);
                }
            }
        }

        if (candidates.length > 0) {
            // Prioriza candidatos com tamanho típico de serial/patrimônio (4 a 12 dígitos)
            const idealCandidate = candidates.find(c => c.length >= 4 && c.length <= 12);
            return idealCandidate || candidates[0];
        }

        // Fallback: remove sequências de 1s repetidos de código de barras
        const cleaned = rawText.replace(/1{4,}/g, '').replace(/[^0-9]/g, '');
        return cleaned;
    },

    async uploadImageServer(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.loadingMode = 'camera1';
        this.errorMessage = '';
        this.successMessage = '';

        try {
            const processedFile = await this.compressImage(file);

            const formData = new FormData();
            formData.append('image', processedFile);

            const csrfMeta = document.querySelector('meta[name=csrf-token]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const response = await fetch('{{ route("api.ocr.read-serial") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                const serialText = this.extractCleanSerial(data.text);
                if (serialText && serialText.length > 0) {
                    this.updateInputValue(serialText);
                    this.successMessage = '[Câmera 1] Código lido: ' + serialText;
                    setTimeout(() => { this.successMessage = ''; }, 4000);
                } else {
                    this.errorMessage = 'Nenhum número válido reconhecido (Câmera 1). Aproxime a foto dos números da etiqueta.';
                    setTimeout(() => { this.errorMessage = ''; }, 6000);
                }
            } else {
                let msg = data.message;
                if (data.errors && data.errors.image) {
                    msg = data.errors.image[0];
                }
                this.errorMessage = msg || 'Não foi possível reconhecer o código na imagem (Câmera 1). Tente a Câmera 2 ou digite manualmente.';
                setTimeout(() => { this.errorMessage = ''; }, 6000);
            }
        } catch (error) {
            console.error('OCR Server Error:', error);
            this.errorMessage = 'Erro ao processar imagem no servidor (Câmera 1). Tente a Câmera 2 ou digite manualmente.';
            setTimeout(() => { this.errorMessage = ''; }, 6000);
        } finally {
            this.loadingMode = null;
            event.target.value = '';
        }
    },

    async processImageClient(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.loadingMode = 'camera2';
        this.errorMessage = '';
        this.successMessage = '';

        try {
            await this.loadTesseractScript();

            const processedFile = await this.compressImage(file);

            const result = await Tesseract.recognize(processedFile, 'eng');
            const rawText = result?.data?.text || '';
            const cleanSerial = this.extractCleanSerial(rawText);

            if (cleanSerial && cleanSerial.length > 0) {
                this.updateInputValue(cleanSerial);

                this.successMessage = '[Câmera 2] Código lido: ' + cleanSerial;
                setTimeout(() => { this.successMessage = ''; }, 4000);
            } else {
                this.errorMessage = 'Nenhum número válido reconhecido (Câmera 2). Dica: aproxime a câmera apenas dos números da etiqueta.';
                setTimeout(() => { this.errorMessage = ''; }, 6000);
            }
        } catch (error) {
            console.error('Tesseract.js Error:', error);
            this.errorMessage = 'Falha no processamento local (Câmera 2). Sugerimos usar a Câmera 1 ou digitar manualmente.';
            setTimeout(() => { this.errorMessage = ''; }, 6000);
        } finally {
            this.loadingMode = null;
            event.target.value = '';
        }
    },

    updateInputValue(val) {
        const inputEl = this.$refs.textInput;
        inputEl.value = val;

        inputEl.dispatchEvent(new Event('input', { bubbles: true }));
        inputEl.dispatchEvent(new Event('change', { bubbles: true }));

        @if($wireModel)
            if (typeof $wire !== 'undefined') {
                $wire.set('{{ $wireModel }}', val);
            }
        @endif
    }
}" class="space-y-1 w-full">
    <div class="relative flex items-center w-full">
        <input x-ref="textInput"
               {{ $attributes->merge([
                   'type' => 'text',
                   'placeholder' => $placeholder,
                   'class' => 'w-full pl-4 pr-36 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-colors'
               ]) }}>

        <!-- File Input para Câmera 1 (Servidor PHP / Tesseract) -->
        <input type="file"
               x-ref="fileInput1"
               @change="uploadImageServer"
               accept="image/*"
               capture="environment"
               class="hidden">

        <!-- File Input para Câmera 2 (Navegador Client-side / Tesseract.js) -->
        <input type="file"
               x-ref="fileInput2"
               @change="processImageClient"
               accept="image/*"
               capture="environment"
               class="hidden">

        <!-- Botões Câmera 1 e Câmera 2 -->
        <div class="absolute right-1.5 flex items-center gap-1">
            <!-- Botão Câmera 1 (PHP) -->
            <button type="button"
                    @click="triggerCamera1"
                    :disabled="loadingMode !== null"
                    title="Câmera 1: Ler via Servidor (PHP Tesseract)"
                    class="px-2 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 bg-slate-200/70 dark:bg-slate-800 hover:bg-emerald-500/20 focus:outline-none transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1 border border-slate-300/60 dark:border-slate-700">
                <template x-if="loadingMode !== 'camera1'">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cam 1</span>
                    </div>
                </template>
                <template x-if="loadingMode === 'camera1'">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-[10px]">Lendo...</span>
                    </div>
                </template>
            </button>

            <!-- Botão Câmera 2 (Tesseract.js) -->
            <button type="button"
                    @click="triggerCamera2"
                    :disabled="loadingMode !== null"
                    title="Câmera 2: Ler no Navegador (Tesseract.js)"
                    class="px-2 py-1 rounded-lg text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-teal-600 dark:hover:text-teal-400 bg-slate-200/70 dark:bg-slate-800 hover:bg-teal-500/20 focus:outline-none transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1 border border-slate-300/60 dark:border-slate-700">
                <template x-if="loadingMode !== 'camera2'">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Cam 2</span>
                    </div>
                </template>
                <template x-if="loadingMode === 'camera2'">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-[10px]">Lendo...</span>
                    </div>
                </template>
            </button>
        </div>
    </div>

    <!-- Mensagens de Sucesso ou Erro do OCR -->
    <template x-if="successMessage">
        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span x-text="successMessage"></span>
        </span>
    </template>

    <template x-if="errorMessage">
        <span class="text-xs font-semibold text-red-500 dark:text-red-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-text="errorMessage"></span>
        </span>
    </template>
</div>
