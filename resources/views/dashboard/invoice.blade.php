<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Input Invoice</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'invoice'])

    <!-- MAIN CONTENT -->
    <div class="flex-grow flex min-h-screen flex-col">
        
        <!-- HEADER -->
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center">
            <h1 class="text-[#0033ab] text-xl font-bold">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="menuBtn" data-open-mobile-sidebar><i data-lucide="menu"></i></button>
            </div>
        </header>

        <main class="flex-grow p-4 md:p-8">
            <div id="formSection" class="max-w-2xl mx-auto space-y-6">
                <h2 class="text-3xl font-bold text-[#001a4d]">New Invoice Entry</h2>
                
                <form id="invoiceForm" class="space-y-4 bg-white p-8 rounded-3xl shadow-sm border border-blue-50">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Pilih Vendor</label>
                        <div class="vendor-select-slot w-full" data-selected-value=""></div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Product Name</label>
                        <input type="text" id="product_name" required placeholder="Contoh: Printer"
                               class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Invoice ID</label>
                        <input type="text" id="invoice_id" required placeholder="Contoh: inv-0123"
                               class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Product ID</label>
                        <input type="text" id="product_id" required placeholder="Contoh: pd-0123"
                               class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Jumlah Box</label>
                            <input type="number" id="box_count" required min="1" placeholder="0"
                                   class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Estimasi Kedatangan</label>
                            <input type="date" id="arrival_date" required
                                   class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-3">
                        <i data-lucide="info" class="text-blue-600 shrink-0"></i>
                        <p class="text-xs text-blue-900">Pastikan Invoice ID sesuai dengan yang dikirim vendor untuk menghindari penolakan saat unloading di dermaga.</p>
                    </div>

                    <p id="formError" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600"></p>

                    <button type="submit" class="w-full bg-[#0033ab] text-white py-4 rounded-xl font-bold text-xl hover:bg-blue-800 transition shadow-lg">
                        Confirm
                    </button>
                </form>
            </div>

            <!-- SUCCESS SECTION (Hidden by default) -->
            <div id="successSection" class="hidden max-w-2xl mx-auto space-y-6 text-center animate-in fade-in zoom-in duration-300">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="check-circle" class="w-16 h-16 text-green-500"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-[#001a4d]">Invoice Berhasil Diinput</h2>
                    <p class="text-gray-500 max-w-sm mx-auto mt-2">Data invoice telah berhasil disimpan dalam antrean sinkronisasi.</p>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-blue-100 overflow-hidden text-left">
                    <div class="bg-[#f4f1ff] p-6 border-b border-blue-100">
                        <h3 class="font-bold text-[#001a4d] uppercase tracking-wider">Ringkasan Invoice</h3>
                    </div>
                    <div class="divide-y divide-blue-100">
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Vendor</span>
                            <span id="res-vendor" class="font-medium text-gray-900 text-right">Vendor A</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Product Name</span>
                            <span id="res-product-name" class="font-medium text-gray-900 text-right">Printer</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Product ID</span>
                            <span id="res-product-id" class="font-medium text-gray-900 text-right">pd-0123</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Invoice ID</span>
                            <span id="res-invoice" class="font-medium text-gray-900 text-right">inv-0123</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Total Box</span>
                            <span id="res-box-count" class="font-medium text-gray-900 text-right">50</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4">
                            <span class="text-gray-500">Estimasi Kedatangan</span>
                            <span id="res-arrival-date" class="font-medium text-[#001a4d] text-right">01/06/2026</span>
                        </div>
                        <div class="p-4 flex justify-between gap-4 items-center">
                            <span class="text-gray-500">Status</span>
                            <span id="res-status-badge" class="inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-50 px-4 py-1 text-xs font-bold text-orange-500">
                                <i id="res-status-icon" data-lucide="alert-triangle" class="w-4 h-4"></i>
                                <span id="res-status-text">Pending</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <button onclick="window.location.reload()" class="w-full bg-[#0033ab] text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-800 transition">Done</button>
                    <button onclick="toggleSection('form')" class="w-full border-2 border-blue-200 text-blue-500 py-4 rounded-xl font-bold text-lg hover:bg-blue-50 transition">Input Other Invoice</button>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
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

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const form = document.getElementById('invoiceForm');
        const formError = document.getElementById('formError');
        const vendorOptionsUrl = '/vendors/options';
        let vendorOptions = [];

        function buildVendorSelect(selectedValue = '') {
            const defaultLabel = vendorOptions.length === 0 ? 'Loading vendors...' : 'Select Vendor';
            const optionsMarkup = vendorOptions.map((vendor) => `
                <option value="${vendor.vendor_name}" ${vendor.vendor_name === selectedValue ? 'selected' : ''}>
                    ${vendor.vendor_name}
                </option>
            `).join('');

            return `
                <select name="vendor_name[]" class="vendor-select w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" ${vendorOptions.length === 0 ? 'disabled' : ''}>
                    <option value="">${defaultLabel}</option>
                    ${optionsMarkup}
                </select>
            `;
        }

        function hydrateVendorSelects() {
            document.querySelectorAll('.vendor-select-slot').forEach((slot) => {
                const selectedValue = slot.dataset.selectedValue || '';
                slot.innerHTML = buildVendorSelect(selectedValue);
            });
        }

        async function loadVendors() {
            try {
                const response = await fetch(vendorOptionsUrl, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                const data = await response.json();

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to load vendors');
                }

                vendorOptions = Array.isArray(data.data) ? data.data : [];
                hydrateVendorSelects();
            } catch (error) {
                formError.textContent = error.message || 'Failed to load vendors';
                formError.classList.remove('hidden');
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            formError.classList.add('hidden');
            formError.textContent = '';

            const payload = {
                vendor_name: document.querySelector('select[name="vendor_name[]"]').value.trim(),
                product_name: document.getElementById('product_name').value.trim(),
                invoice_id: document.getElementById('invoice_id').value.trim(),
                product_id: document.getElementById('product_id').value.trim(),
                box_count: Number(document.getElementById('box_count').value),
                arrival_date: document.getElementById('arrival_date').value || null
            };

            try {
                const response = await fetch('/invoice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to create invoice');
                }

                const savedStatus = data.data?.status || 'pending';
                document.getElementById('res-vendor').innerText = payload.vendor_name;
                document.getElementById('res-invoice').innerText = payload.invoice_id;
                document.getElementById('res-product-name').innerText = payload.product_name;
                document.getElementById('res-product-id').innerText = payload.product_id;
                document.getElementById('res-box-count').innerText = String(payload.box_count);
                document.getElementById('res-arrival-date').innerText = formatArrivalDate(payload.arrival_date);
                applyStatusBadge(savedStatus);
                
                toggleSection('success');

            } catch (error) {
                formError.textContent = error.message;
                formError.classList.remove('hidden');
            }
        });

        function toggleSection(type) {
            if(type === 'success') {
                document.getElementById('formSection').classList.add('hidden');
                document.getElementById('successSection').classList.remove('hidden');
            } else {
                document.getElementById('formSection').classList.remove('hidden');
                document.getElementById('successSection').classList.add('hidden');
                form.reset();
            }
        }

        function formatArrivalDate(value) {
            if (!value) {
                return '-';
            }

            const [year, month, day] = value.split('-');

            if (!year || !month || !day) {
                return value;
            }

            return `${day}/${month}/${year}`;
        }

        function applyStatusBadge(statusValue) {
            const normalizedStatus = String(statusValue || '').trim().toUpperCase();
            const badge = document.getElementById('res-status-badge');
            const icon = document.getElementById('res-status-icon');
            const text = document.getElementById('res-status-text');

            if (!badge || !icon || !text) {
                return;
            }

            if (normalizedStatus === 'MATCH' || normalizedStatus === 'DONE' || normalizedStatus === 'TERVERIFIKASI') {
                badge.className = 'inline-flex items-center gap-2 rounded-full border border-lime-400 bg-white px-4 py-1 text-xs font-bold text-lime-500';
                icon.setAttribute('data-lucide', 'circle-check');
                text.textContent = normalizedStatus === 'TERVERIFIKASI' ? 'Terverifikasi' : 'Done';
            } else if (normalizedStatus === 'NOT_SCANNED' || normalizedStatus === 'PENDING') {
                badge.className = 'inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-50 px-4 py-1 text-xs font-bold text-orange-500';
                icon.setAttribute('data-lucide', 'alert-triangle');
                text.textContent = normalizedStatus === 'NOT_SCANNED' ? 'Not Scanned' : 'Pending';
            } else if (normalizedStatus === 'MISMATCH' || normalizedStatus === 'OVER' || normalizedStatus === 'LESS') {
                badge.className = 'inline-flex items-center gap-2 rounded-full border border-red-300 bg-red-50 px-4 py-1 text-xs font-bold text-red-600';
                icon.setAttribute('data-lucide', 'circle-alert');
                text.textContent = normalizedStatus.charAt(0) + normalizedStatus.slice(1).toLowerCase();
            } else {
                badge.className = 'inline-flex items-center gap-2 rounded-full border border-gray-300 bg-gray-50 px-4 py-1 text-xs font-bold text-gray-600';
                icon.setAttribute('data-lucide', 'circle-help');
                text.textContent = normalizedStatus || 'Unknown';
            }

            lucide.createIcons();
        }

        hydrateVendorSelects();
        loadVendors();
    </script>
</body>
</html>
