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
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nomor PO / Surat Jalan</label>
                        <input type="text" id="po_number" required placeholder="Contoh: PO-2023-001" 
                               class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Target Jumlah Box</label>
                            <input type="number" id="box_count" required placeholder="0" 
                                   class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Estimasi Kedatangan</label>
                            <input type="date" id="arrival_date" required 
                                   class="w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-[#0033ab]">Manual Data Entry</h3>
                                <p class="text-sm text-gray-500">Masukkan data box dan item secara manual seperti referensi form.</p>
                            </div>
                            <button type="button" id="addManualRow" class="shrink-0 rounded-xl border border-blue-200 px-4 py-2 text-sm font-semibold text-[#0033ab] transition hover:bg-blue-50">
                                + Add Row
                            </button>
                        </div>

                        <div id="manualRows" class="mt-4 space-y-3">
                            <div class="manual-entry-row grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-[#f8fbff] p-4 md:grid-cols-2">
                                <input type="text" name="box_id[]" placeholder="BOX ID" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="text" name="item_name[]" placeholder="Item Name" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="number" name="quantity[]" placeholder="Quantity" min="0" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <div class="flex gap-3">
                                    <div class="vendor-select-slot w-full" data-selected-value=""></div>
                                    <button type="button" class="remove-row hidden rounded-xl border border-red-200 px-4 text-sm font-semibold text-red-500 transition hover:bg-red-50">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPLOAD AREA -->
                    <div class="border-2 border-dashed border-blue-200 bg-blue-50 rounded-2xl p-8 text-center cursor-pointer hover:bg-blue-100 transition" 
                         onclick="document.getElementById('fileInput').click()">
                        <input type="file" id="fileInput" class="hidden" accept=".csv, .xlsx">
                        <i data-lucide="file-up" class="w-12 h-12 mx-auto text-[#0033ab] mb-2"></i>
                        <p class="text-blue-800 font-bold">Upload Manifest File (Excel/CSV)</p>
                        <p class="text-xs text-gray-400">Max file size 5MB</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-3">
                        <i data-lucide="info" class="text-blue-600 shrink-0"></i>
                        <p class="text-xs text-blue-900">Pastikan nomor PO sesuai dengan dokumen fisik untuk menghindari penolakan saat unloading di dermaga.</p>
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

                <div class="bg-white rounded-3xl shadow-sm border border-blue-50 overflow-hidden text-left">
                    <div class="bg-blue-50/50 p-6 border-b border-blue-100">
                        <h3 class="font-bold text-[#0033ab] uppercase tracking-wider">Ringkasan Invoice</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div class="p-4 flex justify-between">
                            <span class="text-gray-500">Manual Entry</span>
                            <span id="res-manual-count" class="font-bold">1 baris</span>
                        </div>
                        <div class="p-4 flex justify-between">
                            <span class="text-gray-500">Nomor PO</span>
                            <span id="res-po" class="font-bold">PO-2023-001</span>
                        </div>
                        <div class="p-4 flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">TERVERIFIKASI</span>
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
        const manualRows = document.getElementById('manualRows');
        const addManualRowButton = document.getElementById('addManualRow');
        const formError = document.getElementById('formError');
        const vendorOptionsUrl = '/vendors/options';
        let vendorOptions = [];

        function updateRemoveButtons() {
            const rows = manualRows.querySelectorAll('.manual-entry-row');

            rows.forEach((row, index) => {
                const removeButton = row.querySelector('.remove-row');
                removeButton.classList.toggle('hidden', rows.length === 1);
                removeButton.disabled = rows.length === 1;
                removeButton.dataset.rowIndex = index;
            });
        }

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
            manualRows.querySelectorAll('.vendor-select-slot').forEach((slot) => {
                const selectedValue = slot.dataset.selectedValue || '';
                slot.innerHTML = buildVendorSelect(selectedValue);
            });
        }

        function createManualRow() {
            const row = document.createElement('div');
            row.className = 'manual-entry-row grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-[#f8fbff] p-4 md:grid-cols-2';
            row.innerHTML = `
                <input type="text" name="box_id[]" placeholder="BOX ID" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" name="item_name[]" placeholder="Item Name" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" name="quantity[]" placeholder="Quantity" min="0" class="w-full rounded-xl border border-gray-200 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-3">
                    <div class="vendor-select-slot w-full" data-selected-value=""></div>
                    <button type="button" class="remove-row rounded-xl border border-red-200 px-4 text-sm font-semibold text-red-500 transition hover:bg-red-50">
                        Remove
                    </button>
                </div>
            `;

            const slot = row.querySelector('.vendor-select-slot');
            slot.innerHTML = buildVendorSelect();
            return row;
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

        addManualRowButton.addEventListener('click', () => {
            manualRows.appendChild(createManualRow());
            updateRemoveButtons();
        });

        manualRows.addEventListener('click', (event) => {
            const removeButton = event.target.closest('.remove-row');

            if (!removeButton) {
                return;
            }

            removeButton.closest('.manual-entry-row')?.remove();
            updateRemoveButtons();
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            formError.classList.add('hidden');
            formError.textContent = '';

            const manualEntries = Array.from(manualRows.querySelectorAll('.manual-entry-row')).map((row) => ({
                box_id: row.querySelector('input[name="box_id[]"]').value.trim(),
                item_name: row.querySelector('input[name="item_name[]"]').value.trim(),
                quantity: Number(row.querySelector('input[name="quantity[]"]').value),
                vendor_name: row.querySelector('select[name="vendor_name[]"]').value.trim()
            }));

            const payload = {
                po_number: document.getElementById('po_number').value.trim(),
                box_count: Number(document.getElementById('box_count').value),
                arrival_date: document.getElementById('arrival_date').value || null,
                manual_entries: manualEntries
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

                document.getElementById('res-po').innerText = document.getElementById('po_number').value;
                document.getElementById('res-manual-count').innerText = `${manualEntries.length} baris`;
                
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

        hydrateVendorSelects();
        updateRemoveButtons();
        loadVendors();
    </script>
</body>
</html>
