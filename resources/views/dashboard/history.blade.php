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
                        <input type="text" id="searchInput" placeholder="Search SKU or Item Name" 
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
                const quantityLabel = `${item.quantity} pcs`;
                const locationLabel = item.location || 'No location recorded';
                const statusTone = item.status === 'MATCH'
                    ? 'bg-green-100 text-green-700'
                    : item.status === 'MISMATCH'
                        ? 'bg-red-100 text-red-700'
                        : 'bg-yellow-100 text-yellow-700';
                const card = `
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center gap-6 hover:shadow-md transition">
                        <!-- Icon Square -->
                        <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center text-[#0033ab] shrink-0">
                            <i data-lucide="package" class="w-8 h-8"></i>
                        </div>

                        <!-- Info Area -->
                        <div class="flex-grow">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SKU: ${item.sku}</span>
                            <h3 class="text-xl font-black text-gray-800 leading-tight">${item.item_name}</h3>
                            
                            <div class="flex flex-wrap items-center gap-x-8 gap-y-2 mt-4">
                                <div class="flex items-center gap-10">
                                    <span class="text-sm font-bold text-gray-400">Quantity</span>
                                    <span class="bg-blue-100 text-[#0033ab] px-4 py-1 rounded-full text-sm font-black">${quantityLabel}</span>
                                </div>
                                <div class="flex items-center gap-10">
                                    <span class="text-sm font-bold text-gray-400">Location</span>
                                    <div class="flex items-center gap-1 text-gray-600 font-bold">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        <span>${locationLabel}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-400">Status</span>
                                    <span class="px-4 py-1 rounded-full text-sm font-black ${statusTone}">${item.status}</span>
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
        fetchHistory();
    </script>
</body>
</html>
