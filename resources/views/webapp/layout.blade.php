<!-- resources/views/webapp/layout.blade.php -->
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VPNMarket Mini App</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Telegram Web App SDK -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <!-- Vazirmatn Font -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />

    <style>
        /* تعریف مقادیر پیش‌فرض برای رفع ارور و تست در مرورگر */
        :root {
            --tg-theme-bg-color: #18181b;           /* رنگ پس‌زمینه پیش‌فرض */
            --tg-theme-text-color: #ffffff;         /* رنگ متن پیش‌فرض */
            --tg-theme-secondary-bg-color: #27272a; /* رنگ کارت‌ها */
            --tg-theme-button-color: #3b82f6;       /* رنگ دکمه اصلی */
            --tg-theme-button-text-color: #ffffff;  /* رنگ متن دکمه */
            --tg-theme-hint-color: #9ca3af;         /* رنگ متون کم‌رنگ */
            --tg-theme-link-color: #3b82f6;         /* رنگ لینک‌ها */
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: var(--tg-theme-bg-color);
            color: var(--tg-theme-text-color);
            margin: 0;
            padding: 0;
        }

        .card {
            background-color: var(--tg-theme-secondary-bg-color);
        }

        .btn-primary {
            background-color: var(--tg-theme-button-color);
            color: var(--tg-theme-button-text-color);
        }

        /* مخفی کردن اسکرول بار */
        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 2px;
        }

        /* انیمیشن لودینگ ساده */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-zinc-900 text-white pb-24 select-none min-h-screen">

<!-- Content Section -->
<div class="p-4 fade-in">
    @yield('content')
</div>

<!-- Bottom Navigation -->
<div class="fixed bottom-0 left-0 right-0 border-t flex justify-around p-2 z-50 transition-colors duration-300"
     style="background-color: var(--tg-theme-secondary-bg-color); border-color: rgba(255,255,255,0.1);">

    <!-- دکمه خانه -->
    <a href="{{ route('webapp.index', ['tg_id' => request('tg_id')]) }}"
       class="flex flex-col items-center p-2 rounded-lg w-full {{ request()->routeIs('webapp.index') ? 'text-blue-400' : 'text-gray-400 opacity-70' }}">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="text-[10px] font-medium">خانه</span>
    </a>

    <!-- دکمه خرید -->
    <a href="{{ route('webapp.plans', ['tg_id' => request('tg_id')]) }}"
       class="flex flex-col items-center p-2 rounded-lg w-full {{ request()->routeIs('webapp.plans') ? 'text-blue-400' : 'text-gray-400 opacity-70' }}">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="text-[10px] font-medium">خرید</span>
    </a>
</div>

<script>
    // 1. مقداردهی اولیه تلگرام
    const tg = window.Telegram.WebApp;
    tg.expand(); // تمام صفحه کردن

    // 2. اعمال تم تلگرام روی متغیرهای CSS
    if (tg.themeParams) {
        const root = document.documentElement;
        if(tg.themeParams.bg_color) root.style.setProperty('--tg-theme-bg-color', tg.themeParams.bg_color);
        if(tg.themeParams.text_color) root.style.setProperty('--tg-theme-text-color', tg.themeParams.text_color);
        if(tg.themeParams.secondary_bg_color) root.style.setProperty('--tg-theme-secondary-bg-color', tg.themeParams.secondary_bg_color);
        if(tg.themeParams.button_color) root.style.setProperty('--tg-theme-button-color', tg.themeParams.button_color);
        if(tg.themeParams.button_text_color) root.style.setProperty('--tg-theme-button-text-color', tg.themeParams.button_text_color);
    }

    // 3. هندل کردن دکمه اصلی تلگرام (Main Button)
    // مخفی کردن دکمه اصلی به صورت پیش‌فرض در تمام صفحات
    tg.MainButton.hide();

    // 4. لاگین خودکار (اتصال به لاراول)
    // اگر tg_id در URL نیست ولی ما داخل تلگرام هستیم، آن را پیدا کرده و صفحه را رفرش می‌کنیم
    const urlParams = new URLSearchParams(window.location.search);
    const userId = tg.initDataUnsafe?.user?.id;

    if (!urlParams.has('tg_id') && userId) {
        urlParams.set('tg_id', userId);
        window.location.search = urlParams.toString();
    }
</script>
</body>
</html>
