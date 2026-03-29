<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Catalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Models\Uom;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_items');
        $query = Item::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")->orWhere('sku', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category); 
            });
        }

        $categories = Category::orderBy('name')->get();
        $items = $query->with(['activeUoms', 'catalogs'])->latest()->paginate(15)->withQueryString();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        Gate::authorize('create_items');
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();
        return view('admin.items.create', compact('categories', 'catalogs'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create_items');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'unique:items,sku'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:5'], // 最多5张图
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'catalogs' => ['nullable', 'array'],
            'catalogs.*' => ['exists:catalogs,id'],
            'uoms' => ['required', 'array', 'min:1'],
            'uoms.*.uom_name' => ['required', 'string', 'max:50'],
            'uoms.*.rate_qty' => ['required', 'numeric', 'min:1'],
            'uoms.*.price' => ['required', 'numeric', 'min:0'],
            'uoms.*.status' => ['required', 'in:active,inactive'],
        ], [
            'uoms.required' => __('You must initialize at least one packaging unit (Base Unit Rate 1).'),
            'images.max' => __('You can only upload up to 5 images.'),
        ]);

        // 【多图：保存前防撞名检查】
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $originalName = $file->getClientOriginalName();
                if (Storage::disk('public')->exists('items/' . $originalName)) {
                    return back()->withInput()->with('error', "Upload failed: Image '{$originalName}' already exists. Please rename it.");
                }
            }
        }

        $item = DB::transaction(function () use ($request, $validated) {
            $newItem = Item::create($request->only(['name', 'sku', 'description', 'status']));
            $uploadedPaths = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $path = $file->storeAs('items', $originalName, 'public');
                    $uploadedPaths[] = $path;
                }
                $newItem->update([
                    'images' => $uploadedPaths,
                    'image_path' => $uploadedPaths[0] ?? null // 把第一张图作为主图，兼容顾客端
                ]);
            }

            $newItem->categories()->sync($validated['categories'] ?? []);
            $newItem->catalogs()->sync($validated['catalogs'] ?? []);
            $newItem->uoms()->createMany($validated['uoms']);

            return $newItem;
        });

        return redirect()->route('items.index')->with('success', "Item ({$item->name}) added successfully !");
    }

    public function edit(Item $item): \Illuminate\View\View
    {
        Gate::authorize('edit_items');
        $item->load(['categories', 'catalogs', 'activeUoms']);
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();
        return view('admin.items.edit', compact('item', 'categories', 'catalogs'));
    }

    public function update(Request $request, Item $item): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit_items');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_images' => ['nullable', 'array'], // 接收要删除的旧图
            'categories' => ['nullable', 'array'],
            'catalogs' => ['nullable', 'array'],
            'uoms' => ['nullable', 'array'],
            'uoms.*.id' => ['nullable', 'exists:uoms,id'],
            'uoms.*.uom_name' => ['required_with:uoms', 'string', 'max:50'],
            'uoms.*.rate_qty' => ['required_with:uoms', 'numeric', 'min:1'],
            'uoms.*.price' => ['required_with:uoms', 'numeric', 'min:0'],
            'uoms.*.status' => ['required_with:uoms', 'in:active,inactive'], 
        ]);

        $currentImages = $item->images ?? [];
        if ($item->image_path && !in_array($item->image_path, $currentImages)) {
            $currentImages[] = $item->image_path;
        }

        // 【多图更新防撞名检查】
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $originalName = $file->getClientOriginalName();
                $checkPath = 'items/' . $originalName;
                if (Storage::disk('public')->exists($checkPath) && !in_array($checkPath, $currentImages)) {
                    return back()->withInput()->with('error', "Upload failed: Image '{$originalName}' already exists. Please rename it.");
                }
            }
        }

        DB::transaction(function () use ($item, $request, $validated, &$currentImages) {
            $item->update($request->only(['name', 'description', 'status']));

            // 1. 删除前端要求移除的旧图
            if (!empty($request->remove_images)) {
                foreach ($request->remove_images as $removePath) {
                    if (($key = array_search($removePath, $currentImages)) !== false) {
                        Storage::disk('public')->delete($removePath);
                        unset($currentImages[$key]);
                    }
                }
                $currentImages = array_values($currentImages); // 重新索引
            }

            // 2. 添加新图
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $path = $file->storeAs('items', $originalName, 'public');
                    if(!in_array($path, $currentImages)) {
                        $currentImages[] = $path;
                    }
                }
            }

            $item->update([
                'images' => $currentImages,
                'image_path' => $currentImages[0] ?? null, // 永远把第一张作为主图
            ]);

            $item->categories()->sync($validated['categories'] ?? []);
            $item->catalogs()->sync($validated['catalogs'] ?? []);

            // Process UOMs
            $incomingUomIds = collect($validated['uoms'] ?? [])->pluck('id')->filter()->toArray();
            $uomsToRemove = $item->uoms()->whereNotIn('id', $incomingUomIds)->get();
            foreach ($uomsToRemove as $oldUom) {
                if ($oldUom->orderItems()->exists()) {
                    $oldUom->update(['status' => 'inactive']);
                } else {
                    $oldUom->delete();
                }
            }
            foreach ($validated['uoms'] ?? [] as $uomData) {
                $item->uoms()->updateOrCreate(
                    ['id' => $uomData['id'] ?? null],
                    [
                        'uom_name' => strtoupper($uomData['uom_name']),
                        'rate_qty' => $uomData['rate_qty'],
                        'price' => $uomData['price'],
                        'status' => $uomData['status'] === 'active' ? 'active' : 'inactive',
                    ]
                );
            }
        });

        return redirect()->route('items.index')->with('success', "Product ({$item->name}) updated successfully !");
    }

    public function destroy(Item $item)
    {
        if (!$item->canBeDeleted()) {
            return redirect()->route('items.index')->with('error', "Product ({$item->name}) is linked to existing transactions.");
        }

        $images = $item->images ?? ($item->image_path ? [$item->image_path] : []);
        foreach($images as $path) {
            Storage::disk('public')->delete($path);
        }

        $itemName = $item->name;
        $item->delete();

        return redirect()->route('items.index')->with('success', "Product ({$itemName}) deleted successfully !");
    }

    public function show(Item $item): \Illuminate\View\View
    {
        Gate::authorize('view_items');
        $item->load(['categories', 'catalogs', 'uoms']);
        return view('admin.items.show', compact('item'));
    }
}