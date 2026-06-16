<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - User Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">
    @include('dashboard.partials.sidebar', ['activePage' => 'users'])

    <div class="flex min-h-screen flex-grow flex-col">
        <header class="z-10 flex items-center justify-between bg-white px-8 py-4 shadow-sm">
            <h1 class="text-xl font-bold tracking-tight text-[#0033ab]">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button type="button" data-open-mobile-sidebar class="p-2 text-gray-600">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-grow space-y-6 p-4 md:p-8">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3 text-[#0033ab]">
                    <i data-lucide="users" class="h-7 w-7"></i>
                    <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
                </div>
                <a
                    href="/users/create"
                    class="flex items-center gap-2 rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800"
                >
                    <i data-lucide="user-plus" class="h-5 w-5"></i>
                    Add User
                </a>
            </div>

            <div id="alertBanner" class="hidden rounded-xl border px-4 py-3 text-sm font-medium"></div>

            <div class="overflow-hidden rounded-3xl border border-blue-50 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 lg:flex-row lg:items-center">
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
                        <input
                            id="searchInput"
                            type="text"
                            placeholder="Search username or email..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]"
                        >
                    </div>
                    <select
                        id="roleFilter"
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]"
                    >
                        <option value="">All roles</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="SUPERVISOR">SUPERVISOR</option>
                        <option value="PETUGAS_GUDANG">PETUGAS GUDANG</option>
                        <option value="VENDOR">VENDOR</option>
                    </select>
                    <button
                        id="searchBtn"
                        class="flex items-center justify-center gap-2 rounded-xl bg-blue-50 px-5 py-3 text-sm font-bold text-[#0033ab] transition hover:bg-blue-100"
                    >
                        <i data-lucide="filter" class="h-4 w-4"></i>
                        Filter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#f8faff] text-xs font-black uppercase tracking-widest text-gray-400">
                            <tr>
                                <th class="w-16 px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">User</th>
                                <th class="px-6 py-4 text-left">Role</th>
                                <th class="px-6 py-4 text-left">Vendor</th>
                                <th class="w-52 px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-gray-50">
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center font-medium text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="loader-2" class="h-6 w-6 animate-spin text-blue-400"></i>
                                        Loading users...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 px-6 py-4 text-sm font-medium text-gray-500 sm:flex-row">
                    <span id="paginationInfo"></span>
                    <div id="paginationControls" class="flex gap-2"></div>
                </div>
            </div>
        </main>

        <footer class="mt-auto bg-[#001a4d] p-4 text-[10px] text-white md:text-xs">
            <div class="mx-auto flex max-w-7xl flex-col justify-between opacity-80 md:flex-row">
                <p>EPSON SVS - User Management</p>
                <p>App Version: 1.2.0-stable</p>
            </div>
        </footer>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i data-lucide="trash-2" class="h-8 w-8"></i>
            </div>
            <h3 class="text-xl font-black text-gray-800">Delete User?</h3>
            <p class="mt-2 text-sm font-medium text-gray-400">
                Are you sure you want to delete <span id="deleteUserName" class="font-black text-gray-700"></span>?
            </p>
            <input type="hidden" id="deleteUserId">
            <div class="mt-6 flex gap-3">
                <button id="cancelDeleteBtn" class="flex-1 rounded-xl border-2 border-gray-200 py-3 font-bold text-gray-500 transition hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" class="flex-1 rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg shadow-red-100 transition hover:bg-red-600">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const tableBody = document.getElementById('userTableBody');
        const alertBanner = document.getElementById('alertBanner');
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        let currentPage = 1;
        let currentSearch = '';
        let currentRole = '';

        function showAlert(message, type = 'error') {
            const isError = type === 'error';
            alertBanner.className = `rounded-xl border px-4 py-3 text-sm font-medium ${
                isError
                    ? 'border-red-200 bg-red-50 text-red-600'
                    : 'border-green-200 bg-green-50 text-green-700'
            }`;
            alertBanner.textContent = message;
            alertBanner.classList.remove('hidden');
            setTimeout(() => alertBanner.classList.add('hidden'), 4000);
        }

        function openModal(id) {
            const element = document.getElementById(id);
            element.classList.remove('hidden');
            element.classList.add('flex');
        }

        function closeModal(id) {
            const element = document.getElementById(id);
            element.classList.add('hidden');
            element.classList.remove('flex');
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function loadUsers(page = 1, search = '', role = '') {
            currentPage = page;
            currentSearch = search;
            currentRole = role;

            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center font-medium text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="loader-2" class="h-6 w-6 animate-spin text-blue-400"></i>
                            Loading users...
                        </div>
                    </td>
                </tr>
            `;
            lucide.createIcons();

            try {
                const params = new URLSearchParams({ page, per_page: 20 });
                if (search) {
                    params.set('search', search);
                }
                if (role) {
                    params.set('role', role);
                }

                const response = await fetch(`/users/data?${params.toString()}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to load users');
                }

                renderTable(payload.data);
            } catch (error) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center font-medium text-red-400">
                            ${escapeHtml(error.message || 'Failed to load users')}
                        </td>
                    </tr>
                `;
            }
        }

        function renderTable(data) {
            const items = data?.items ?? [];
            const meta = data?.pagination ?? {};

            if (items.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center font-medium text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="users" class="h-8 w-8 text-gray-300"></i>
                                No users found.
                            </div>
                        </td>
                    </tr>
                `;
                paginationInfo.textContent = '';
                paginationControls.innerHTML = '';
                lucide.createIcons();
                return;
            }

            tableBody.innerHTML = items.map((user, index) => {
                const rowNumber = ((meta.current_page - 1) * 20) + index + 1;
                return `
                    <tr class="group transition hover:bg-blue-50/40">
                        <td class="px-6 py-4 text-xs font-bold text-gray-400">${rowNumber}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0033ab]">
                                    <i data-lucide="user-round" class="h-4 w-4"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">${escapeHtml(user.username)}</p>
                                    <p class="text-xs text-gray-400">${escapeHtml(user.email)}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0033ab]">
                                ${escapeHtml(user.role)}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-500">
                            ${escapeHtml(user.vendor_name || '-')}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="/users/${user.user_id}" class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold text-gray-600 transition hover:bg-gray-50">
                                    View
                                </a>
                                <a href="/users/${user.user_id}/edit" class="rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-bold text-[#0033ab] transition hover:bg-blue-100">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    class="delete-btn rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-bold text-red-500 transition hover:bg-red-100"
                                    data-id="${user.user_id}"
                                    data-name="${escapeHtml(user.username)}"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            const from = items.length === 0 ? 0 : ((meta.current_page - 1) * meta.per_page) + 1;
            const to = ((meta.current_page - 1) * meta.per_page) + items.length;

            paginationInfo.textContent = `Showing ${from}-${to} of ${meta.total} users`;
            renderPagination(meta.current_page, meta.last_page);
            lucide.createIcons();

            tableBody.querySelectorAll('.delete-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('deleteUserId').value = button.dataset.id;
                    document.getElementById('deleteUserName').textContent = button.dataset.name;
                    openModal('deleteModal');
                });
            });
        }

        function renderPagination(current, last) {
            if (last <= 1) {
                paginationControls.innerHTML = '';
                return;
            }

            const buttonClass = 'rounded-xl border px-4 py-2 text-sm font-bold transition';
            let html = '';

            if (current > 1) {
                html += `<button class="${buttonClass} border-gray-200 text-gray-600 hover:bg-gray-50" data-page="${current - 1}">Prev</button>`;
            }

            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);

            for (let page = start; page <= end; page += 1) {
                const active = page === current;
                html += `<button class="${buttonClass} ${active ? 'border-[#0033ab] bg-[#0033ab] text-white shadow-lg shadow-blue-100' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}" data-page="${page}">${page}</button>`;
            }

            if (current < last) {
                html += `<button class="${buttonClass} border-gray-200 text-gray-600 hover:bg-gray-50" data-page="${current + 1}">Next</button>`;
            }

            paginationControls.innerHTML = html;
            paginationControls.querySelectorAll('button[data-page]').forEach((button) => {
                button.addEventListener('click', () => {
                    loadUsers(Number(button.dataset.page), currentSearch, currentRole);
                });
            });
        }

        document.getElementById('searchBtn').addEventListener('click', () => {
            loadUsers(1, searchInput.value.trim(), roleFilter.value);
        });

        searchInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                loadUsers(1, searchInput.value.trim(), roleFilter.value);
            }
        });

        roleFilter.addEventListener('change', () => {
            loadUsers(1, searchInput.value.trim(), roleFilter.value);
        });

        document.getElementById('cancelDeleteBtn').addEventListener('click', () => closeModal('deleteModal'));
        document.getElementById('deleteModal').addEventListener('click', (event) => {
            if (event.target === event.currentTarget) {
                closeModal('deleteModal');
            }
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            const button = document.getElementById('confirmDeleteBtn');
            const userId = document.getElementById('deleteUserId').value;
            const userName = document.getElementById('deleteUserName').textContent;

            button.disabled = true;
            button.textContent = 'Deleting...';

            try {
                const response = await fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                });
                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to delete user');
                }

                closeModal('deleteModal');
                showAlert(`User "${userName}" deleted successfully.`, 'success');
                loadUsers(currentPage, currentSearch, currentRole);
            } catch (error) {
                closeModal('deleteModal');
                showAlert(error.message || 'Failed to delete user');
            } finally {
                button.disabled = false;
                button.textContent = 'Delete';
            }
        });

        loadUsers();
    </script>
</body>
</html>
