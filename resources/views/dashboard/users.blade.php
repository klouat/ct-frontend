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
        <header class="z-10 flex items-center justify-between bg-white px-4 py-4 shadow-sm md:px-8">
            <h1 class="text-base font-bold tracking-tight text-[#0033ab] md:text-xl">EPSON Smart Verification System (SVS)</h1>
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
                <button
                    type="button"
                    id="openCreateUserBtn"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800 sm:w-auto"
                >
                    <i data-lucide="user-plus" class="h-5 w-5"></i>
                    Add User
                </button>
            </div>

            <div id="alertBanner" class="hidden rounded-xl border px-4 py-3 text-sm font-medium"></div>

            <div class="overflow-hidden rounded-3xl border border-blue-50 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:p-5 lg:flex-row lg:items-center">
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
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab] lg:w-auto"
                    >
                        <option value="">All roles</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="SUPERVISOR">SUPERVISOR</option>
                        <option value="PETUGAS_GUDANG">PETUGAS GUDANG</option>
                        <option value="VENDOR">VENDOR</option>
                    </select>
                    <button
                        id="searchBtn"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-50 px-5 py-3 text-sm font-bold text-[#0033ab] transition hover:bg-blue-100 lg:w-auto"
                    >
                        <i data-lucide="filter" class="h-4 w-4"></i>
                        Filter
                    </button>
                </div>

                <div id="mobileUserCards" class="space-y-3 p-4 sm:hidden">
                    <div class="rounded-2xl border border-dashed border-gray-200 px-4 py-8 text-center text-sm font-medium text-gray-400">
                        Loading users...
                    </div>
                </div>

                <div class="hidden overflow-x-auto sm:block">
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

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 px-3 py-3 backdrop-blur-sm sm:items-center sm:px-4">
        <div class="max-h-[calc(100vh-1.5rem)] w-full max-w-sm overflow-y-auto rounded-3xl bg-white p-6 text-center shadow-2xl sm:p-8">
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

    <div id="viewUserModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 px-3 py-3 backdrop-blur-sm sm:items-center sm:px-4">
        <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl sm:max-h-[90vh]">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-4 py-4 sm:items-center sm:px-6 sm:py-5">
                <div class="flex items-center gap-3 text-[#0033ab]">
                    <i data-lucide="badge-info" class="mt-0.5 h-6 w-6 shrink-0 sm:mt-0"></i>
                    <div>
                        <h3 class="text-xl font-black text-gray-800">User Details</h3>
                        <p class="text-sm font-medium text-gray-400">Review account data without leaving the list.</p>
                    </div>
                </div>
                <button type="button" data-close-modal="viewUserModal" class="rounded-xl p-2 text-gray-400 transition hover:bg-gray-100">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="overflow-y-auto p-4 sm:p-6">
                <div id="viewUserLoading" class="flex flex-col items-center gap-3 py-12 text-gray-400">
                    <i data-lucide="loader-2" class="h-7 w-7 animate-spin text-blue-400"></i>
                    Loading user details...
                </div>
                <div id="viewUserContent" class="hidden space-y-6">
                    <div class="flex items-start gap-4 sm:items-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#0033ab]">
                            <i data-lucide="user-round" class="h-8 w-8"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 id="viewUsername" class="break-words text-xl font-black text-gray-800 sm:text-2xl"></h4>
                            <p id="viewEmail" class="break-all font-medium text-gray-400"></p>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Role</p>
                            <p id="viewRole" class="mt-2 text-lg font-bold text-[#0033ab]"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Vendor</p>
                            <p id="viewVendor" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">User ID</p>
                            <p id="viewUserId" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Created At</p>
                            <p id="viewCreatedAt" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="userFormModal" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 px-3 py-3 backdrop-blur-sm sm:items-center sm:px-4">
        <div class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-4xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl sm:max-h-[90vh]">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-4 py-4 sm:items-center sm:px-6 sm:py-5">
                <div class="flex items-center gap-3 text-[#0033ab]">
                    <i id="userFormIcon" data-lucide="user-plus" class="mt-0.5 h-6 w-6 shrink-0 sm:mt-0"></i>
                    <div>
                        <h3 id="userFormTitle" class="text-xl font-black text-gray-800">Create User</h3>
                        <p id="userFormSubtitle" class="text-sm font-medium text-gray-400">Create a new account with the correct role and access level.</p>
                    </div>
                </div>
                <button type="button" data-close-modal="userFormModal" class="rounded-xl p-2 text-gray-400 transition hover:bg-gray-100">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="overflow-y-auto p-4 sm:p-6 md:p-8">
                <div id="userFormSummary" class="hidden rounded-xl border p-4 text-sm"></div>
                <div id="userFormLoading" class="hidden flex-col items-center gap-3 py-12 text-gray-400">
                    <i data-lucide="loader-2" class="h-7 w-7 animate-spin text-blue-400"></i>
                    Loading user data...
                </div>
                <form id="userForm" class="space-y-6">
                    <input type="hidden" id="formUserId">
                    <div class="grid gap-5 sm:gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Username</label>
                            <input id="username" type="text" maxlength="50" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                            <p id="usernameError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Email</label>
                            <input id="email" type="email" maxlength="150" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                            <p id="emailError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Role</label>
                            <select id="role" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                                <option value="">Select role</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="SUPERVISOR">SUPERVISOR</option>
                                <option value="PETUGAS_GUDANG">PETUGAS GUDANG</option>
                                <option value="VENDOR">VENDOR</option>
                            </select>
                            <p id="roleError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                        <div id="vendorField" class="hidden">
                            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Vendor</label>
                            <select id="vendor_id" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                                <option value="">Select vendor</option>
                            </select>
                            <p id="vendorIdError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                        <div>
                            <label id="passwordLabel" class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Password</label>
                            <div class="relative">
                                <input id="password" type="password" minlength="8" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 pr-14 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                                <button
                                    type="button"
                                    id="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-3 flex items-center text-sm font-bold text-[#0033ab] transition hover:text-blue-800"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    Show
                                </button>
                            </div>
                            <p id="passwordHint" class="mt-2 text-xs text-gray-400">Minimum 8 characters.</p>
                            <p id="passwordError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 border-t border-gray-100 pt-6 sm:flex-row">
                        <button type="button" data-close-modal="userFormModal" class="rounded-xl border-2 border-gray-200 px-5 py-3 text-center font-bold text-gray-500 transition hover:bg-gray-50">
                            Cancel
                        </button>
                        <button id="submitBtn" type="submit" class="rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800">
                            Create User
                        </button>
                    </div>
                </form>
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
        const mobileUserCards = document.getElementById('mobileUserCards');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const userForm = document.getElementById('userForm');
        const userFormSummary = document.getElementById('userFormSummary');
        const userFormLoading = document.getElementById('userFormLoading');
        const userFormState = {
            mode: 'create',
            userId: null,
        };
        const fieldMap = {
            username: document.getElementById('username'),
            email: document.getElementById('email'),
            role: document.getElementById('role'),
            vendor_id: document.getElementById('vendor_id'),
            password: document.getElementById('password'),
        };
        const errorMap = {
            username: document.getElementById('usernameError'),
            email: document.getElementById('emailError'),
            role: document.getElementById('roleError'),
            vendor_id: document.getElementById('vendorIdError'),
            password: document.getElementById('passwordError'),
        };
        const passwordToggleButton = document.getElementById('togglePasswordVisibility');

        let currentPage = 1;
        let currentSearch = '';
        let currentRole = '';
        let vendorOptionsLoaded = false;

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
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(id) {
            const element = document.getElementById(id);
            element.classList.add('hidden');
            element.classList.remove('flex');
            if (document.querySelectorAll('.fixed.inset-0.z-50.flex').length === 0) {
                document.body.classList.remove('overflow-hidden');
            }
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
            mobileUserCards.innerHTML = `
                <div class="rounded-2xl border border-dashed border-gray-200 px-4 py-8 text-center text-sm font-medium text-gray-400">
                    Loading users...
                </div>
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
                mobileUserCards.innerHTML = `
                    <div class="rounded-2xl border border-dashed border-gray-200 px-4 py-8 text-center text-sm font-medium text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="users" class="h-8 w-8 text-gray-300"></i>
                            No users found.
                        </div>
                    </div>
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
                                <button
                                    type="button"
                                    class="view-btn rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold text-gray-600 transition hover:bg-gray-50"
                                    data-id="${user.user_id}"
                                >
                                    View
                                </button>
                                <button
                                    type="button"
                                    class="edit-btn rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-bold text-[#0033ab] transition hover:bg-blue-100"
                                    data-id="${user.user_id}"
                                >
                                    Edit
                                </button>
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

            mobileUserCards.innerHTML = items.map((user) => `
                <div class="rounded-2xl border border-blue-50 bg-[#fbfdff] p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0033ab]">
                                <i data-lucide="user-round" class="h-4 w-4"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-bold text-gray-800">${escapeHtml(user.username)}</p>
                                <p class="truncate text-xs text-gray-400">${escapeHtml(user.email)}</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold text-[#0033ab]">
                            ${escapeHtml(user.role)}
                        </span>
                    </div>
                    <div class="mt-4 rounded-xl bg-white px-3 py-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-black uppercase tracking-widest text-gray-400">Vendor</span>
                            <span class="truncate font-medium text-gray-600">${escapeHtml(user.vendor_name || '-')}</span>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <button
                            type="button"
                            class="view-btn rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold text-gray-600 transition hover:bg-gray-50"
                            data-id="${user.user_id}"
                        >
                            View
                        </button>
                        <button
                            type="button"
                            class="edit-btn rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-bold text-[#0033ab] transition hover:bg-blue-100"
                            data-id="${user.user_id}"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            class="delete-btn rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-bold text-red-500 transition hover:bg-red-100"
                            data-id="${user.user_id}"
                            data-name="${escapeHtml(user.username)}"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            `).join('');

            const from = items.length === 0 ? 0 : ((meta.current_page - 1) * meta.per_page) + 1;
            const to = ((meta.current_page - 1) * meta.per_page) + items.length;

            paginationInfo.textContent = `Showing ${from}-${to} of ${meta.total} users`;
            renderPagination(meta.current_page, meta.last_page);
            lucide.createIcons();

            document.querySelectorAll('.delete-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('deleteUserId').value = button.dataset.id;
                    document.getElementById('deleteUserName').textContent = button.dataset.name;
                    openModal('deleteModal');
                });
            });
            document.querySelectorAll('.view-btn').forEach((button) => {
                button.addEventListener('click', () => openViewUserModal(button.dataset.id));
            });
            document.querySelectorAll('.edit-btn').forEach((button) => {
                button.addEventListener('click', () => openEditUserModal(button.dataset.id));
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

        function clearFormErrors() {
            Object.values(fieldMap).forEach((field) => {
                field.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                field.classList.add('border-gray-200');
            });

            Object.values(errorMap).forEach((errorNode) => {
                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            });

            userFormSummary.className = 'hidden rounded-xl border p-4 text-sm';
            userFormSummary.textContent = '';
        }

        function setFormError(fieldName, message) {
            const field = errorMap[fieldName] ? fieldMap[fieldName] : null;
            const errorNode = errorMap[fieldName];

            if (!field || !errorNode || !message) {
                return;
            }

            field.classList.remove('border-gray-200');
            field.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            errorNode.textContent = message;
            errorNode.classList.remove('hidden');
        }

        function showFormSummary(message, type = 'error') {
            userFormSummary.className = 'rounded-xl border p-4 text-sm';
            if (type === 'success') {
                userFormSummary.classList.add('border-green-200', 'bg-green-50', 'text-green-700');
            } else {
                userFormSummary.classList.add('border-red-200', 'bg-red-50', 'text-red-600');
            }
            userFormSummary.textContent = message;
        }

        function toggleVendorField() {
            const showVendor = fieldMap.role.value === 'VENDOR';
            document.getElementById('vendorField').classList.toggle('hidden', !showVendor);
        }

        function resetUserForm() {
            userForm.reset();
            document.getElementById('formUserId').value = '';
            fieldMap.password.type = 'password';
            passwordToggleButton.textContent = 'Show';
            passwordToggleButton.setAttribute('aria-label', 'Show password');
            passwordToggleButton.setAttribute('aria-pressed', 'false');
            toggleVendorField();
            clearFormErrors();
        }

        function setUserFormMode(mode) {
            userFormState.mode = mode;
            const isEdit = mode === 'edit';

            document.getElementById('userFormTitle').textContent = isEdit ? 'Edit User' : 'Create User';
            document.getElementById('userFormSubtitle').textContent = isEdit
                ? 'Update user account details and password if needed.'
                : 'Create a new account with the correct role and access level.';
            document.getElementById('userFormIcon').setAttribute('data-lucide', isEdit ? 'user-cog' : 'user-plus');
            document.getElementById('passwordLabel').textContent = isEdit ? 'New Password' : 'Password';
            document.getElementById('passwordHint').textContent = isEdit
                ? 'Leave blank to keep the current password.'
                : 'Minimum 8 characters.';
            document.getElementById('submitBtn').textContent = isEdit ? 'Update User' : 'Create User';
            fieldMap.password.required = !isEdit;
            lucide.createIcons();
        }

        async function loadVendorOptions() {
            if (vendorOptionsLoaded) {
                return;
            }

            const response = await fetch('/vendors/options', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Failed to load vendors');
            }

            const vendors = Array.isArray(payload.data) ? payload.data : [];
            fieldMap.vendor_id.innerHTML = `
                <option value="">Select vendor</option>
                ${vendors.map((vendor) => `<option value="${vendor.vendor_id}">${escapeHtml(vendor.vendor_name)}</option>`).join('')}
            `;
            vendorOptionsLoaded = true;
        }

        async function fetchUserDetails(userId) {
            const response = await fetch(`/users/${userId}/data`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return null;
            }

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Failed to load user details');
            }

            return payload.data;
        }

        function formatDate(value) {
            if (!value) {
                return '-';
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleString();
        }

        async function openViewUserModal(userId) {
            openModal('viewUserModal');
            document.getElementById('viewUserLoading').classList.remove('hidden');
            document.getElementById('viewUserContent').classList.add('hidden');

            try {
                const user = await fetchUserDetails(userId);

                if (!user) {
                    return;
                }

                document.getElementById('viewUsername').textContent = user.username || '-';
                document.getElementById('viewEmail').textContent = user.email || '-';
                document.getElementById('viewRole').textContent = user.role || '-';
                document.getElementById('viewVendor').textContent = user.vendor_name || '-';
                document.getElementById('viewUserId').textContent = user.user_id || '-';
                document.getElementById('viewCreatedAt').textContent = formatDate(user.created_at);

                document.getElementById('viewUserLoading').classList.add('hidden');
                document.getElementById('viewUserContent').classList.remove('hidden');
            } catch (error) {
                closeModal('viewUserModal');
                showAlert(error.message || 'Failed to load user details');
            }
        }

        async function openCreateUserModal() {
            await loadVendorOptions();
            userFormState.userId = null;
            setUserFormMode('create');
            resetUserForm();
            openModal('userFormModal');
        }

        async function openEditUserModal(userId) {
            openModal('userFormModal');
            userFormLoading.classList.remove('hidden');
            userFormLoading.classList.add('flex');
            userForm.classList.add('hidden');

            try {
                await loadVendorOptions();
                const user = await fetchUserDetails(userId);

                if (!user) {
                    return;
                }

                userFormState.userId = user.user_id;
                setUserFormMode('edit');
                resetUserForm();
                document.getElementById('formUserId').value = user.user_id;
                fieldMap.username.value = user.username || '';
                fieldMap.email.value = user.email || '';
                fieldMap.role.value = user.role || '';
                toggleVendorField();
                if (user.vendor_id) {
                    fieldMap.vendor_id.value = String(user.vendor_id);
                }

                userFormLoading.classList.add('hidden');
                userFormLoading.classList.remove('flex');
                userForm.classList.remove('hidden');
            } catch (error) {
                userFormLoading.classList.add('hidden');
                userFormLoading.classList.remove('flex');
                closeModal('userFormModal');
                showAlert(error.message || 'Failed to load user');
            }
        }

        document.getElementById('openCreateUserBtn').addEventListener('click', async () => {
            try {
                userFormLoading.classList.add('hidden');
                userFormLoading.classList.remove('flex');
                userForm.classList.remove('hidden');
                await openCreateUserModal();
            } catch (error) {
                showAlert(error.message || 'Failed to open user form');
            }
        });

        fieldMap.role.addEventListener('change', toggleVendorField);
        passwordToggleButton.addEventListener('click', () => {
            const isHidden = fieldMap.password.type === 'password';

            fieldMap.password.type = isHidden ? 'text' : 'password';
            passwordToggleButton.textContent = isHidden ? 'Hide' : 'Show';
            passwordToggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            passwordToggleButton.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        });

        userForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFormErrors();

            const payload = {
                username: fieldMap.username.value.trim(),
                email: fieldMap.email.value.trim(),
                password: fieldMap.password.value,
                role: fieldMap.role.value,
                vendor_id: fieldMap.role.value === 'VENDOR' ? fieldMap.vendor_id.value : null,
            };

            const submitButton = document.getElementById('submitBtn');
            const isEdit = userFormState.mode === 'edit';

            submitButton.disabled = true;
            submitButton.textContent = isEdit ? 'Updating...' : 'Creating...';

            try {
                const response = await fetch(isEdit ? `/users/${userFormState.userId}` : '/users', {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });
                const result = await response.json();

                if (!response.ok) {
                    Object.entries(result.errors || {}).forEach(([field, messages]) => {
                        if (Array.isArray(messages) && messages.length > 0) {
                            setFormError(field, messages[0]);
                        }
                    });
                    showFormSummary(result.message || 'Failed to save user');
                    return;
                }

                closeModal('userFormModal');
                showAlert(result.message || 'User saved successfully', 'success');
                loadUsers(currentPage, currentSearch, currentRole);
            } catch (error) {
                showFormSummary(error.message || 'Failed to save user');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = isEdit ? 'Update User' : 'Create User';
            }
        });

        document.getElementById('cancelDeleteBtn').addEventListener('click', () => closeModal('deleteModal'));
        document.getElementById('deleteModal').addEventListener('click', (event) => {
            if (event.target === event.currentTarget) {
                closeModal('deleteModal');
            }
        });
        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => closeModal(button.dataset.closeModal));
        });
        ['viewUserModal', 'userFormModal'].forEach((modalId) => {
            document.getElementById(modalId).addEventListener('click', (event) => {
                if (event.target === event.currentTarget) {
                    closeModal(modalId);
                }
            });
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
