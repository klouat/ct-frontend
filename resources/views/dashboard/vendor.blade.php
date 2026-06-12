<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Vendor Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4faff] flex min-h-screen font-sans text-gray-800">

    @include('dashboard.partials.sidebar', ['activePage' => 'vendors'])

    <div class="flex-grow flex min-h-screen flex-col">

        <!-- HEADER -->
        <header class="bg-white py-4 px-8 shadow-sm flex justify-between items-center z-10">
            <h1 class="text-[#0033ab] text-xl font-bold tracking-tight">EPSON Smart Verification System (SVS)</h1>
            <div class="lg:hidden">
                <button id="mobileMenuBtn" data-open-mobile-sidebar class="p-2 text-gray-600">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </header>

        <main class="flex-grow p-4 md:p-8 space-y-6 max-w-7xl mx-auto w-full">

            <!-- Page Title & Add Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-[#0033ab]">
                    <i data-lucide="building-2" class="w-7 h-7"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Vendor Management</h2>
                </div>
                <button id="openAddModal"
                    class="flex items-center gap-2 rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800 active:scale-95 cursor-pointer">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Add Vendor
                </button>
            </div>

            <!-- Alert Banner -->
            <div id="alertBanner" class="hidden rounded-xl border px-4 py-3 text-sm font-medium"></div>

            <!-- Search + Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-blue-50 overflow-hidden">

                <!-- Search Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-5 border-b border-gray-100">
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        <input id="searchInput" type="text" placeholder="Search vendor name..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                    </div>
                    <button id="searchBtn"
                        class="flex items-center justify-center gap-2 rounded-xl bg-blue-50 px-5 py-3 text-sm font-bold text-[#0033ab] transition hover:bg-blue-100 cursor-pointer">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Filter
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#f8faff] text-xs uppercase tracking-widest text-gray-400 font-black">
                            <tr>
                                <th class="px-6 py-4 text-left w-16">#</th>
                                <th class="px-6 py-4 text-left">Vendor Name</th>
                                <th class="px-6 py-4 text-right w-40">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vendorTableBody" class="divide-y divide-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">
                                    <div class="flex flex-col items-center gap-2">
                                        <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-400"></i>
                                        Loading vendors...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div id="paginationBar" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 text-sm text-gray-500 font-medium">
                    <span id="paginationInfo"></span>
                    <div class="flex gap-2" id="paginationControls"></div>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="mt-auto bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between opacity-80">
                <p>EPSON SVS — Vendor Management</p>
                <p>App Version: 1.2.0-stable</p>
            </div>
        </footer>
    </div>

    <!-- ===== ADD MODAL ===== -->
    <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-black text-gray-800">Add Vendor</h3>
                <button id="closeAddModal" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="addForm" class="space-y-4">
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Vendor Name</label>
                    <input id="addVendorName" type="text" required maxlength="150" placeholder="e.g. Vendor A"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                    <p id="addError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" id="cancelAddModal"
                        class="flex-1 rounded-xl border-2 border-gray-200 py-3 font-bold text-gray-500 transition hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="addSubmitBtn"
                        class="flex-1 rounded-xl bg-[#0033ab] py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800 active:scale-95 cursor-pointer">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-black text-gray-800">Edit Vendor</h3>
                <button id="closeEditModal" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="editForm" class="space-y-4">
                <input type="hidden" id="editVendorId">
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">Vendor Name</label>
                    <input id="editVendorName" type="text" required maxlength="150"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                    <p id="editError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" id="cancelEditModal"
                        class="flex-1 rounded-xl border-2 border-gray-200 py-3 font-bold text-gray-500 transition hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="editSubmitBtn"
                        class="flex-1 rounded-xl bg-[#0033ab] py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800 active:scale-95 cursor-pointer">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== DELETE CONFIRM MODAL ===== -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-3xl bg-white p-8 shadow-2xl text-center">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-500">
                <i data-lucide="trash-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-black text-gray-800">Delete Vendor?</h3>
            <p class="mt-2 text-sm text-gray-400 font-medium">
                Are you sure you want to delete <span id="deleteVendorName" class="font-black text-gray-700"></span>?
                This action cannot be undone.
            </p>
            <input type="hidden" id="deleteVendorId">
            <div class="mt-6 flex gap-3">
                <button id="cancelDeleteModal"
                    class="flex-1 rounded-xl border-2 border-gray-200 py-3 font-bold text-gray-500 transition hover:bg-gray-50 cursor-pointer">
                    Cancel
                </button>
                <button id="confirmDeleteBtn"
                    class="flex-1 rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg shadow-red-100 transition hover:bg-red-600 active:scale-95 cursor-pointer">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const csrfToken    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const alertBanner  = document.getElementById('alertBanner');
        const tableBody    = document.getElementById('vendorTableBody');
        const searchInput  = document.getElementById('searchInput');
        const paginationInfo     = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        let current_page   = 1;
        let current_search = '';

        // ── Helpers ──────────────────────────────────────────────
        function show_alert(message, type = 'error') {
            const is_error = type === 'error';
            alertBanner.className = `rounded-xl border px-4 py-3 text-sm font-medium ${
                is_error
                    ? 'border-red-200 bg-red-50 text-red-600'
                    : 'border-green-200 bg-green-50 text-green-700'
            }`;
            alertBanner.textContent = message;
            alertBanner.classList.remove('hidden');
            setTimeout(() => alertBanner.classList.add('hidden'), 4000);
        }

        function open_modal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function close_modal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function set_btn_loading(btn, loading, default_text) {
            btn.disabled = loading;
            btn.textContent = loading ? 'Saving...' : default_text;
        }

        // ── Load vendors ──────────────────────────────────────────
        async function load_vendors(page = 1, search = '') {
            current_page   = page;
            current_search = search;

            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">
                        <div class="flex flex-col items-center gap-2">
                            <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-blue-400"></i>
                            Loading vendors...
                        </div>
                    </td>
                </tr>`;
            lucide.createIcons();

            try {
                const params = new URLSearchParams({ page, per_page: 20 });
                if (search) params.set('vendor_name', search);

                const response = await fetch(`/vendors/data?${params}`, {
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

                render_table(payload.data);
            } catch (error) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-red-400 font-medium">
                            ${error.message || 'Failed to load vendors'}
                        </td>
                    </tr>`;
            }
        }

        function render_table(data) {
            const items = data?.items ?? data?.data ?? [];
            const meta  = {
                current_page:  data?.current_page  ?? 1,
                last_page:     data?.last_page      ?? 1,
                from:          data?.from           ?? 0,
                to:            data?.to             ?? 0,
                total:         data?.total          ?? items.length,
            };

            if (items.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-14 text-center text-gray-400 font-medium">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="building-2" class="w-8 h-8 text-gray-300"></i>
                                No vendors found.
                            </div>
                        </td>
                    </tr>`;
                lucide.createIcons();
                paginationInfo.textContent = '';
                paginationControls.innerHTML = '';
                return;
            }

            tableBody.innerHTML = items.map((vendor, index) => {
                const row_num = ((meta.current_page - 1) * 20) + index + 1;
                return `
                    <tr class="hover:bg-blue-50/40 transition group">
                        <td class="px-6 py-4 text-gray-400 font-bold text-xs">${row_num}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0033ab]">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                </div>
                                <span class="font-bold text-gray-800">${escape_html(vendor.vendor_name)}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <button
                                    class="edit-btn flex items-center gap-1.5 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-bold text-[#0033ab] transition hover:bg-blue-100 cursor-pointer"
                                    data-id="${vendor.vendor_id}" data-name="${escape_html(vendor.vendor_name)}">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </button>
                                <button
                                    class="delete-btn flex items-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-bold text-red-500 transition hover:bg-red-100 cursor-pointer"
                                    data-id="${vendor.vendor_id}" data-name="${escape_html(vendor.vendor_name)}">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            lucide.createIcons();

            paginationInfo.textContent = meta.total > 0
                ? `Showing ${meta.from}–${meta.to} of ${meta.total} vendors`
                : '';

            render_pagination(meta.current_page, meta.last_page);

            // Attach row-action listeners
            tableBody.querySelectorAll('.edit-btn').forEach((btn) => {
                btn.addEventListener('click', () => open_edit_modal(btn.dataset.id, btn.dataset.name));
            });
            tableBody.querySelectorAll('.delete-btn').forEach((btn) => {
                btn.addEventListener('click', () => open_delete_modal(btn.dataset.id, btn.dataset.name));
            });
        }

        function render_pagination(current, last) {
            if (last <= 1) {
                paginationControls.innerHTML = '';
                return;
            }

            const btn_class = 'rounded-xl border px-4 py-2 text-sm font-bold transition cursor-pointer';
            let html = '';

            if (current > 1) {
                html += `<button class="${btn_class} border-gray-200 text-gray-600 hover:bg-gray-50" data-page="${current - 1}">← Prev</button>`;
            }

            const start = Math.max(1, current - 2);
            const end   = Math.min(last, current + 2);

            for (let p = start; p <= end; p++) {
                const active = p === current;
                html += `<button class="${btn_class} ${active ? 'border-[#0033ab] bg-[#0033ab] text-white shadow-lg shadow-blue-100' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}" data-page="${p}">${p}</button>`;
            }

            if (current < last) {
                html += `<button class="${btn_class} border-gray-200 text-gray-600 hover:bg-gray-50" data-page="${current + 1}">Next →</button>`;
            }

            paginationControls.innerHTML = html;
            paginationControls.querySelectorAll('button[data-page]').forEach((btn) => {
                btn.addEventListener('click', () => load_vendors(Number(btn.dataset.page), current_search));
            });
        }

        function escape_html(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // ── Add Vendor ────────────────────────────────────────────
        document.getElementById('openAddModal').addEventListener('click', () => {
            document.getElementById('addVendorName').value = '';
            document.getElementById('addError').classList.add('hidden');
            open_modal('addModal');
            setTimeout(() => document.getElementById('addVendorName').focus(), 100);
        });

        document.getElementById('closeAddModal').addEventListener('click', () => close_modal('addModal'));
        document.getElementById('cancelAddModal').addEventListener('click', () => close_modal('addModal'));

        document.getElementById('addForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name     = document.getElementById('addVendorName').value.trim();
            const error_el = document.getElementById('addError');
            const submit   = document.getElementById('addSubmitBtn');
            error_el.classList.add('hidden');

            if (!name) {
                error_el.textContent = 'Vendor name is required.';
                error_el.classList.remove('hidden');
                return;
            }

            set_btn_loading(submit, true, 'Save');
            try {
                const response = await fetch('/vendors', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ vendor_name: name }),
                });
                const payload = await response.json();

                if (!response.ok) {
                    const first_err = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;
                    throw new Error(first_err || payload.message || 'Failed to create vendor');
                }

                close_modal('addModal');
                show_alert(`Vendor "${name}" created successfully.`, 'success');
                load_vendors(1, current_search);
            } catch (error) {
                error_el.textContent = error.message;
                error_el.classList.remove('hidden');
            } finally {
                set_btn_loading(submit, false, 'Save');
            }
        });

        // ── Edit Vendor ───────────────────────────────────────────
        function open_edit_modal(id, name) {
            document.getElementById('editVendorId').value   = id;
            document.getElementById('editVendorName').value = name;
            document.getElementById('editError').classList.add('hidden');
            open_modal('editModal');
            setTimeout(() => document.getElementById('editVendorName').focus(), 100);
        }

        document.getElementById('closeEditModal').addEventListener('click', () => close_modal('editModal'));
        document.getElementById('cancelEditModal').addEventListener('click', () => close_modal('editModal'));

        document.getElementById('editForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id       = document.getElementById('editVendorId').value;
            const name     = document.getElementById('editVendorName').value.trim();
            const error_el = document.getElementById('editError');
            const submit   = document.getElementById('editSubmitBtn');
            error_el.classList.add('hidden');

            if (!name) {
                error_el.textContent = 'Vendor name is required.';
                error_el.classList.remove('hidden');
                return;
            }

            set_btn_loading(submit, true, 'Update');
            try {
                const response = await fetch(`/vendors/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ vendor_name: name }),
                });
                const payload = await response.json();

                if (!response.ok) {
                    const first_err = payload.errors ? Object.values(payload.errors)[0]?.[0] : null;
                    throw new Error(first_err || payload.message || 'Failed to update vendor');
                }

                close_modal('editModal');
                show_alert(`Vendor updated to "${name}" successfully.`, 'success');
                load_vendors(current_page, current_search);
            } catch (error) {
                error_el.textContent = error.message;
                error_el.classList.remove('hidden');
            } finally {
                set_btn_loading(submit, false, 'Update');
            }
        });

        // ── Delete Vendor ─────────────────────────────────────────
        function open_delete_modal(id, name) {
            document.getElementById('deleteVendorId').value  = id;
            document.getElementById('deleteVendorName').textContent = name;
            open_modal('deleteModal');
        }

        document.getElementById('cancelDeleteModal').addEventListener('click', () => close_modal('deleteModal'));
        document.getElementById('closeDeleteModal')?.addEventListener('click', () => close_modal('deleteModal'));

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            const id   = document.getElementById('deleteVendorId').value;
            const name = document.getElementById('deleteVendorName').textContent;
            const btn  = document.getElementById('confirmDeleteBtn');
            btn.disabled    = true;
            btn.textContent = 'Deleting...';

            try {
                const response = await fetch(`/vendors/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                });
                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Failed to delete vendor');
                }

                close_modal('deleteModal');
                show_alert(`Vendor "${name}" deleted.`, 'success');

                // If deleting the only item on a page > 1, go back one page
                const remaining = tableBody.querySelectorAll('tr:not([colspan])').length;
                const go_to_page = (remaining <= 1 && current_page > 1) ? current_page - 1 : current_page;
                load_vendors(go_to_page, current_search);
            } catch (error) {
                close_modal('deleteModal');
                show_alert(error.message || 'Failed to delete vendor');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Delete';
            }
        });

        // ── Search ────────────────────────────────────────────────
        document.getElementById('searchBtn').addEventListener('click', () => {
            load_vendors(1, searchInput.value.trim());
        });
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') load_vendors(1, searchInput.value.trim());
        });

        // ── Close modals on backdrop click ────────────────────────
        ['addModal', 'editModal', 'deleteModal'].forEach((id) => {
            document.getElementById(id).addEventListener('click', (e) => {
                if (e.target === e.currentTarget) close_modal(id);
            });
        });

        // ── Init ──────────────────────────────────────────────────
        load_vendors();
    </script>
</body>
</html>
