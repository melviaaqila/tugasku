<?php
namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function __construct()
    {

        // ✅ GUNAKAN INI: Kunci berdasarkan kemampuan (Permission)
        $this->middleware('permission:history.view')->only('index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $histories = History::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('History/Index', [
            'histories' => $histories
        ]);
    }
}