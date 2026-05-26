<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js for responsive reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'home'])

    <!-- MAIN CONTENT -->
    <div class="flex-grow flex min-h-screen flex-col">
        
        <!-- HEADER -->
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center z-10">
            <h1 class="text-[#0033ab] text-xl font-bold tracking-tight">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="mobileMenuBtn" data-open-mobile-sidebar class="p-2 text-gray-600"><i data-lucide="menu"></i></button>
            </div>
        </header>

        <!-- DASHBOARD AREA -->
        <main class="flex-grow overflow-y-auto p-6 lg:p-10 space-y-8">
            
            <!-- DAILY REPORT SECTION -->
            <section class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                    <div class="flex items-center gap-3 text-[#0033ab]">
                        <i data-lucide="layout-grid" class="w-6 h-6"></i>
                        <h2 class="text-2xl font-bold">Daily Report</h2>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3">
                        <div class="flex rounded-lg overflow-hidden border border-blue-200 shadow-sm">
                            <span class="bg-[#3b82f6] text-white px-3 py-2 text-sm font-bold">Date</span>
                            <input type="date" id="dateFilter" class="px-4 py-2 text-sm focus:outline-none w-40 bg-white">
                            <button id="openDatePicker" type="button" class="bg-[#3b82f6] text-white px-3 py-2"><i data-lucide="calendar" class="w-4 h-4"></i></button>
                        </div>
                        <div class="flex rounded-lg overflow-hidden border border-blue-200 shadow-sm">
                            <span class="bg-[#3b82f6] text-white px-3 py-2 text-sm font-bold">Vendor</span>
                            <select id="vendorFilter" class="px-4 py-2 text-sm focus:outline-none appearance-none pr-8 bg-white min-w-40">
                                <option value="">Loading vendors...</option>
                            </select>
                            <button id="applyFilters" type="button" class="bg-[#3b82f6] text-white px-3 py-2"><i data-lucide="chevron-down" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>

                <p id="dashboardMessage" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm font-medium"></p>

                <!-- Daily Stat Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="dailyStats">
                    <!-- Total Boxes -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 relative overflow-hidden">
                        <div class="flex items-center gap-2 text-[#0033ab] mb-4">
                            <i data-lucide="archive" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Total Boxes</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-total">50 Boxes</p>
                    </div>
                    <!-- Less -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 border-l-4 border-l-red-400">
                        <div class="flex items-center gap-2 text-red-500 mb-4">
                            <i data-lucide="minus-circle" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Less</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-less">5 Boxes</p>
                        <p class="text-sm text-gray-400 font-medium" id="stat-less-rate">10% from total boxes</p>
                    </div>
                    <!-- Match -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 border-l-4 border-l-green-400">
                        <div class="flex items-center gap-2 text-green-500 mb-4">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Match</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-match">40 Boxes</p>
                        <p class="text-sm text-gray-400 font-medium" id="stat-match-rate">80% from total boxes</p>
                    </div>
                    <!-- Mismatch -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 border-l-4 border-l-red-400">
                        <div class="flex items-center gap-2 text-red-400 mb-4">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Mismatch</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-mismatch">5 Boxes</p>
                        <p class="text-sm text-gray-400 font-medium" id="stat-mismatch-rate">10% from total boxes</p>
                    </div>
                    <!-- Pending -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 border-l-4 border-l-yellow-400">
                        <div class="flex items-center gap-2 text-yellow-500 mb-4">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Pending</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-pending">0 Boxes</p>
                        <p class="text-sm text-gray-400 font-medium" id="stat-pending-rate">0% from total boxes</p>
                    </div>
                    <!-- Over -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 border-l-4 border-l-red-400">
                        <div class="flex items-center gap-2 text-red-500 mb-4">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            <span class="font-bold uppercase text-xs tracking-widest">Over</span>
                        </div>
                        <p class="text-3xl font-black text-gray-800" id="stat-over">0 Boxes</p>
                        <p class="text-sm text-gray-400 font-medium" id="stat-over-rate">0% from total boxes</p>
                    </div>
                </div>
            </section>

            <!-- MONTHLY REPORT SECTION -->
            <section class="max-w-7xl mx-auto pb-10">
                <div class="flex items-center gap-3 text-[#0033ab] mb-6">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                    <h2 class="text-2xl font-bold">Monthly Report</h2>
                </div>

                <!-- Charts Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Main Summary Chart -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50 col-span-1 lg:col-span-2">
                        <h3 class="font-bold text-lg mb-4" id="summaryChartTitle">Status Breakdown</h3>
                        <div class="h-64">
                            <canvas id="allVendorsChart"></canvas>
                        </div>
                    </div>
                    <!-- Individual Vendor Charts -->
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50">
                        <h3 class="font-bold text-gray-600 mb-4" id="vendorChartTitle">Vendor Breakdown</h3>
                        <div class="h-48">
                            <canvas id="vendorAChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-50">
                        <h3 class="font-bold text-gray-600 mb-4">Target vs Actual</h3>
                        <div class="h-48">
                            <canvas id="vendorBChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="mt-auto shrink-0 bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
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

        const dashboardApiUrl = '/home/data';
        const activityLogUrl = '/activity-log';
        const vendorOptionsUrl = '/vendors/options';
        const dashboardMessage = document.getElementById('dashboardMessage');
        const dateFilter = document.getElementById('dateFilter');
        const vendorFilter = document.getElementById('vendorFilter');
        const openDatePickerButton = document.getElementById('openDatePicker');
        const applyFiltersButton = document.getElementById('applyFilters');
        const statusKeys = ['less', 'match', 'mismatch', 'pending', 'over'];
        const statusLabels = ['Less', 'Match', 'Mismatch', 'Pending', 'Over'];

        function setDashboardMessage(text, tone = 'error') {
            if (!text) {
                dashboardMessage.className = 'hidden mb-4 rounded-xl border px-4 py-3 text-sm font-medium';
                dashboardMessage.textContent = '';
                return;
            }

            const toneClasses = tone === 'info'
                ? 'border-blue-200 bg-blue-50 text-blue-700'
                : 'border-red-200 bg-red-50 text-red-600';

            dashboardMessage.className = `mb-4 rounded-xl border px-4 py-3 text-sm font-medium ${toneClasses}`;
            dashboardMessage.textContent = text;
        }

        function getTodayDateString() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatRate(count, total) {
            if (total === 0) {
                return '0% from total boxes';
            }

            return `${Math.round((count / total) * 100)}% from total boxes`;
        }

        function updateStat(id, count, total) {
            document.getElementById(`stat-${id}`).textContent = `${count} Boxes`;
            document.getElementById(`stat-${id}-rate`).textContent = formatRate(count, total);
        }

        function logActivity(payload) {
            fetch(activityLogUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            }).catch(() => {});
        }

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 6, font: { size: 10 } } } },
            scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } }
        };

        const summaryChart = new Chart(document.getElementById('allVendorsChart'), {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [
                    {
                        label: 'Boxes',
                        data: [0, 0, 0, 0, 0],
                        backgroundColor: ['#f87171', '#10b981', '#f43f5e', '#facc15', '#fb7185']
                    }
                ]
            },
            options: commonOptions
        });

        const vendorBreakdownChart = new Chart(document.getElementById('vendorAChart'), {
            type: 'doughnut',
            data: {
                labels: ['No Data'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#dbeafe']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const targetChart = new Chart(document.getElementById('vendorBChart'), {
            type: 'doughnut',
            data: {
                labels: ['Actual Boxes', 'Remaining to Target'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: ['#2563eb', '#dbeafe']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        function updateCharts(data) {
            const statusCounts = data.status_counts || {};
            summaryChart.data.datasets[0].data = statusKeys.map((key) => statusCounts[key] || 0);
            summaryChart.update();

            const vendorBreakdown = Array.isArray(data.vendor_breakdown) ? data.vendor_breakdown : [];
            if (vendorBreakdown.length > 0) {
                vendorBreakdownChart.data.labels = vendorBreakdown.map((item) => item.vendor_name);
                vendorBreakdownChart.data.datasets[0].data = vendorBreakdown.map((item) => item.total_boxes);
                vendorBreakdownChart.data.datasets[0].backgroundColor = ['#2563eb', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'];
            } else {
                vendorBreakdownChart.data.labels = ['No Data'];
                vendorBreakdownChart.data.datasets[0].data = [1];
                vendorBreakdownChart.data.datasets[0].backgroundColor = ['#dbeafe'];
            }
            vendorBreakdownChart.update();

            const totalBoxes = Number(data.total_boxes || 0);
            const targetBoxes = Number(data.target_boxes || 0);
            const remaining = Math.max(targetBoxes - totalBoxes, 0);
            targetChart.data.datasets[0].data = targetBoxes > 0 ? [totalBoxes, remaining] : [totalBoxes || 1, 0];
            targetChart.update();
        }

        function updateDashboardUI(payload) {
            const data = payload.data || {};
            const totalBoxes = Number(data.total_boxes || 0);
            const statusCounts = data.status_counts || {};

            document.getElementById('stat-total').textContent = `${totalBoxes} Boxes`;
            updateStat('less', Number(statusCounts.less || 0), totalBoxes);
            updateStat('match', Number(statusCounts.match || 0), totalBoxes);
            updateStat('mismatch', Number(statusCounts.mismatch || 0), totalBoxes);
            updateStat('pending', Number(statusCounts.pending || 0), totalBoxes);
            updateStat('over', Number(statusCounts.over || 0), totalBoxes);

            document.getElementById('summaryChartTitle').textContent = `Status Breakdown - ${data.date || dateFilter.value}`;
            document.getElementById('vendorChartTitle').textContent = data.vendor_name
                ? `Vendor Breakdown - ${data.vendor_name}`
                : 'Vendor Breakdown';

            updateCharts(data);
            setDashboardMessage('');
        }

        async function loadVendors() {
            try {
                const response = await fetch(vendorOptionsUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                const payload = await response.json();

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to load vendors');
                }

                const vendors = Array.isArray(payload.data) ? payload.data : [];
                const options = vendors.map((vendor) => `<option value="${vendor.vendor_id}">${vendor.vendor_name}</option>`).join('');
                const includeAll = vendors.length > 1 ? '<option value="">All Vendors</option>' : '';
                vendorFilter.innerHTML = `${includeAll}${options}`;
            } catch (error) {
                setDashboardMessage(error.message || 'Failed to load vendors');
            }
        }

        async function loadDashboard() {
            setDashboardMessage('');

            const params = new URLSearchParams({
                date: dateFilter.value || getTodayDateString()
            });

            if (vendorFilter.value) {
                params.set('vendor_id', vendorFilter.value);
            }

            try {
                const response = await fetch(`${dashboardApiUrl}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                const payload = await response.json();

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to load dashboard');
                }

                updateDashboardUI(payload);
            } catch (error) {
                setDashboardMessage(error.message || 'Failed to load dashboard');
            }
        }

        openDatePickerButton.addEventListener('click', () => {
            if (typeof dateFilter.showPicker === 'function') {
                dateFilter.showPicker();
                return;
            }

            dateFilter.focus();
            dateFilter.click();
        });

        applyFiltersButton.addEventListener('click', loadDashboard);
        dateFilter.addEventListener('change', loadDashboard);
        vendorFilter.addEventListener('change', loadDashboard);

        dateFilter.value = getTodayDateString();
        logActivity({
            action: 'VIEW_DASHBOARD_PAGE',
            table_name: 'pages',
            description: 'Opened dashboard page',
        });
        loadVendors().then(loadDashboard);
    </script>
</body>
</html>
