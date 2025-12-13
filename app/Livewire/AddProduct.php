<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Container\Attributes\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class AddProduct extends Component
{
    use WithPagination;
    use WithPagination, WithFileUploads;

    public $image;
    public $oldImage;
    public $search = '';
    public $product_id;
    public $product;
    public $description;
    public $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->product_id = $product->id;
        $this->product = $product->product;
        $this->description = $product->description;
        $this->oldImage = $product->image;

        $this->isEdit = true;
        $this->dispatch('open-modal');
    }


    public function submit()
    {
        $this->validate([
            'product' => 'required',
            'description' => 'required',
            'image' => $this->isEdit ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $imagePath = $this->oldImage;

        if ($this->image) {
            if ($this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }

            $imagePath = $this->image->store('products', 'public');
        }

        if ($this->isEdit) {
            Product::where('id', $this->product_id)->update([
                'product' => $this->product,
                'description' => $this->description,
                'image' => $imagePath,
            ]);

            session()->flash('success', 'Product updated');
        } else {
            Product::create([
                'product' => $this->product,
                'description' => $this->description,
                'image' => $imagePath,
            ]);

            session()->flash('success', 'Product added');
        }

        $this->reset(['product', 'description', 'image', 'oldImage', 'product_id', 'isEdit']);
        $this->dispatch('close-modal');
    }


    public function delete($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        session()->flash('success', 'Product deleted');
    }


    public function render()
    {
        $products = Product::where('product', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('livewire.add-product', compact('products'));
    }
}
