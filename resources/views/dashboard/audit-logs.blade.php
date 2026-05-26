<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Audit Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'audit-logs'])

    <div class="flex-grow flex min-h-screen flex-col">
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center">
            <h1 class="text-[#0033ab] text-xl font-bold">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button data-open-mobile-sidebar class="p-2 text-gray-600"><i data-lucide="menu"></i></button>
            </div>
        </header>

        <main class="flex-grow p-4 md:p-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-[#001a4d]">Audit Logs</h2>
                        <p class="mt-1 text-sm font-medium text-gray-500">Track login, invoice, scan, vendor, shipment, and other system actions.</p>
                    </div>
                    <div class="rounded-2xl border border-blue-100 bg-white px-4 py-3 text-sm font-semibold text-gray-500 shadow-sm">
                        <span id="logCount">0 records</span>
                    </div>
                </div>

                <section class="rounded-3xl border border-blue-50 bg-white p-5 shadow-sm md:p-6">
                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label for="actionFilter" class="mb-2 block text-sm font-semibold text-gray-600">Action</label>
                            <input id="actionFilter" type="text" placeholder="LOGIN, CREATE_INVOICE..."
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="tableFilter" class="mb-2 block text-sm font-semibold text-gray-600">Table</label>
                            <input id="tableFilter" type="text" placeholder="invoices, users..."
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="userFilter" class="mb-2 block text-sm font-semibold text-gray-600">User ID</label>
                            <input id="userFilter" type="number" min="1" placeholder="1"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end gap-3">
                            <button id="applyFilters" type="button" class="w-full rounded-xl bg-[#0033ab] px-4 py-3 font-bold text-white transition hover:bg-blue-800">
                                Apply
                            </button>
                            <button id="resetFilters" type="button" class="w-full rounded-xl border border-blue-200 px-4 py-3 font-bold text-[#0033ab] transition hover:bg-blue-50">
                                Reset
                            </button>
                        </div>
                    </div>
                </section>

                <p id="pageMessage" class="hidden rounded-xl border px-4 py-3 text-sm font-medium"></p>

                <section class="overflow-hidden rounded-3xl border border-blue-50 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-blue-100">
                            <thead class="bg-[#f8fbff]">
                                <tr class="text-left text-xs font-black uppercase tracking-wider text-gray-500">
                                    <th class="px-5 py-4">Time</th>
                                    <th class="px-5 py-4">Action</th>
                                    <th class="px-5 py-4">Table</th>
                                    <th class="px-5 py-4">User</th>
                                    <th class="px-5 py-4">Description</th>
                                </tr>
                            </thead>
                            <tbody id="logsTableBody" class="divide-y divide-blue-50 bg-white">
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm font-medium text-gray-400">Loading audit logs...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="flex flex-col gap-3 rounded-2xl border border-blue-50 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <p id="paginationSummary" class="text-sm font-medium text-gray-500">Page 1</p>
                    <div class="flex gap-3">
                        <button id="previousPage" type="button" class="rounded-xl border border-blue-200 px-4 py-2 font-bold text-[#0033ab] transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50">
                            Previous
                        </button>
                        <button id="nextPage" type="button" class="rounded-xl border border-blue-200 px-4 py-2 font-bold text-[#0033ab] transition hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        const logsApiUrl = '/audit-logs/data';
        const activityLogUrl = '/activity-log';
        const pageMessage = document.getElementById('pageMessage');
        const logsTableBody = document.getElementById('logsTableBody');
        const paginationSummary = document.getElementById('paginationSummary');
        const logCount = document.getElementById('logCount');
        const actionFilter = document.getElementById('actionFilter');
        const tableFilter = document.getElementById('tableFilter');
        const userFilter = document.getElementById('userFilter');
        const previousPageButton = document.getElementById('previousPage');
        const nextPageButton = document.getElementById('nextPage');

        let currentPage = 1;
        let lastPage = 1;

        function setPageMessage(text, tone = 'error') {
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

        function formatTimestamp(value) {
            if (!value) {
                return '-';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleString('en-GB', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        }

        function logActivity(payload) {
            fetch(activityLogUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            }).catch(() => {});
        }

        function renderLogs(items) {
            if (!Array.isArray(items) || items.length === 0) {
                logsTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm font-medium text-gray-400">No audit logs found for the selected filters.</td>
                    </tr>
                `;
                return;
            }

            logsTableBody.innerHTML = items.map((item) => `
                <tr class="align-top">
                    <td class="px-5 py-4 text-sm font-medium text-gray-500">${formatTimestamp(item.created_at)}</td>
                    <td class="px-5 py-4">
                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-[#0033ab]">
                            ${item.action ?? '-'}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold text-gray-700">${item.table_name ?? '-'}</td>
                    <td class="px-5 py-4 text-sm text-gray-600">
                        <div class="font-semibold text-gray-800">${item.user?.username ?? 'System'}</div>
                        <div class="text-xs text-gray-400">ID: ${item.user_id ?? '-'}</div>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600">${item.description ?? '-'}</td>
                </tr>
            `).join('');
        }

        async function loadAuditLogs(page = 1) {
            setPageMessage('');
            logsTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-sm font-medium text-gray-400">Loading audit logs...</td>
                </tr>
            `;

            const params = new URLSearchParams({
                page: String(page),
                per_page: '20',
            });

            if (actionFilter.value.trim() !== '') {
                params.set('action', actionFilter.value.trim());
            }

            if (tableFilter.value.trim() !== '') {
                params.set('table_name', tableFilter.value.trim());
            }

            if (userFilter.value.trim() !== '') {
                params.set('user_id', userFilter.value.trim());
            }

            try {
                const response = await fetch(`${logsApiUrl}?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const payload = await response.json();

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (response.status === 403) {
                    setPageMessage(payload.message || 'You are not allowed to view audit logs.');
                    renderLogs([]);
                    return;
                }

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to load audit logs');
                }

                const items = payload.data?.items || [];
                const pagination = payload.data?.pagination || {};

                currentPage = Number(pagination.current_page || 1);
                lastPage = Number(pagination.last_page || 1);

                renderLogs(items);
                logCount.textContent = `${pagination.total || items.length} records`;
                paginationSummary.textContent = `Page ${currentPage} of ${lastPage}`;
                previousPageButton.disabled = currentPage <= 1;
                nextPageButton.disabled = currentPage >= lastPage;
            } catch (error) {
                setPageMessage(error.message || 'Failed to load audit logs');
                renderLogs([]);
            }
        }

        document.getElementById('applyFilters').addEventListener('click', () => loadAuditLogs(1));
        document.getElementById('resetFilters').addEventListener('click', () => {
            actionFilter.value = '';
            tableFilter.value = '';
            userFilter.value = '';
            loadAuditLogs(1);
        });
        previousPageButton.addEventListener('click', () => {
            if (currentPage > 1) {
                loadAuditLogs(currentPage - 1);
            }
        });
        nextPageButton.addEventListener('click', () => {
            if (currentPage < lastPage) {
                loadAuditLogs(currentPage + 1);
            }
        });

        logActivity({
            action: 'VIEW_AUDIT_LOG_PAGE',
            table_name: 'pages',
            description: 'Opened audit logs page',
        });
        loadAuditLogs();
    </script>
</body>
</html>
