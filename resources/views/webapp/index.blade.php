<!-- resources/views/webapp/index.blade.php -->
@extends('webapp.layout')

@section('content')
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-bold">سلام، {{ $user->name }} 👋</h1>
            <p class="text-sm text-gray-400 text-xs">موجودی: {{ number_format($user->balance) }} تومان</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
            {{ substr($user->name, 0, 1) }}
        </div>
    </div>

    <!-- Active Services Section -->
    <h2 class="text-md font-bold mb-3 border-r-4 border-blue-500 pr-2">سرویس‌های فعال من</h2>

    @if($activeServices->count() > 0)
        <div class="space-y-3">
            @foreach($activeServices as $order)
                <a href="{{ route('webapp.order', ['id' => $order->id, 'tg_id' => request('tg_id')]) }}" class="block card p-4 rounded-xl shadow-lg border border-zinc-700 active:scale-95 transition-transform">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-white">{{ $order->plan->name }}</h3>
                            <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded mt-1 inline-block">فعال</span>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs text-gray-400">حجم: {{ $order->plan->volume_gb }} GB</span>
                            <span class="block text-xs text-gray-400 mt-1">انقضا: {{ \Carbon\Carbon::parse($order->expires_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    <!-- Progress Bar (Fake for visual) -->
                    <div class="w-full bg-zinc-700 rounded-full h-1.5 mt-4">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 75%"></div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-10 card rounded-xl border border-dashed border-zinc-600">
            <p class="text-gray-400 mb-4">شما هیچ سرویس فعالی ندارید 😔</p>
            <a href="{{ route('webapp.plans', ['tg_id' => request('tg_id')]) }}" class="btn-primary px-6 py-2 rounded-lg text-sm shadow-lg shadow-blue-500/30">خرید سرویس جدید</a>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3 mt-6">
        <button onclick="tg.showAlert('بخش آموزش به زودی...')" class="card p-3 rounded-lg flex items-center justify-center gap-2 text-sm text-gray-300">
            📚 آموزش اتصال
        </button>
        <button onclick="tg.openLink('https://t.me/VPNMarket_OfficialSupport')" class="card p-3 rounded-lg flex items-center justify-center gap-2 text-sm text-gray-300">
            💬 پشتیبانی
        </button>
    </div>
@endsection
