<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'history'])

    <!-- MAIN CONTENT -->
    <div class="flex-grow flex flex-col h-screen overflow-hidden">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center z-10">
            <h1 class="text-[#0033ab] text-xl font-bold tracking-tight">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="mobileMenuBtn" data-open-mobile-sidebar class="p-2 text-gray-600"><i data-lucide="menu"></i></button>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <div class="flex-grow overflow-y-auto p-6 lg:p-10">
            <div class="max-w-4xl mx-auto">
                
                <!-- Search and Filter Row -->
                <div class="flex gap-3 mb-8">
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Search Invoice ID, Product ID, or Product Name" 
                               class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                    </div>
                    <button id="searchButton" class="px-4 py-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
                        <i data-lucide="search" class="w-6 h-6 text-gray-600"></i>
                    </button>
                </div>

                <h2 class="text-3xl font-extrabold text-[#001a4d] mb-6">History</h2>

                <p id="historyMessage" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm font-medium"></p>

                <!-- History List Container -->
                <div id="historyList" class="space-y-4">
                    <!-- Cards will be injected here by JS -->
                    <div class="animate-pulse space-y-4">
                        <div class="h-32 bg-gray-200 rounded-2xl w-full"></div>
                        <div class="h-32 bg-gray-200 rounded-2xl w-full"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- FOOTER -->
        <footer class="bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
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

        const HISTORY_API = "/history/data";
        const ACTIVITY_LOG_URL = "/activity-log";
        const historyList = document.getElementById('historyList');
        const historyMessage = document.getElementById('historyMessage');
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');

        function setMessage(text, tone = 'error') {
            if (!text) {
                historyMessage.className = 'hidden mb-4 rounded-xl border px-4 py-3 text-sm font-medium';
                historyMessage.textContent = '';
                return;
            }

            const toneClasses = tone === 'info'
                ? 'border-blue-200 bg-blue-50 text-blue-700'
                : 'border-red-200 bg-red-50 text-red-600';

            historyMessage.className = `mb-4 rounded-xl border px-4 py-3 text-sm font-medium ${toneClasses}`;
            historyMessage.textContent = text;
        }

        function renderLoading() {
            historyList.innerHTML = `
                <div class="animate-pulse space-y-4">
                    <div class="h-32 bg-gray-200 rounded-2xl w-full"></div>
                    <div class="h-32 bg-gray-200 rounded-2xl w-full"></div>
                </div>
            `;
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

        async function fetchHistory() {
            renderLoading();
            setMessage('');

            const params = new URLSearchParams();
            const search = searchInput.value.trim();

            if (search) {
                params.set('search', search);
            }

            try {
                const response = await fetch(`${HISTORY_API}?${params.toString()}`, {
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
                    throw new Error(data.message || 'Failed to load history');
                }

                renderHistory(data.data?.items || []);
            } catch (error) {
                historyList.innerHTML = '';
                setMessage(error.message || 'Failed to load history');
            }
        }

        function renderHistory(items) {
            historyList.innerHTML = "";

            if (items.length === 0) {
                historyList.innerHTML = `
                    <div class="rounded-2xl border border-blue-100 bg-white p-8 text-center shadow-sm">
                        <p class="text-lg font-bold text-[#001a4d]">No history records found</p>
                        <p class="mt-2 text-sm text-gray-500">Try another SKU or item name.</p>
                    </div>
                `;
                setMessage('No history records found for the current filter.', 'info');
                return;
            }

            items.forEach(item => {
                const quantityLabel = `${item.box_quantity} box`;
                const locationLabel = item.location || 'No location recorded';
                const isNotScanned = item.status === 'NOT_SCANNED';
                const isPending = item.status === 'PENDING';
                const isOnProgress = item.status === 'ON_PROGRESS';
                const isDone = item.status === 'MATCH' || item.status === 'DONE' || item.status === 'TERVERIFIKASI';
                const statusTone = isDone
                    ? 'border border-lime-400 bg-white text-lime-500'
                    : isNotScanned
                        ? 'border border-orange-300 bg-orange-50 text-orange-500'
                        : isOnProgress
                        ? 'border border-blue-400 bg-blue-50 text-blue-600'
                        : isPending
                        ? 'border border-orange-300 bg-orange-50 text-orange-500'
                        : item.status === 'MISMATCH'
                            ? 'border border-red-300 bg-red-50 text-red-700'
                            : item.status === 'LESS' || item.status === 'OVER'
                                ? 'border border-red-300 bg-red-50 text-red-700'
                            : 'bg-yellow-100 text-yellow-700';
                const statusLabel = isDone
                    ? 'Done'
                    : isNotScanned
                        ? 'Not Scanned'
                    : isOnProgress
                        ? 'On Progress'
                    : isPending
                        ? 'Pending'
                    : item.status;
                const statusIcon = isNotScanned || isPending
                    ? '<i data-lucide="alert-triangle" class="w-4 h-4"></i>'
                    : isOnProgress
                        ? '<i data-lucide="scan-line" class="w-4 h-4"></i>'
                    : isDone
                        ? '<i data-lucide="circle-check" class="w-4 h-4"></i>'
                        : item.status === 'LESS' || item.status === 'OVER' || item.status === 'MISMATCH'
                            ? '<i data-lucide="circle-alert" class="w-4 h-4"></i>'
                    : '';
                const card = `
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#f4f2ff] text-[#173a8f]">
                                <i data-lucide="package" class="w-6 h-6"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Product ID: ${item.product_id || '-'}</span>
                                <h3 class="mt-1 text-2xl font-black leading-tight text-gray-800">${item.product_name || '-'}</h3>
                                <p class="mt-1 text-sm font-semibold text-gray-500">Invoice: ${item.invoice_code || '-'}</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-gray-400">Status</span>
                                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1 text-sm font-black ${statusTone}">${statusIcon}<span>${statusLabel}</span></span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-gray-400">Vendor</span>
                                <span class="text-base font-semibold text-gray-700">${item.vendor_name || '-'}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-gray-400">Quantity</span>
                                <span class="rounded-full bg-blue-100 px-4 py-1 text-sm font-black text-[#173a8f]">${quantityLabel}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-bold text-gray-400">Location</span>
                                <div class="flex items-center gap-1 text-base font-semibold text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    <span>${locationLabel}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                historyList.innerHTML += card;
            });

            setMessage('');
            lucide.createIcons();
        }

        searchButton.addEventListener('click', fetchHistory);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                fetchHistory();
            }
        });

        // Initialize
        logActivity({
            action: 'VIEW_HISTORY_PAGE',
            table_name: 'pages',
            description: 'Opened history page',
        });
        fetchHistory();
    </script>
</body>
</html>
