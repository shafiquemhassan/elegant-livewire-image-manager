<?php

namespace App\Livewire;

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AddProduct extends Component
{
    use WithPagination, WithFileUploads;

    public ProductForm $form;

    #[Url(history: true)]
    public $search = '';

    public bool $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    /**
     * Reset pagination when search query changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Load product data for editing.
     */
    public function edit(Product $product)
    {
        $this->form->setProduct($product);
        $this->isEdit = true;
        
        $this->dispatch('open-modal');
    }

    /**
     * Handle form submission for both create and update.
     */
    public function submit()
    {
        if ($this->isEdit) {
            $this->form->update();
            session()->flash('success', 'Product updated successfully.');
        } else {
            $this->form->store();
            session()->flash('success', 'Product added successfully.');
        }

        $this->reset(['isEdit']);
        $this->dispatch('close-modal');
    }

    /**
     * Delete a product and its associated image.
     */
    public function delete(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    /**
     * Reset the form when the modal is closed without saving.
     */
    public function resetForm()
    {
        $this->form->resetFields();
        $this->reset(['isEdit']);
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, function ($query) {
                $query->where('product', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('livewire.add-product', compact('products'));
    }
}
