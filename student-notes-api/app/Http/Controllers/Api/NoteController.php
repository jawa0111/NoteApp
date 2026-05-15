<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        $result = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return response()->json($result, 200);
    }

    public function store(StoreNoteRequest $request)
    {
        $note = Note::create($request->validated());

        return response()->json([
            'message' => 'Note created',
            'data' => $note
        ], 201);
    }

    public function archive($id)
    {
        $note = Note::findOrFail($id);

        if (auth()->check()) {
            $this->authorize('archive', $note);
        }

        $note->update([
            'is_archived' => true,
        ]);

        return response()->json([
            'message' => 'Note archived'
        ], 200);
    }
}