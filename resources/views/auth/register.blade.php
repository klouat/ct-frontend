<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4faff] flex flex-col min-h-screen font-sans">

    <!-- Header Title -->
    <header class="py-6">
        <h1 class="text-[#0033ab] text-xl md:text-2xl font-bold text-center">
            EPSON Smart Verification System (SVS)
        </h1>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pb-12">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-blue-50 p-8 md:p-10">
            
            <div class="flex flex-col items-center mb-6">
                <div class="w-20 h-20 mb-4 bg-[#d0e1ff] rounded-full flex items-center justify-center text-[#0033ab]">
                    <i data-lucide="user-plus" class="w-10 h-10"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Create New Account</h2>
                <p class="text-gray-400 text-sm">Fill in your details to register</p>
            </div>

            <form id="registerForm" class="space-y-4">
                
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </span>
                        <input type="text" id="username" placeholder="Enter your username" maxlength="50" required
                               class="w-full pl-10 pr-4 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <p id="usernameError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </span>
                        <input type="email" id="email" placeholder="Enter your email address" required
                               class="w-full pl-10 pr-4 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">You can log in with either your email or your username.</p>
                    <p id="emailError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Role</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="contact-round" class="w-5 h-5"></i>
                        </span>
                        <select id="role" required
                                class="w-full pl-10 pr-10 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-500">
                            <option value="" disabled selected>Select role</option>
                            <option value="ADMIN">ADMIN</option>
                            <option value="SUPERVISOR">SUPERVISOR</option>
                            <option value="PETUGAS_GUDANG">PETUGAS GUDANG</option>
                            <option value="VENDOR">VENDOR</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </span>
                    </div>
                    <p id="roleError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div id="vendorField" class="hidden">
                    <label class="block text-sm font-bold text-gray-800 mb-2">Vendor</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="building" class="w-5 h-5"></i>
                        </span>
                        <select id="vendor_id"
                                class="w-full pl-10 pr-10 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-500">
                            <option value="" disabled selected>Select vendor</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </span>
                    </div>
                    <p id="vendorIdError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </span>
                        <input type="password" id="password" placeholder="create your password" minlength="8" required
                               class="w-full pl-10 pr-10 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <button type="button" onclick="togglePass('password', 'eye1')" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i data-lucide="eye" id="eye1" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p id="passwordError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </span>
                        <input type="password" id="confirm_password" placeholder="confirm your password" required
                               class="w-full pl-10 pr-10 py-2.5 bg-[#f8f9fc] border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <button type="button" onclick="togglePass('confirm_password', 'eye2')" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i data-lucide="eye" id="eye2" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p id="confirmPasswordError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <div class="flex items-center gap-2 py-2">
                    <input type="checkbox" id="terms" required class="w-5 h-5 rounded border-gray-300 text-blue-700 focus:ring-blue-500">
                    <label for="terms" class="text-sm text-gray-700">
                        I agree to the <a href="#" class="text-[#0033ab] font-bold hover:underline">Terms & Conditions</a>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#0033ab] text-white py-3 rounded-xl font-bold text-lg hover:bg-blue-800 transition shadow-md">
                    Register
                </button>

                <div class="flex items-center py-2">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="px-4 text-gray-400 text-sm">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-1">Already have an account?</p>
                    <a href="/login" class="text-[#0033ab] font-bold text-lg hover:underline">Login</a>
                </div>

                <div id="messageSummary" class="hidden rounded-xl border p-3 text-sm">
                    <p id="msg" class="hidden text-center font-medium"></p>
                    <ul id="messageList" class="hidden list-disc space-y-1 pl-5"></ul>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
        <p>Database: Connected (Local Sync Active)</p>
        <p>Last Update: 2026-05-03 10:48 WIB</p>
        <p>App Version: 1.2.0-stable</p>
    </footer>

    <script>
        lucide.createIcons();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const registerFieldMap = {
            username: document.getElementById('username'),
            email: document.getElementById('email'),
            role: document.getElementById('role'),
            vendor_id: document.getElementById('vendor_id'),
            password: document.getElementById('password'),
            confirm_password: document.getElementById('confirm_password')
        };
        const registerErrorMap = {
            username: document.getElementById('usernameError'),
            email: document.getElementById('emailError'),
            role: document.getElementById('roleError'),
            vendor_id: document.getElementById('vendorIdError'),
            password: document.getElementById('passwordError'),
            confirm_password: document.getElementById('confirmPasswordError')
        };
        const messageSummary = document.getElementById('messageSummary');
        const msgEl = document.getElementById('msg');
        const messageList = document.getElementById('messageList');

        async function loadVendors() {
            try {
                const res = await fetch('/api/public/vendors');
                const data = await res.json();
                if (data && data.data && data.data.items) {
                    const vendorSelect = document.getElementById('vendor_id');
                    data.data.items.forEach(vendor => {
                        const option = document.createElement('option');
                        option.value = vendor.vendor_id;
                        option.textContent = vendor.vendor_name;
                        vendorSelect.appendChild(option);
                    });
                }
            } catch (err) {
                console.error('Failed to load vendors', err);
            }
        }

        document.getElementById('role').addEventListener('change', function() {
            const vendorField = document.getElementById('vendorField');
            if (this.value === 'VENDOR') {
                vendorField.classList.remove('hidden');
            } else {
                vendorField.classList.add('hidden');
            }
        });

        loadVendors();

        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function clearRegisterErrors() {
            Object.values(registerFieldMap).forEach((field) => {
                field.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                field.classList.add('border-gray-200');
            });

            Object.values(registerErrorMap).forEach((errorNode) => {
                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            });

            messageSummary.className = 'hidden rounded-xl border p-3 text-sm';
            msgEl.textContent = '';
            msgEl.classList.add('hidden');
            messageList.innerHTML = '';
            messageList.classList.add('hidden');
        }

        function setRegisterFieldError(fieldName, message) {
            const field = registerFieldMap[fieldName];
            const errorNode = registerErrorMap[fieldName];

            if (!field || !errorNode || !message) {
                return;
            }

            field.classList.remove('border-gray-200');
            field.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            errorNode.textContent = message;
            errorNode.classList.remove('hidden');
        }

        function showRegisterSummary(message, type = 'error', items = []) {
            messageSummary.className = 'rounded-xl border p-3 text-sm';

            if (type === 'success') {
                messageSummary.classList.add('border-green-200', 'bg-green-50', 'text-green-600');
            } else {
                messageSummary.classList.add('border-red-200', 'bg-red-50', 'text-red-600');
            }

            if (items.length > 1) {
                messageList.innerHTML = items.map((item) => `<li>${item}</li>`).join('');
                messageList.classList.remove('hidden');
                msgEl.classList.add('hidden');
            } else {
                msgEl.textContent = items[0] || message;
                msgEl.classList.remove('hidden');
                messageList.classList.add('hidden');
            }
        }

        function renderRegisterErrors(message, errors = {}) {
            clearRegisterErrors();

            const entries = Object.entries(errors).filter(([, messages]) => Array.isArray(messages) && messages.length > 0);
            const allMessages = entries.flatMap(([, messages]) => messages).filter((value) => typeof value === 'string' && value.trim() !== '');

            entries.forEach(([field, messages]) => {
                setRegisterFieldError(field, messages[0]);
            });

            if (allMessages.length > 0) {
                showRegisterSummary(message, 'error', allMessages);
                return;
            }

            showRegisterSummary(message || 'Registration failed', 'error');
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearRegisterErrors();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const role = document.getElementById('role').value;
            const vendorId = document.getElementById('vendor_id').value;

            if (!username) {
                renderRegisterErrors('Please fill in the required fields.', {
                    username: ['Username is required.']
                });
                return;
            }

            if (!email) {
                renderRegisterErrors('Please fill in the required fields.', {
                    email: ['Email is required.']
                });
                return;
            }

            if (role === 'VENDOR' && !vendorId) {
                renderRegisterErrors('Please fill in the required fields.', {
                    vendor_id: ['Vendor is required for VENDOR role.']
                });
                return;
            }

            if (password !== confirm) {
                renderRegisterErrors('Please review the highlighted fields.', {
                    password: ['Passwords do not match.'],
                    confirm_password: ['Passwords do not match.']
                });
                return;
            }

            const payload = {
                username: username,
                email: email,
                password: password,
                role: role
            };

            if (role === 'VENDOR') {
                payload.vendor_id = vendorId;
            }

            try {
                const res = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    renderRegisterErrors(data.message || 'Registration failed', data.errors || {});
                    return;
                }

                clearRegisterErrors();
                showRegisterSummary(data.message || 'Registration successful! Redirecting...', 'success');
                
                setTimeout(() => window.location.href = data.redirect || '/login', 1500);

            } catch (err) {
                renderRegisterErrors(err.message || 'Registration failed');
            }
        });

    </script>
</body>
</html>
