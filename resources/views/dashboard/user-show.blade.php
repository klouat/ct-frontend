<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPSON SVS - User Details</title>
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

        <main class="mx-auto w-full max-w-5xl flex-grow space-y-6 p-4 md:p-8">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3 text-[#0033ab]">
                    <i data-lucide="badge-info" class="h-7 w-7"></i>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">User Details</h2>
                        <p class="text-sm font-medium text-gray-400">Review account data, assigned role, and vendor linkage.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="/users" class="rounded-xl border-2 border-gray-200 px-5 py-3 font-bold text-gray-500 transition hover:bg-gray-50">Back</a>
                    <a href="/users/{{ $userId }}/edit" class="rounded-xl bg-[#0033ab] px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-800">Edit User</a>
                </div>
            </div>

            <div id="messageSummary" class="hidden rounded-xl border p-4 text-sm"></div>

            <div class="rounded-3xl border border-blue-50 bg-white p-6 shadow-sm md:p-8">
                <div id="loadingState" class="flex flex-col items-center gap-3 py-12 text-gray-400">
                    <i data-lucide="loader-2" class="h-7 w-7 animate-spin text-blue-400"></i>
                    Loading user details...
                </div>

                <div id="detailCard" class="hidden space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-[#0033ab]">
                            <i data-lucide="user-round" class="h-8 w-8"></i>
                        </div>
                        <div>
                            <h3 id="detailUsername" class="text-2xl font-black text-gray-800"></h3>
                            <p id="detailEmail" class="font-medium text-gray-400"></p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Role</p>
                            <p id="detailRole" class="mt-2 text-lg font-bold text-[#0033ab]"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Vendor</p>
                            <p id="detailVendor" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">User ID</p>
                            <p id="detailUserId" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-[#f8fbff] p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Created At</p>
                            <p id="detailCreatedAt" class="mt-2 text-lg font-bold text-gray-800"></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="mt-auto bg-[#001a4d] p-4 text-[10px] text-white md:text-xs">
            <div class="mx-auto flex max-w-7xl flex-col justify-between opacity-80 md:flex-row">
                <p>EPSON SVS - User Details</p>
                <p>App Version: 1.2.0-stable</p>
            </div>
        </footer>
    </div>

    <script>
        lucide.createIcons();

        const userId = @json($userId);
        const messageSummary = document.getElementById('messageSummary');
        const detailCard = document.getElementById('detailCard');
        const loadingState = document.getElementById('loadingState');

        function showSummary(message) {
            messageSummary.className = 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600';
            messageSummary.textContent = message;
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

        async function loadUser() {
            try {
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
                    throw new Error(payload.message || 'Failed to load user details');
                }

                const user = payload.data;
                document.getElementById('detailUsername').textContent = user.username || '-';
                document.getElementById('detailEmail').textContent = user.email || '-';
                document.getElementById('detailRole').textContent = user.role || '-';
                document.getElementById('detailVendor').textContent = user.vendor_name || '-';
                document.getElementById('detailUserId').textContent = user.user_id || '-';
                document.getElementById('detailCreatedAt').textContent = formatDate(user.created_at);

                loadingState.classList.add('hidden');
                detailCard.classList.remove('hidden');
                lucide.createIcons();
            } catch (error) {
                loadingState.classList.add('hidden');
                showSummary(error.message || 'Failed to load user details');
            }
        }

        loadUser();
    </script>
</body>
</html>
