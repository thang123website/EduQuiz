<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FormSubmissionController extends Controller
{
    public function index(Request $request)
    {
        // Add gate check if needed, e.g., Gate::authorize('form_submission.view');
        
        $query = FormSubmission::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.form_submissions.index', compact('submissions'));
    }

    public function show(FormSubmission $form)
    {
        // Gate::authorize('form_submission.view');
        return view('admin.form_submissions.show', compact('form'));
    }

    public function updateStatus(Request $request, FormSubmission $form)
    {
        // Gate::authorize('form_submission.update');
        $request->validate([
            'status' => 'required|string',
        ]);

        $form->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái form.');
    }

    public function destroy(FormSubmission $form)
    {
        // Gate::authorize('form_submission.delete');
        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Đã xóa form.');
    }
}
