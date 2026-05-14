<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->paginate(15);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::all();
        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:5',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'gender_type' => 'required|in:male,female,unisex',
            'description' => 'nullable|string',
            'is_popular' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['branch_id'] = session('active_branch_id', 1);
        $validated['is_active'] = true;
        
        $validated['is_popular'] = $request->has('is_popular');
        $validated['is_featured'] = $request->has('is_featured');

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Hizmet başarıyla eklendi.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();
        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:5',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'gender_type' => 'required|in:male,female,unisex',
            'description' => 'nullable|string',
            'is_popular' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        $validated['is_popular'] = $request->has('is_popular');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Hizmet başarıyla güncellendi.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Hizmet silindi.');
    }

    public function toggleStatus(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return response()->json(['success' => true, 'is_active' => $service->is_active]);
    }
}
