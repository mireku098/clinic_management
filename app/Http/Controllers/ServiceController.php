<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get services for API (for dropdown)
     */
    public function getServicesApi(Request $request)
    {
        try {
            $services = Service::where('status', 'active')
                ->select(['id', 'service_name', 'category', 'price'])
                ->orderBy('service_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading services: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Service::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('service_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Price range filter
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;
            if ($priceRange === '0-5000') {
                $query->whereBetween('price', [0, 5000]);
            } elseif ($priceRange === '5000-10000') {
                $query->whereBetween('price', [5000, 10000]);
            } elseif ($priceRange === '10000+') {
                $query->where('price', '>=', 10000);
            }
        }
        
        $services = $query->latest()->paginate(12);
        
        // Check if AJAX request
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('services.partials.service-grid', compact('services'))->render(),
                'pagination' => $services->links()->toHtml(),
                'count' => $services->total()
            ]);
        }
        
        return view('services', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('services.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreServiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Always generate unique service code
            $validated['service_code'] = $this->generateServiceCode();
            
            $service = Service::create($validated);
            
            // Check if request wants JSON (AJAX)
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Service created successfully!',
                    'service' => $service
                ]);
            }
            
            return redirect()->route('services.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => 'Service created successfully!'
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
            
        } catch (\Exception $e) {
            // Handle other errors for AJAX
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating service: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error creating service: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            
            $validated = $request->validate([
                'service_name' => 'required|string|max:255',
                'category' => 'required|string|max:100',
                'price' => 'required|numeric|min:0',
                'result_type' => 'required|in:text,numeric,file',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);
            
            $service->update($validated);
            
            // Check if request wants JSON (AJAX)
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Service updated successfully!',
                    'service' => $service
                ]);
            }
            
            return redirect()->route('services.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => 'Service updated successfully!'
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
            
        } catch (\Exception $e) {
            // Handle other errors for AJAX
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating service: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error updating service: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            
            // Toggle status instead of deleting
            $service->status = $service->status === 'active' ? 'inactive' : 'active';
            $service->save();
            
            // Check if request wants JSON (AJAX)
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => "Service {$service->status} successfully!",
                    'service' => $service
                ]);
            }
            
            return redirect()->route('services.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => "Service {$service->status} successfully!"
                ]);
                
        } catch (\Exception $e) {
            // Handle other errors for AJAX
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating service status: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error updating service status: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique service code.
     *
     * @return string
     */
    private function generateServiceCode()
    {
        do {
            $code = 'SVC-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (Service::where('service_code', $code)->exists());

        return $code;
    }
}
