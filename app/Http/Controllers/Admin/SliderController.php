<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\SliderItem;
use App\Services\SliderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SliderController extends Controller
{
    public function __construct(protected SliderService $sliderService) {}

    // ─── Slider Group CRUD ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        Gate::authorize('slider.view');
        
        $query = Slider::withCount('items');

        // Tìm kiếm tên/key
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('key', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo khoảng ngày
        if ($request->filled('date')) {
            $dates = explode(' to ', $request->date);
            if (count($dates) == 2) {
                $query->whereDate('created_at', '>=', $dates[0])
                      ->whereDate('created_at', '<=', $dates[1]);
            } else {
                $query->whereDate('created_at', $dates[0]);
            }
        }

        $sliders = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        Gate::authorize('slider.create');
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('slider.create');
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'key'         => 'required|string|max:100|unique:sliders,key|regex:/^[a-z0-9\-_]+$/',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:active,inactive',
            'settings'    => 'nullable|array',
        ], [
            'key.regex' => 'Key chỉ được chứa chữ thường, số, dấu gạch ngang và gạch dưới.',
        ]);

        $this->sliderService->create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Tạo slider thành công!');
    }

    public function edit(Slider $slider)
    {
        Gate::authorize('slider.update');
        $slider->load(['items' => fn($q) => $q->orderBy('order')]);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        Gate::authorize('slider.update');
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'key'         => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-_]+$/', Rule::unique('sliders', 'key')->ignore($slider->id)],
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:active,inactive',
            'settings'    => 'nullable|array',
        ], [
            'key.regex' => 'Key chỉ được chứa chữ thường, số, dấu gạch ngang và gạch dưới.',
        ]);

        $this->sliderService->update($slider, $validated);

        return redirect()->route('admin.sliders.edit', $slider)
            ->with('success', 'Cập nhật slider thành công!');
    }

    public function destroy(Slider $slider)
    {
        Gate::authorize('slider.delete');
        $this->sliderService->delete($slider);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Xóa slider thành công!');
    }

    // ─── Slider Items CRUD (AJAX) ─────────────────────────────────────────────

    /**
     * Lưu item mới (tạo) qua AJAX từ Modal.
     */
    public function storeItem(Request $request, Slider $slider)
    {
        Gate::authorize('slider.update');
        $rules = array_merge(
            translatable_rules('title', 'nullable|string|max:255'),
            translatable_rules('description', 'nullable|string|max:500'),
            [
                'image'       => 'required|string',
                'link'        => 'nullable|url|max:500',
                'status'      => 'required|in:active,inactive',
                'is_highlight'=> 'boolean',
            ]
        );
        $validated = $request->validate($rules);

        $item = $this->sliderService->saveItem($slider, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm slide thành công!',
            'item'    => $item,
        ]);
    }

    /**
     * Cập nhật item đã có qua AJAX từ Modal.
     */
    public function updateItem(Request $request, Slider $slider, SliderItem $item)
    {
        Gate::authorize('slider.update');

        // Kiểm tra item thuộc về slider này
        abort_if($item->slider_id !== $slider->id, 403);

        $rules = array_merge(
            translatable_rules('title', 'nullable|string|max:255'),
            translatable_rules('description', 'nullable|string|max:500'),
            [
                'image'       => 'required|string',
                'link'        => 'nullable|url|max:500',
                'status'      => 'required|in:active,inactive',
                'is_highlight'=> 'boolean',
            ]
        );
        $validated = $request->validate($rules);

        $item = $this->sliderService->saveItem($slider, $validated, $item);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật slide thành công!',
            'item'    => $item,
        ]);
    }

    /**
     * Xóa item qua AJAX.
     */
    public function destroyItem(Slider $slider, SliderItem $item)
    {
        Gate::authorize('slider.delete');
        abort_if($item->slider_id !== $slider->id, 403);

        $this->sliderService->deleteItem($item);

        return response()->json(['success' => true, 'message' => 'Đã xóa slide!']);
    }

    /**
     * Cập nhật thứ tự kéo thả (Sortable.js).
     * Body: { orders: [{id: 1, order: 0}, {id: 2, order: 1}] }
     */
    public function reorderItems(Request $request, Slider $slider)
    {
        Gate::authorize('slider.update');
        $request->validate([
            'orders'          => 'required|array',
            'orders.*.id'     => 'required|integer',
            'orders.*.order'  => 'required|integer|min:0',
        ]);

        $this->sliderService->updateOrder($slider, $request->orders);

        return response()->json(['success' => true, 'message' => 'Đã lưu thứ tự!']);
    }
}
