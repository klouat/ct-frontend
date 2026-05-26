<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Print Invoice Barcodes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'invoice-barcodes'])

    <div class="flex-grow flex min-h-screen flex-col">
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center">
            <h1 class="text-[#0033ab] text-xl font-bold">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="menuBtn" data-open-mobile-sidebar><i data-lucide="menu"></i></button>
            </div>
        </header>

        <main class="flex-grow p-4 md:p-8">
            <div class="mx-auto max-w-6xl space-y-6">
                <div class="flex flex-col gap-2">
                    <h2 class="text-3xl font-bold text-[#001a4d]">Print Invoice Barcodes</h2>
                    <p class="text-sm text-gray-500">List of all invoices with QR previews for printing and testing.</p>
                </div>

                <p id="pageMessage" class="hidden rounded-xl border px-4 py-3 text-sm font-medium"></p>

                <div id="barcodeList" class="grid gap-6">
                    <div class="animate-pulse space-y-4">
                        <div class="h-48 rounded-3xl bg-white shadow-sm"></div>
                        <div class="h-48 rounded-3xl bg-white shadow-sm"></div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="mt-auto bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between opacity-80">
                <div>
                    <p>Database: Connected (Local Sync Active)</p>
                    <p>Last Update: 2026-05-03 10:48 WIB</p>
                </div>
                <p class="mt-2 md:mt-0">App Version: 1.2.0-stable</p>
            </div>
        </footer>
    </div>

    <script>
        lucide.createIcons();

        const DATA_URL = '/invoice-barcodes/data';
        const ACTIVITY_LOG_URL = '/activity-log';
        const barcodeList = document.getElementById('barcodeList');
        const pageMessage = document.getElementById('pageMessage');

        function setMessage(text, tone = 'error') {
            if (!text) {
                pageMessage.className = 'hidden rounded-xl border px-4 py-3 text-sm font-medium';
                pageMessage.textContent = '';
                return;
            }

            const toneClasses = tone === 'info'
                ? 'border-blue-200 bg-blue-50 text-blue-700'
                : 'border-red-200 bg-red-50 text-red-600';

            pageMessage.className = `rounded-xl border px-4 py-3 text-sm font-medium ${toneClasses}`;
            pageMessage.textContent = text;
        }

        function logActivity(payload) {
            fetch(ACTIVITY_LOG_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            }).catch(() => {});
        }

        async function loadInvoices() {
            try {
                const response = await fetch(DATA_URL, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                const payload = await response.json();

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to load invoices');
                }

                renderInvoices(payload.data?.items || []);
            } catch (error) {
                barcodeList.innerHTML = '';
                setMessage(error.message || 'Failed to load invoices');
            }
        }

        function renderInvoices(items) {
            if (!items.length) {
                barcodeList.innerHTML = `
                    <div class="rounded-3xl border border-blue-100 bg-white p-10 text-center shadow-sm">
                        <p class="text-lg font-bold text-[#001a4d]">No invoices found</p>
                        <p class="mt-2 text-sm text-gray-500">Create an invoice first, then its barcode will appear here.</p>
                    </div>
                `;
                setMessage('No invoices found.', 'info');
                return;
            }

            barcodeList.innerHTML = items.map((item, index) => {
                const vendorName = item.vendor?.vendor_name || 'Unknown Vendor';
                const qrText = item.qr_text || item.invoice_code || '';

                return `
                    <div class="rounded-3xl border border-blue-100 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Invoice ID</p>
                                <h3 class="mt-1 text-2xl font-black text-[#001a4d]">${item.invoice_code || '-'}</h3>
                                <div class="mt-4 grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                                    <p><span class="font-bold text-gray-800">Product:</span> ${item.product_name || '-'}</p>
                                    <p><span class="font-bold text-gray-800">Product ID:</span> ${item.product_id || '-'}</p>
                                    <p><span class="font-bold text-gray-800">Vendor:</span> ${vendorName}</p>
                                    <p><span class="font-bold text-gray-800">Total Box:</span> ${item.target_box_count || 0}</p>
                                </div>
                            </div>
                            <button type="button" onclick="printBarcode(${index})" class="shrink-0 rounded-xl border border-blue-200 px-5 py-3 text-sm font-bold text-[#0033ab] transition hover:bg-blue-50">
                                Print QR
                            </button>
                        </div>

                        <div class="mt-6 rounded-[2rem] border border-dashed border-blue-200 bg-[#fcfdff] p-6">
                            <div id="barcode-${index}" class="mx-auto flex justify-center"></div>
                            <p class="mt-4 break-all text-center text-sm font-semibold text-gray-500">${qrText}</p>
                        </div>
                    </div>
                `;
            }).join('');

            window.invoiceBarcodeItems = items;

            items.forEach((item, index) => {
                new QRCode(document.getElementById(`barcode-${index}`), {
                    text: item.qr_text || item.invoice_code || '',
                    width: 220,
                    height: 220,
                    colorDark: '#173f9b',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
            });

            setMessage('');
        }

        function printBarcode(index) {
            const items = window.invoiceBarcodeItems || [];
            const item = items[index];

            if (!item) {
                return;
            }

            const printWindow = window.open('', '_blank', 'width=800,height=700');

            if (!printWindow) {
                return;
            }

            logActivity({
                action: 'PRINT_INVOICE_BARCODE',
                table_name: 'invoices',
                record_id: item.invoice_id || null,
                description: `Printed invoice QR for ${item.invoice_code || '-'}`,
            });

            const vendorName = item.vendor?.vendor_name || 'Unknown Vendor';
            const barcodeValue = item.qr_text || item.invoice_code || '';

            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>Print Invoice QR</title>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 24px; color: #1f2937; }
                        .sheet { border: 1px dashed #bfdbfe; border-radius: 24px; padding: 28px; }
                        .title { font-size: 28px; font-weight: 800; color: #173f9b; margin-bottom: 12px; }
                        .meta { margin-top: 8px; font-size: 15px; }
                        .value { margin-top: 18px; text-align: center; font-weight: 700; word-break: break-all; }
                    </style>
                </head>
                <body>
                    <div class="sheet">
                        <div class="title">${item.invoice_code || '-'}</div>
                        <div class="meta">Product: ${item.product_name || '-'}</div>
                        <div class="meta">Product ID: ${item.product_id || '-'}</div>
                        <div class="meta">Vendor: ${vendorName}</div>
                        <div class="meta">Total Box: ${item.target_box_count || 0}</div>
                        <div id="printQrCode"></div>
                        <div class="value">${barcodeValue}</div>
                    </div>
                    <script>
                        new QRCode(document.getElementById('printQrCode'), {
                            text: ${JSON.stringify(barcodeValue)},
                            width: 280,
                            height: 280,
                            colorDark: '#173f9b',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        window.onload = () => window.print();
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        logActivity({
            action: 'VIEW_INVOICE_BARCODE_PAGE',
            table_name: 'pages',
            description: 'Opened print invoice barcodes page',
        });
        loadInvoices();
    </script>
</body>
</html>
