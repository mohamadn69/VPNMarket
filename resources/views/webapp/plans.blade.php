<!-- resources/views/webapp/plans.blade.php -->
@extends('webapp.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4 text-center">🛒 فروشگاه سرویس‌ها</h2>

    <div class="grid gap-4">
        @foreach($plans as $plan)
            <div class="card p-5 rounded-2xl border border-zinc-700 relative overflow-hidden">
                @if($plan->is_popular)
                    <div class="absolute top-0 left-0 bg-yellow-500 text-black text-[10px] font-bold px-3 py-1 rounded-br-lg">پیشنهاد ویژه</div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">{{ $plan->name }}</h3>
                    <div class="text-right">
                        <span class="block text-xl font-bold text-blue-400">{{ number_format($plan->price) }}</span>
                        <span class="text-xs text-gray-500">تومان</span>
                    </div>
                </div>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-sm text-gray-300">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ $plan->volume_gb }} گیگابایت حجم</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-300">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $plan->duration_days }} روز اعتبار</span>
                    </div>
                </div>

                <!-- دکمه خرید که کاربر را به ربات برمی‌گرداند تا پرداخت کند -->
                <button onclick="tg.close(); tg.sendData('buy_plan_{{ $plan->id }}')" class="w-full btn-primary py-3 rounded-xl font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-transform">
                    انتخاب و خرید
                </button>
            </div>
        @endforeach
    </div>
@endsection
