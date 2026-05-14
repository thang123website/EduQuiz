<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizPart;
use Illuminate\Http\Request;

class QuizPartController extends Controller
{
    public function store(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $orderIdx = $quiz->parts()->max('order_idx') + 1;
        
        $quiz->parts()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order_idx' => $orderIdx
        ]);
        
        return back()->with('success', 'Đã thêm Part mới thành công.');
    }
    
    public function update(Request $request, $id)
    {
        $part = QuizPart::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $part->update($validated);
        
        return back()->with('success', 'Cập nhật Part thành công.');
    }
    
    public function destroy($id)
    {
        $part = QuizPart::findOrFail($id);
        $part->delete();
        
        return back()->with('success', 'Đã xóa Part thành công.');
    }

    public function reorder(Request $request)
    {
        $orderedIds = $request->input('ordered_ids');
        if (is_array($orderedIds)) {
            foreach ($orderedIds as $index => $id) {
                QuizPart::where('id', $id)->update(['order_idx' => $index + 1]);
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
    }
}
