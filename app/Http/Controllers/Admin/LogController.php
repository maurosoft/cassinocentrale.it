<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Contracts\View\View;

class LogController extends Controller
{
    public function email(): View
    {
        $logs = EmailLog::latest()->paginate(30);

        return view('admin.logs.email', compact('logs'));
    }
}
