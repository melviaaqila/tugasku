<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\RoutineTask;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoutineTaskController extends Controller
{
    public function __construct()
    {

        // ✅ GUNAKAN INI: Kunci berdasarkan kemampuan (Permission)
        $this->middleware('permission:tugasrutin.view')->only('index');
        $this->middleware('permission:tugasrutin.create')->only('store');
        $this->middleware('permission:tugasrutin.edit')->only('update');
        $this->middleware('permission:tugasrutin.delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search    = $request->input('search', '');
        $perPage   = (int) $request->input('perPage', 5);
        $sortKey   = $request->input('sortKey', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');
        $divisi    = $request->input('divisi', 'all');

        $routineTasks = RoutineTask::with('divisi')
            ->when($search, fn($q) =>
                $q->where('nama_tugas', 'like', "%{$search}%")
            )
            ->when($divisi && $divisi !== 'all', fn($q) =>
                $q->where('divisi_id', $divisi)
            )
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage)
            ->withQueryString();

        $divisis = Divisi::all();

        return Inertia::render('Master/RoutineTask/Index', [
            'routineTasks' => $routineTasks,
            'divisis'      => $divisis,
            'filters'      => [
                'search'    => $search,
                'perPage'   => $perPage,
                'sortKey'   => $sortKey,
                'sortOrder' => $sortOrder,
                'divisi'    => $divisi,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'divisi_id'  => 'required|exists:divisi,id',
        ]);

        RoutineTask::create([
            'nama_tugas' => $validated['nama_tugas'],
            'divisi_id'  => $validated['divisi_id'],
        ]);

        return redirect()->back()->with('success', 'Tugas rutin berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'divisi_id'  => 'required|exists:divisi,id',
        ]);

        RoutineTask::where('id', $id)->update([
            'nama_tugas' => $validated['nama_tugas'],
            'divisi_id'  => $validated['divisi_id'],
        ]);

        return redirect()->back()->with('success', 'Tugas rutin berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        RoutineTask::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Tugas rutin berhasil dihapus.');
    }
}