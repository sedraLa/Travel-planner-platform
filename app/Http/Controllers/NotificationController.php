<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransportReservation;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead(); // تعليم كمقروء
        return redirect()->back(); // أو صفحة تفاصيل الحجز إذا عندها relation
    }
}

