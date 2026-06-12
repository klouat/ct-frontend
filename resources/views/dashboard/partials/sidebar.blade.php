@php
    $activePage = $activePage ?? '';
    $userRole = (string) session('svs_auth.user.role', '');

    $navigationItems = match ($userRole) {
        'SUPERVISOR' => [
            ['key' => 'home', 'label' => 'Dashboard', 'icon' => 'layout-grid', 'href' => '/home'],
            ['key' => 'audit-logs', 'label' => 'Audit Logs', 'icon' => 'scroll-text', 'href' => '/audit-logs'],
        ],
        'PETUGAS_GUDANG' => [
            ['key' => 'scan', 'label' => 'Scanner', 'icon' => 'scan', 'href' => '/scan'],
        ],
        'VENDOR' => [
            ['key' => 'invoice-barcodes', 'label' => 'QR Preview', 'icon' => 'qr-code', 'href' => '/invoice-barcodes'],
        ],
        default => [
            ['key' => 'home',    'label' => 'Dashboard',     'icon' => 'layout-grid', 'href' => '/home'],
            ['key' => 'invoice', 'label' => 'Input Invoice', 'icon' => 'file-text',   'href' => '/invoice'],
            ['key' => 'vendors', 'label' => 'Vendors',       'icon' => 'building-2',  'href' => '/vendors'],
            ['key' => 'history', 'label' => 'History',       'icon' => 'history',     'href' => '/history'],
            ['key' => 'audit-logs', 'label' => 'Audit Logs', 'icon' => 'scroll-text', 'href' => '/audit-logs'],
        ],
    };
@endphp

<aside class="hidden lg:flex flex-col w-64 bg-white shadow-xl z-20">
    <div class="p-6 mb-4">
        <h1 class="text-[#0033ab] font-black text-2xl tracking-tighter">
            <i data-lucide="qr-code" class="w-8 h-8 inline-block mr-2"></i>EPSON
        </h1>
    </div>

    <nav class="flex-grow space-y-1 px-4">
        @foreach ($navigationItems as $item)
            @php
                $isActive = $activePage === $item['key'];
            @endphp
            <a
                href="{{ $item['href'] }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $isActive ? 'bg-[#0033ab] text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-blue-50' }}"
            >
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                <span class="font-bold">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 w-full rounded-xl transition font-bold">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Logout
            </button>
        </form>
    </div>
</aside>

<div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

<aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-white shadow-xl transition-transform duration-300 ease-in-out lg:hidden">
    <div class="flex items-center justify-between border-b border-gray-100 p-6">
        <h1 class="text-[#0033ab] font-black text-2xl tracking-tighter">
            <i data-lucide="qr-code" class="w-8 h-8 inline-block mr-2"></i>EPSON
        </h1>
        <button type="button" data-close-mobile-sidebar class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <nav class="flex-grow space-y-1 px-4 py-4">
        @foreach ($navigationItems as $item)
            @php
                $isActive = $activePage === $item['key'];
            @endphp
            <a
                href="{{ $item['href'] }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ $isActive ? 'bg-[#0033ab] text-white shadow-lg shadow-blue-200' : 'text-gray-500 hover:bg-blue-50' }}"
            >
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                <span class="font-bold">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-gray-100 p-4">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 w-full rounded-xl transition font-bold">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                Logout
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeButtons = document.querySelectorAll('[data-close-mobile-sidebar]');

        if (!mobileSidebar || !mobileSidebarOverlay) {
            return;
        }

        const openSidebar = () => {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileSidebarOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const closeSidebar = () => {
            mobileSidebar.classList.add('-translate-x-full');
            mobileSidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        mobileSidebarOverlay.addEventListener('click', closeSidebar);
        closeButtons.forEach((button) => button.addEventListener('click', closeSidebar));

        document.addEventListener('click', (event) => {
            const openTrigger = event.target.closest('[data-open-mobile-sidebar]');

            if (openTrigger) {
                openSidebar();
                return;
            }

            const closeTrigger = event.target.closest('[data-close-mobile-sidebar]');

            if (closeTrigger) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    });
</script>