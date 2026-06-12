<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Scan Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js" defer></script>
    <script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js" defer></script>
    <style>
        #cameraPreview {
            position: absolute;
            inset: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            border-radius: 1.75rem;
            background: #020617;
        }

        #cameraPreview video,
        #cameraPreview canvas {
            height: 100%;
            width: 100%;
            object-fit: cover;
            display: block;
        }
    </style>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans overflow-x-hidden">

    @include('dashboard.partials.sidebar', ['activePage' => 'scan'])

    <!-- MAIN CONTENT AREA -->
    <div class="flex-grow flex min-w-0 flex-col transition-all duration-300">
        
        <!-- Header / Top Bar -->
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center">
            <h1 class="text-[#0033ab] text-xl font-bold">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="menuBtn" data-open-mobile-sidebar class="shrink-0 rounded-lg p-2 text-gray-600 hover:bg-gray-100">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </header>

        <main class="flex max-w-full flex-col items-stretch justify-center gap-6 p-4 md:p-8 xl:flex-row xl:items-start xl:gap-8">
            
            <!-- Left Side: Scanner View -->
            <div class="flex w-full min-w-0 flex-col gap-6 xl:w-1/2">
                <!-- Camera Box -->
                <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden rounded-[2rem] border-4 border-white bg-gray-900 shadow-2xl sm:aspect-video xl:aspect-square">
                    <div id="cameraPreview" class="absolute inset-0 z-0 h-full w-full">
                        <video id="cameraVideo" class="h-full w-full object-cover" autoplay playsinline muted></video>
                    </div>
                    <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/55 via-black/10 to-black/45"></div>

                    <div class="relative z-20 flex w-full flex-col items-center px-4">
                        <p class="mb-6 rounded-full bg-black/30 px-3 py-1 text-center text-xs font-bold uppercase tracking-[0.2em] text-white backdrop-blur-sm sm:mb-8 sm:px-4 sm:text-sm md:text-base">Use your camera to detect barcode or QR code</p>
                        <div class="relative h-28 w-44 border-4 border-transparent sm:h-32 sm:w-48 md:h-40 md:w-64">
                            <div class="absolute left-0 top-0 h-8 w-8 border-l-4 border-t-4 border-blue-500"></div>
                            <div class="absolute right-0 top-0 h-8 w-8 border-r-4 border-t-4 border-blue-500"></div>
                            <div class="absolute bottom-0 left-0 h-8 w-8 border-b-4 border-l-4 border-blue-500"></div>
                            <div class="absolute bottom-0 right-0 h-8 w-8 border-b-4 border-r-4 border-blue-500"></div>
                            <div class="absolute left-0 top-1/2 h-1 w-full -translate-y-1/2 bg-blue-400 shadow-[0_0_15px_rgba(96,165,250,0.8)]"></div>
                        </div>
                        <p id="cameraStatus" class="mt-6 rounded-full bg-black/30 px-4 py-2 text-center text-[11px] font-bold uppercase tracking-[0.18em] text-white backdrop-blur-sm sm:text-xs">Starting camera...</p>
                    </div>
                </div>

                <!-- Manual Input -->
                <div class="rounded-[1.5rem] border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Manual QR</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input type="text" id="manualInput" placeholder="Enter QR text..." 
                            class="min-w-0 flex-grow rounded-xl border border-gray-100 bg-gray-50 px-5 py-3 font-bold transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                        <button onclick="handleManualInput()" class="w-full rounded-xl bg-[#0033ab] px-8 py-3 font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-800 active:scale-95 sm:w-auto">
                            Scan
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex w-full min-w-0 items-center justify-center rounded-[2rem] border border-dashed border-blue-100 bg-white/70 p-8 text-center shadow-sm xl:w-1/2">
                <div class="max-w-md">
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-[#0033ab]">
                        <i data-lucide="scan-search" class="h-10 w-10"></i>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight text-gray-800 sm:text-4xl">Ready to Verify</h2>
                    <p id="systemStatus" class="mt-3 text-base font-bold text-gray-400 sm:text-lg">
                        Scan an invoice QR code to open the verification popup.
                    </p>
                </div>
            </div>
        </main>

        <div id="verificationModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 px-0 pt-10 backdrop-blur-sm sm:items-center sm:px-4 sm:py-6">
            <div class="max-h-[70vh] w-full overflow-y-auto rounded-t-[2rem] border border-gray-100 bg-white p-5 shadow-2xl sm:max-h-[85vh] sm:max-w-3xl sm:rounded-[2rem] sm:p-8">
                <div class="mx-auto mb-4 h-1.5 w-20 rounded-full bg-gray-200 sm:hidden"></div>
                <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-3xl font-black tracking-tight text-gray-800 sm:text-4xl">Verification</h2>
                        <p class="mt-1 font-bold text-gray-400">Record Detail</p>
                    </div>
                    <div id="matchBadge" class="flex w-full items-center justify-center gap-2 rounded-full border-2 border-gray-300 bg-gray-50 px-5 py-2 text-sm font-black uppercase tracking-wider text-gray-600 sm:w-auto sm:justify-start sm:px-6">
                        <i id="statusIcon" data-lucide="check-circle" class="w-5 h-5"></i>
                        <span id="statusText">Not Scanned</span>
                    </div>
                </div>

                <div class="mb-8 space-y-4 sm:mb-10">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-blue-50 bg-[#f8faff] p-4 sm:p-5">
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-gray-400">Product ID</label>
                            <input type="text" id="productId" value="-" readonly
                                class="w-full bg-transparent text-lg font-black text-gray-800 focus:outline-none sm:text-xl md:text-2xl">
                        </div>
                        <div class="rounded-2xl border border-blue-50 bg-[#f8faff] p-4 sm:p-5">
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-gray-400">Invoice ID</label>
                            <input type="text" id="invoiceId" value="-" readonly
                                class="w-full bg-transparent text-lg font-black text-gray-800 focus:outline-none sm:text-xl md:text-2xl">
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 rounded-2xl border border-blue-50 bg-[#f8faff] p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                        <div class="min-w-0 flex-grow">
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-gray-400">Vendor Name</label>
                            <input type="text" id="vendorName" value="-" readonly
                                class="w-full bg-transparent text-xl font-black text-gray-800 focus:outline-none sm:text-2xl">
                        </div>
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center self-start rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-100 sm:self-auto">
                            <i data-lucide="truck" class="h-7 w-7"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-blue-50 bg-[#f8faff] p-4 sm:p-5">
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-gray-400">Scanned Boxes</label>
                            <input type="text" id="scannedCount" value="0 / 0" readonly
                                class="w-full bg-transparent text-lg font-black text-gray-800 focus:outline-none sm:text-xl md:text-2xl">
                        </div>
                        <div class="rounded-2xl border border-blue-50 bg-[#f8faff] p-4 sm:p-5">
                            <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-gray-400">Remaining</label>
                            <input type="text" id="remainingCount" value="0" readonly
                                class="w-full bg-transparent text-lg font-black text-gray-800 focus:outline-none sm:text-xl md:text-2xl">
                        </div>
                    </div>
                </div>

                <div class="grid gap-4">
                    <button id="confirmBtn" onclick="handleConfirm()"
                        class="w-full rounded-[1.25rem] bg-[#0033ab] py-4 text-lg font-black text-white shadow-xl shadow-blue-100 transition hover:bg-blue-800 active:scale-[0.98] sm:py-5 sm:text-xl">
                        Confirm Data
                    </button>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <button id="cancelButton" onclick="handleCancel()" class="rounded-[1.25rem] border-2 border-gray-200 bg-white py-4 text-lg font-bold text-gray-500 transition hover:bg-gray-50">
                            Cancel
                        </button>
                        <button id="pendingButton" onclick="markPending()" class="rounded-[1.25rem] border-2 border-blue-100 bg-white py-4 text-lg font-bold text-[#0033ab] transition hover:bg-blue-50">
                            Pending
                        </button>
                        <button id="endButton" onclick="completeScan()" class="rounded-[1.25rem] border-2 border-red-100 bg-white py-4 text-lg font-bold text-red-500 transition hover:bg-red-50">
                            End
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Footer -->
        <footer class="mt-auto bg-[#001a4d] text-white p-6 text-[10px] md:text-xs">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4 opacity-70">
                <div class="flex gap-6">
                    <p class="flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Database Online</p>
                    <p>Updated: 2026-05-03 10:48 WIB</p>
                </div>
                <p class="font-bold tracking-widest">SVS v1.2.0-STABLE</p>
            </div>
        </footer>
    </div>

    <script>
        lucide.createIcons();

        const SCAN_API = "/scan/data";
        const ACTIVITY_LOG_URL = "/activity-log";
        const cameraStatus = document.getElementById('cameraStatus');
        const cameraVideo = document.getElementById('cameraVideo');
        const verificationModal = document.getElementById('verificationModal');
        const csrfToken = '{{ csrf_token() }}';
        let isHandlingScan = false;
        let isVerificationModalOpen = false;
        let lastScannedText = '';
        let lastScanAt = 0;
        let mediaStream = null;
        let detectorInterval = null;
        let barcodeDetector = null;
        let zxingReader = null;
        let activeInvoiceId = null;
        const scanCanvas = document.createElement('canvas');
        const scanContext = scanCanvas.getContext('2d', { willReadFrequently: true });

        function logActivity(payload) {
            fetch(ACTIVITY_LOG_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            }).catch(() => {});
        }

        function handleManualInput() {
            const val = document.getElementById('manualInput').value.trim();
            if (val) {
                document.getElementById('manualInput').value = val;
                handleScan(val);
            }
        }

        async function handleScan(scannedText) {
            const normalizedText = scannedText.trim();
            const now = Date.now();

            if (!normalizedText || isHandlingScan || isVerificationModalOpen) {
                return;
            }

            if (normalizedText === lastScannedText && now - lastScanAt < 3000) {
                return;
            }

            isHandlingScan = true;
            lastScannedText = normalizedText;
            lastScanAt = now;
            document.getElementById('manualInput').value = normalizedText;
            document.getElementById('systemStatus').textContent = "Searching...";
            setCameraStatus("QR detected. Checking record...");

            try {
                const response = await fetch(SCAN_API, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ qr_text: normalizedText })
                });
                const result = await response.json();

                if (response.ok) {
                    updateUI(result.data);
                    document.getElementById('systemStatus').textContent = "Record found. Review the popup to continue.";
                    setCameraStatus("QR detected successfully. Confirm the popup to scan again.");
                    openVerificationModal();
                } else {
                    document.getElementById('systemStatus').textContent = result.message || "Record not found";
                    setCameraStatus("QR detected, but record was not found");
                }
            } catch (error) {
                console.error("API Error:", error);
                document.getElementById('systemStatus').textContent = "Connection error";
                setCameraStatus("Camera scan worked, but the server could not be reached");
            } finally {
                isHandlingScan = false;
            }
        }

        function updateUI(data) {
            if (!data.invoice) {
                activeInvoiceId = null;
                document.getElementById('productId').value = '-';
                document.getElementById('invoiceId').value = '-';
                document.getElementById('vendorName').value = '-';
                document.getElementById('scannedCount').value = '-';
                document.getElementById('remainingCount').value = '-';
                return;
            }

            activeInvoiceId = data.invoice.invoice_id || null;
            document.getElementById('productId').value = data.invoice.product_id || '-';
            document.getElementById('invoiceId').value = data.invoice.invoice_code || '-';
            document.getElementById('vendorName').value = data.invoice.vendor_name || '-';
            document.getElementById('scannedCount').value = `${data.invoice.scanned_box_count || 0} / ${data.invoice.box_quantity || 0}`;
            document.getElementById('remainingCount').value = `${data.invoice.remaining_box_count || 0}`;
            setStatusBadge(data.verification.status);
        }

        async function handleConfirm() {
            if (!activeInvoiceId) {
                closeVerificationModal();
                document.getElementById('systemStatus').textContent = "Verification confirmed. Ready for the next scan.";
                setCameraStatus("Camera is active. Point it at a barcode or QR code");
                return;
            }

            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Confirming...';

            try {
                const response = await fetch(`/scan/invoices/${activeInvoiceId}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to confirm scan');
                }

                const data = result.data || {};
                document.getElementById('scannedCount').value = `${data.scanned_box_count || 0} / ${data.target_box_count || 0}`;
                document.getElementById('remainingCount').value = `${Math.max((data.target_box_count || 0) - (data.scanned_box_count || 0), 0)}`;
                setStatusBadge(data.status || 'on_progress');
                document.getElementById('systemStatus').textContent = result.message || 'Scan confirmed. Ready for the next scan.';
            } catch (error) {
                alert(error.message || 'Failed to confirm scan');
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Data';
                closeVerificationModal();
                setCameraStatus("Camera is active. Point it at a barcode or QR code");
            }
        }

        function handleCancel() {
            closeVerificationModal();
            document.getElementById('systemStatus').textContent = "Scan cancelled. Ready for the next scan.";
            setCameraStatus("Camera is active. Point it at a barcode or QR code");
        }

        async function markPending() {
            if (!activeInvoiceId) {
                alert('Scan an invoice barcode first.');
                return;
            }

            await updateInvoiceScanStatus(`/scan/invoices/${activeInvoiceId}/pending`);
        }

        async function completeScan() {
            if (!activeInvoiceId) {
                alert('Scan an invoice barcode first.');
                return;
            }

            await updateInvoiceScanStatus(`/scan/invoices/${activeInvoiceId}/complete`);
        }

        async function updateInvoiceScanStatus(url) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to update invoice status');
                }

                const data = result.data || {};
                document.getElementById('scannedCount').value = `${data.scanned_box_count || 0} / ${data.target_box_count || 0}`;
                document.getElementById('remainingCount').value = `${Math.max((data.target_box_count || 0) - (data.scanned_box_count || 0), 0)}`;
                setStatusBadge(data.status || 'pending');
                document.getElementById('systemStatus').textContent = result.message || 'Status updated';
                closeVerificationModal();
                setCameraStatus("Camera is active. Point it at a barcode or QR code");
            } catch (error) {
                alert(error.message || 'Failed to update invoice status');
            }
        }

        function openVerificationModal() {
            isVerificationModalOpen = true;
            verificationModal.classList.remove('hidden');
            verificationModal.classList.add('flex');
        }

        function closeVerificationModal() {
            isVerificationModalOpen = false;
            verificationModal.classList.add('hidden');
            verificationModal.classList.remove('flex');
        }

        function setStatusBadge(status) {
            const normalized = String(status || '').trim().toUpperCase();
            const badge = document.getElementById('matchBadge');
            const icon = document.getElementById('statusIcon');
            const text = document.getElementById('statusText');

            const styles = normalized === 'MATCH'
                ? ['border-green-500 bg-green-50 text-green-600', 'check-circle', 'Match']
                : normalized === 'LESS'
                    ? ['border-red-500 bg-red-50 text-red-600', 'circle-alert', 'Less']
                    : normalized === 'OVER'
                        ? ['border-red-500 bg-red-50 text-red-600', 'circle-alert', 'Over']
                        : normalized === 'PENDING'
                            ? ['border-orange-400 bg-orange-50 text-orange-500', 'alert-triangle', 'Pending']
                            : normalized === 'ON_PROGRESS'
                                ? ['border-blue-500 bg-blue-50 text-blue-600', 'scan-line', 'On Progress']
                                : ['border-gray-300 bg-gray-50 text-gray-600', 'circle-help', 'Not Scanned'];

            badge.className = `flex w-full items-center justify-center gap-2 rounded-full border-2 px-5 py-2 text-sm font-black uppercase tracking-wider sm:w-auto sm:justify-start sm:px-6 ${styles[0]}`;
            icon.setAttribute('data-lucide', styles[1]);
            text.textContent = styles[2];
            lucide.createIcons();
        }

        function setCameraStatus(message) {
            cameraStatus.textContent = message;
        }

        async function startCameraScanner() {
            try {
                if ('BarcodeDetector' in window) {
                    const supportedFormats = await window.BarcodeDetector.getSupportedFormats();
                    const preferredFormats = ['code_128', 'qr_code', 'ean_13', 'ean_8', 'upc_a', 'upc_e'];
                    const formats = preferredFormats.filter((format) => supportedFormats.includes(format));

                    if (formats.length > 0) {
                        barcodeDetector = new window.BarcodeDetector({ formats });
                    }
                }

                if (window.ZXing?.MultiFormatReader) {
                    const hints = new Map();
                    hints.set(window.ZXing.DecodeHintType.POSSIBLE_FORMATS, [
                        window.ZXing.BarcodeFormat.CODE_128,
                        window.ZXing.BarcodeFormat.QR_CODE,
                        window.ZXing.BarcodeFormat.EAN_13,
                        window.ZXing.BarcodeFormat.EAN_8,
                        window.ZXing.BarcodeFormat.UPC_A,
                        window.ZXing.BarcodeFormat.UPC_E,
                    ]);
                    hints.set(window.ZXing.DecodeHintType.TRY_HARDER, true);

                    zxingReader = new window.ZXing.MultiFormatReader();
                    zxingReader.setHints(hints);
                }

                if (!barcodeDetector && !zxingReader && typeof window.jsQR !== 'function') {
                    setCameraStatus("Barcode scanner failed to load");
                    return;
                }
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' }
                    },
                    audio: false
                });

                cameraVideo.srcObject = mediaStream;
                await cameraVideo.play();
                setCameraStatus("Camera is active. Point it at a barcode or QR code");
                startDetectionLoop();
            } catch (error) {
                console.error("Camera Error:", error);
                setCameraStatus("Camera access failed. Please allow camera permission");
            }
        }

        function startDetectionLoop() {
            stopDetectionLoop();

            detectorInterval = window.setInterval(async () => {
                if (!scanContext || !cameraVideo || cameraVideo.readyState < 2 || isHandlingScan || isVerificationModalOpen) {
                    return;
                }

                try {
                    const width = cameraVideo.videoWidth;
                    const height = cameraVideo.videoHeight;

                    if (!width || !height) {
                        return;
                    }

                    scanCanvas.width = width;
                    scanCanvas.height = height;
                    scanContext.drawImage(cameraVideo, 0, 0, width, height);

                    if (barcodeDetector) {
                        const barcodes = await barcodeDetector.detect(scanCanvas);

                        if (barcodes.length > 0) {
                            const rawValue = barcodes[0]?.rawValue?.trim();

                            if (rawValue) {
                                handleScan(rawValue);
                                return;
                            }
                        }
                    }

                    if (zxingReader) {
                        const imageData = scanContext.getImageData(0, 0, width, height);
                        const luminanceSource = new window.ZXing.RGBLuminanceSource(imageData.data, width, height);
                        const binaryBitmap = new window.ZXing.BinaryBitmap(
                            new window.ZXing.HybridBinarizer(luminanceSource)
                        );

                        try {
                            const result = zxingReader.decode(binaryBitmap);
                            const rawValue = result?.getText?.()?.trim();

                            if (rawValue) {
                                handleScan(rawValue);
                                zxingReader.reset();
                                return;
                            }
                        } catch (decodeError) {
                            // No barcode found in this frame.
                        }
                    }

                    if (typeof window.jsQR === 'function') {
                        const imageData = scanContext.getImageData(0, 0, width, height);
                        const qrCode = window.jsQR(imageData.data, width, height, {
                            inversionAttempts: 'dontInvert'
                        });

                        if (qrCode?.data) {
                            handleScan(qrCode.data);
                        }
                    }
                } catch (error) {
                    console.error("Detection Error:", error);
                }
            }, 300);
        }

        function stopDetectionLoop() {
            if (detectorInterval) {
                window.clearInterval(detectorInterval);
                detectorInterval = null;
            }
        }

        document.getElementById('manualInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleManualInput();
        });

        window.addEventListener('load', () => {
            logActivity({
                action: 'VIEW_SCAN_PAGE',
                table_name: 'pages',
                description: 'Opened scan page',
            });
            startCameraScanner();
        });
        window.addEventListener('beforeunload', () => {
            stopDetectionLoop();

            if (mediaStream) {
                mediaStream.getTracks().forEach((track) => track.stop());
            }
        });
    </script>
</body>
</html>
