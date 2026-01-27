<?php

namespace App\Http\Controllers\WebApp;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class WebAppController extends Controller
{
    /**
     * احراز هویت ساده بر اساس پارامترهای تلگرام
     * در محیط پروداکشن باید initData را اعتبارسنجی کنید
     */
    private function getUser(Request $request)
    {
        // در مینی‌اپ، تلگرام شناسه کاربر را ارسال می‌کند
        // فعلاً برای تست ساده از query string می‌گیریم
        // مثال: https://your-domain.com/webapp?tg_id=123456789
        $tgId = $request->query('tg_id');

        if (!$tgId) return null;

        return User::where('telegram_chat_id', $tgId)->first();
    }

    public function index(Request $request)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return view('webapp.error', ['message' => 'کاربر یافت نشد. لطفا ربات را استارت کنید.']);
        }

        $activeServices = $user->orders()
            ->where('status', 'paid')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return view('webapp.index', compact('user', 'activeServices'));
    }

    public function plans(Request $request)
    {
        $user = $this->getUser($request);
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return view('webapp.plans', compact('user', 'plans'));
    }

    public function orderDetail($id, Request $request)
    {
        $user = $this->getUser($request);
        $order = Order::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        return view('webapp.detail', compact('user', 'order'));
    }
}
