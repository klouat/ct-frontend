<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Using Lucide icons via CDN for the input icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4faff] flex flex-col min-h-screen">

    <!-- Header Title -->
    <header class="py-6">
        <h1 class="text-[#0033ab] text-xl md:text-2xl font-bold text-center">
            EPSON Smart Verification System (SVS)
        </h1>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 pb-12">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-blue-50 p-8 md:p-10">
            
            <!-- Icon and Welcome -->
            <div class="flex flex-col items-center mb-8">
                <div class="w-20 h-20 mb-4 text-blue-500">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="#3b82f6" stroke="#3b82f6"/>
                        <path d="m9 12 2 2 4-4" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Welcome Back!</h2>
                <p class="text-gray-500 text-sm">Please login to continue</p>
            </div>

            <form id="loginForm" class="space-y-5">
                
                <!-- Username -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Email / Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </span>
                        <input type="text" id="username" placeholder="Enter email or username"
                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <p id="usernameError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </span>
                        <input type="password" id="password" placeholder="Enter password"
                               class="w-full pl-10 pr-10 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <p id="passwordError" class="mt-2 hidden text-sm text-red-500"></p>
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-700 focus:ring-blue-500">
                        Remember me
                    </label>
                    <a href="#" class="text-blue-800 font-bold text-sm hover:underline">Forgot Password ?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="w-full bg-[#0033ab] text-white py-3 rounded-xl font-bold text-lg hover:bg-blue-800 transition shadow-md">
                    Login
                </button>

                <!-- Divider -->
                <div class="flex items-center py-2">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="px-4 text-gray-400 text-sm">or</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- Scan Login -->
                <button type="button" class="w-full border-2 border-blue-200 text-[#0033ab] py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-blue-50 transition">
                    <i data-lucide="scan" class="w-5 h-5"></i>
                    Scan Login
                </button>

                <!-- Register -->
                <div class="text-center pt-4">
                    <p class="text-sm text-gray-600 mb-1">Don't have an account?</p>
                    <a href="/register" class="text-blue-800 font-bold hover:underline">Register</a>
                </div>

                <!-- Error Message -->
                <div id="errorSummary" class="hidden rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-600">
                    <p id="error" class="hidden text-center font-medium"></p>
                    <ul id="errorList" class="hidden list-disc space-y-1 pl-5"></ul>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer Info -->
    <footer class="bg-[#001a4d] text-white p-4 text-[10px] md:text-xs">
        <p>Database: Connected (Local Sync Active)</p>
        <p>Last Update: 2026-05-03 10:48 WIB</p>
        <p>App Version: 1.2.0-stable</p>
    </footer>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const loginFieldMap = {
            username: document.getElementById('username'),
            password: document.getElementById('password')
        };
        const loginErrorMap = {
            username: document.getElementById('usernameError'),
            password: document.getElementById('passwordError')
        };
        const errorSummary = document.getElementById('errorSummary');
        const errorEl = document.getElementById('error');
        const errorList = document.getElementById('errorList');

        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                password.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons(); // Refresh icons
        }

        function clearLoginErrors() {
            Object.values(loginFieldMap).forEach((field) => {
                field.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                field.classList.add('border-gray-200');
            });

            Object.values(loginErrorMap).forEach((errorNode) => {
                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            });

            errorSummary.classList.add('hidden');
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
            errorList.innerHTML = '';
            errorList.classList.add('hidden');
        }

        function setLoginFieldError(fieldName, message) {
            const field = loginFieldMap[fieldName];
            const errorNode = loginErrorMap[fieldName];

            if (!field || !errorNode || !message) {
                return;
            }

            field.classList.remove('border-gray-200');
            field.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            errorNode.textContent = message;
            errorNode.classList.remove('hidden');
        }

        function renderLoginErrors(message, errors = {}) {
            clearLoginErrors();

            const entries = Object.entries(errors).filter(([, messages]) => Array.isArray(messages) && messages.length > 0);
            const allMessages = entries.flatMap(([, messages]) => messages).filter((value) => typeof value === 'string' && value.trim() !== '');

            entries.forEach(([field, messages]) => {
                setLoginFieldError(field, messages[0]);
            });

            if (allMessages.length > 1) {
                errorList.innerHTML = allMessages.map((item) => `<li>${item}</li>`).join('');
                errorList.classList.remove('hidden');
                errorSummary.classList.remove('hidden');
                return;
            }

            if (allMessages.length === 1) {
                errorEl.textContent = allMessages[0];
            } else if (message) {
                errorEl.textContent = message;
            } else {
                errorEl.textContent = 'Login failed';
            }

            errorEl.classList.remove('hidden');
            errorSummary.classList.remove('hidden');
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            clearLoginErrors();

            if (!username || !password) {
                if (!username) {
                    setLoginFieldError('username', 'Email or username is required.');
                }

                if (!password) {
                    setLoginFieldError('password', 'Password is required.');
                }

                renderLoginErrors('Please fill in the required fields.', {
                    username: !username ? ['Email or username is required.'] : [],
                    password: !password ? ['Password is required.'] : []
                });
                return;
            }

            try {
                const res = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ username, password })
                });

                const data = await res.json();

                if (!res.ok) {
                    renderLoginErrors(data.message || 'Login failed', data.errors || {});
                    return;
                }

                window.location.href = data.redirect || '/invoice';

            } catch (err) {
                renderLoginErrors(err.message || 'Login failed');
            }
        });
    </script>
</body>
</html>
