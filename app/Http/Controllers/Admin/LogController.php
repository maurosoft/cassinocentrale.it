<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\WhatsappLog;
use Illuminate\Contracts\View\View;

class LogController extends Controller
{
    public function email(): View
    {
        $logs = EmailLog::latest()->paginate(30);

        return view('admin.logs.email', compact('logs'));
    }

    public function whatsapp(): View
    {
        $logs = WhatsappLog::latest()->paginate(30);

        return view('admin.logs.whatsapp', compact('logs'));
    }
}
