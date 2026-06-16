@php
    $isEdit = $mode === 'edit';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - {{ $isEdit ? 'Edit User' : 'Create User' }}</title>
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

        <main class="mx-auto w-full max-w-4xl flex-grow space-y-6 p-4 md:p-8">
            <div class="flex items-center gap-3 text-[#0033ab]">
                <i data-lucide="{{ $isEdit ? 'user-cog' : 'user-plus' }}" class="h-7 w-7"></i>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $isEdit ? 'Edit User' : 'Create User' }}</h2>
                    <p class="text-sm font-medium text-gray-400">
                        {{ $isEdit ? 'Update user account details and password if needed.' : 'Create a new account with the correct role and access level.' }}
                    </p>
                </div>
            </div>

            <div id="messageSummary" class="hidden rounded-xl border p-4 text-sm"></div>

            <div class="rounded-3xl border border-blue-50 bg-white p-6 shadow-sm md:p-8">
                <form id="userForm" class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
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
                            <label class="mb-2 block text-xs font-black uppercase tracking-widest text-gray-400">
                                {{ $isEdit ? 'New Password' : 'Password' }}
                            </label>
                            <input id="password" type="password" minlength="8" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0033ab]">
                            <p class="mt-2 text-xs text-gray-400">
                                {{ $isEdit ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.' }}
                            </p>
                            <p id="passwordError" class="mt-2 hidden text-xs font-medium text-red-500"></p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-gray-100 pt-6 sm:flex-row">
                        <a href="{{ $isEdit ? '/users/'.$userId : '/users' }}" class="rounded-xl border-2 border-gray-200 px-5 py-3 text-center font-bold text-gray-500 transition hover:bg-gray-50">
                            Cancel
                        </a>
                        <button id="submitBtn" type="submit" class="rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800">
                            {{ $isEdit ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <footer class="mt-auto bg-[#001a4d] p-4 text-[10px] text-white md:text-xs">
            <div class="mx-auto flex max-w-7xl flex-col justify-between opacity-80 md:flex-row">
                <p>EPSON SVS - {{ $isEdit ? 'Edit User' : 'Create User' }}</p>
                <p>App Version: 1.2.0-stable</p>
            </div>
        </footer>
    </div>

    <script>
        lucide.createIcons();

        const isEdit = @json($isEdit);
        const userId = @json($userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const messageSummary = document.getElementById('messageSummary');
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

        function clearErrors() {
            Object.values(fieldMap).forEach((field) => {
                field.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                field.classList.add('border-gray-200');
            });

            Object.values(errorMap).forEach((errorNode) => {
                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            });

            messageSummary.className = 'hidden rounded-xl border p-4 text-sm';
            messageSummary.textContent = '';
        }

        function setFieldError(fieldName, message) {
            const field = fieldMap[fieldName];
            const errorNode = errorMap[fieldName];

            if (!field || !errorNode || !message) {
                return;
            }

            field.classList.remove('border-gray-200');
            field.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            errorNode.textContent = message;
            errorNode.classList.remove('hidden');
        }

        function showSummary(message, type = 'error') {
            messageSummary.className = 'rounded-xl border p-4 text-sm';
            if (type === 'success') {
                messageSummary.classList.add('border-green-200', 'bg-green-50', 'text-green-700');
            } else {
                messageSummary.classList.add('border-red-200', 'bg-red-50', 'text-red-600');
            }
            messageSummary.textContent = message;
        }

        function toggleVendorField() {
            const showVendor = document.getElementById('role').value === 'VENDOR';
            document.getElementById('vendorField').classList.toggle('hidden', !showVendor);
        }

        async function loadVendors() {
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
            document.getElementById('vendor_id').innerHTML = `
                <option value="">Select vendor</option>
                ${vendors.map((vendor) => `<option value="${vendor.vendor_id}">${vendor.vendor_name}</option>`).join('')}
            `;
        }

        async function loadUser() {
            if (!isEdit) {
                return;
            }

            const response = await fetch(`/users/${userId}/data`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Failed to load user');
            }

            const user = payload.data;
            document.getElementById('username').value = user.username || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('role').value = user.role || '';
            toggleVendorField();

            if (user.vendor_id) {
                document.getElementById('vendor_id').value = String(user.vendor_id);
            }
        }

        document.getElementById('role').addEventListener('change', toggleVendorField);

        document.getElementById('userForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();

            const payload = {
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                password: document.getElementById('password').value,
                role: document.getElementById('role').value,
                vendor_id: document.getElementById('role').value === 'VENDOR'
                    ? document.getElementById('vendor_id').value
                    : null,
            };

            const submitButton = document.getElementById('submitBtn');
            submitButton.disabled = true;
            submitButton.textContent = isEdit ? 'Updating...' : 'Creating...';

            try {
                const response = await fetch(isEdit ? `/users/${userId}` : '/users', {
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
                    const errors = result.errors || {};
                    Object.entries(errors).forEach(([field, messages]) => {
                        if (Array.isArray(messages) && messages.length > 0) {
                            setFieldError(field, messages[0]);
                        }
                    });

                    showSummary(result.message || 'Failed to save user');
                    return;
                }

                showSummary(result.message || 'User saved successfully', 'success');
                window.location.href = `/users/${result.data.user_id}`;
            } catch (error) {
                showSummary(error.message || 'Failed to save user');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = isEdit ? 'Update User' : 'Create User';
            }
        });

        Promise.all([loadVendors(), loadUser()])
            .catch((error) => showSummary(error.message || 'Failed to load form data'));
    </script>
</body>
</html>
