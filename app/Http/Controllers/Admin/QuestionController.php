<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Category;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil pertanyaan beserta kategori (eager load) dan urutkan berdasarkan nama kategori lalu tanggal dibuat
        $questions = Question::query()
            ->with('categoryRef')
            ->leftJoin('categories', 'categories.id', '=', 'questions.category_id')
            ->select('questions.*')
            ->orderByRaw("COALESCE(categories.name, '') ASC")
            ->orderByDesc('questions.created_at')
            ->paginate(10);
        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.questions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:text,multiple_choice,rating',
            'category_id' => 'nullable|exists:categories,id',
            'is_required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Simpan pertanyaan baru
        Question::create([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'category' => null,
            'category_id' => $request->category_id,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Question::findOrFail($id);
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $question = Question::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        return view('admin.questions.edit', compact('question', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|in:text,multiple_choice,rating',
            'category_id' => 'nullable|exists:categories,id',
            'is_required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $question = Question::findOrFail($id);
        
        $question->update([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'category' => null,
            'category_id' => $request->category_id,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function toggle(Question $question)
    {
        $question->is_active = !$question->is_active;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Status pertanyaan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        
        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
