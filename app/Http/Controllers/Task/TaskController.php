<?php
namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\RoutineTask;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function __construct()
    {

        // ✅ GUNAKAN INI: Kunci berdasarkan kemampuan (Permission)
        $this->middleware('permission:tugasku.view')->only('index');
        $this->middleware('permission:tugasku.create')->only('store');
        $this->middleware('permission:tugasku.edit')->only('update');
        $this->middleware('permission:tugasku.delete')->only('destroy');
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
        $status    = $request->input('status', 'all');
        $user      = auth()->user();

        $routineTasks = RoutineTask::where('divisi_id', $user->divisi_id)->get();

        $tasks = $user->tasks()
            ->where('user_id', $user->id)
            ->when($search, fn($query) => $query->where('nama_tugas', 'like', "%{$search}%"))
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->orderBy($sortKey, $sortOrder)
            ->paginate($perPage);

        return Inertia::render('Tasks/Index', [
            'tasks'        => $tasks,
            'routineTasks' => $routineTasks,
            'filters'      => [
                'search'    => $search,
                'perPage'   => $perPage,
                'sortKey'   => $sortKey,
                'sortOrder' => $sortOrder,
                'status'    => $status,
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
            'jenis_tugas' => 'required|string|in:tugas rutin,tugas tambahan',
        ]);

        $user = auth()->user();

        $user->tasks()->create([
            'nama_tugas' => $validated['nama_tugas'],
            'jenis_tugas' => $validated['jenis_tugas'],
            'status' => 'belum',
            'created_at' => now()->timezone('Asia/Jakarta')->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = auth()->user()->tasks()->findOrFail($id);

        $validated = $request->validate([
            'nama_tugas' => 'string|max:255',
            'jenis_tugas' => 'string|in:tugas rutin,tugas tambahan',
            'status' => 'string|in:belum,sedang berlangsung,selesai',
        ]);

        $task->update([
            'nama_tugas' => $validated['nama_tugas'],
            'jenis_tugas' => $validated['jenis_tugas'],
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'selesai') {
            History::create([
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'nama_tugas' => $task->nama_tugas,
                'jenis_tugas' => $task->jenis_tugas,
                'divisi' => auth()->user()->divisi->nama_divisi,
                'tanggal_dibuat' => $task->created_at,
                'tanggal_selesai' => now()->timezone('Asia/Jakarta')->toDateString(),
            ]);
        }

        return redirect()->back()->with('success', 'Tugas berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }
}
