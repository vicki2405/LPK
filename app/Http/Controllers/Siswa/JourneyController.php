<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JourneyController extends Controller
{
    /**
     * Redirect to student report card.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('siswa.rapor.index');
    }
}

