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
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </span>
                    </div>
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

                <p id="msg" class="text-sm hidden text-center font-medium mt-4"></p>
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

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const msgEl = document.getElementById('msg');
            msgEl.className = "text-sm hidden text-center font-medium mt-4";

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const role = document.getElementById('role').value;

            if (!username) {
                msgEl.textContent = "Username is required";
                msgEl.classList.add('text-red-500', 'block');
                msgEl.classList.remove('hidden');
                return;
            }

            if (!email) {
                msgEl.textContent = "Email is required";
                msgEl.classList.add('text-red-500', 'block');
                msgEl.classList.remove('hidden');
                return;
            }

            // Basic Client Validations
            if (password !== confirm) {
                msgEl.textContent = "Passwords do not match";
                msgEl.classList.add('text-red-500', 'block');
                msgEl.classList.remove('hidden');
                return;
            }

            // --- PAYLOAD CONSTRUCTION ---
            const payload = {
                username: username,
                email: email,
                password: password,
                role: role
            };

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
                    throw new Error(data.message || 'Registration failed');
                }

                msgEl.textContent = data.message || "Registration successful! Redirecting...";
                msgEl.classList.add('text-green-500', 'block');
                msgEl.classList.remove('hidden');
                
                setTimeout(() => window.location.href = data.redirect || '/login', 1500);

            } catch (err) {
                msgEl.textContent = err.message;
                msgEl.classList.add('text-red-500', 'block');
                msgEl.classList.remove('hidden');
            }
        });

    </script>
</body>
</html>
