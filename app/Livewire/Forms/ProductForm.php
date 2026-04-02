<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $productModel = null;

    #[Validate('required|min:3|max:255')]
    public $product = '';

    #[Validate('required|min:10')]
    public $description = '';

    #[Validate('nullable|image|max:2048', as: 'product image')]
    public $image;

    public $oldImage;

    /**
     * Load existing product data into the form.
     */
    public function setProduct(Product $product)
    {
        $this->productModel = $product;
        $this->product = $product->product;
        $this->description = $product->description;
        $this->oldImage = $product->image;
    }

    /**
     * Store a new product.
     */
    public function store()
    {
        $this->validate();

        $imagePath = $this->image 
            ? $this->image->store('products', 'public') 
            : null;

        Product::create([
            'product' => $this->product,
            'description' => $this->description,
            'image' => $imagePath,
        ]);

        $this->reset();
    }

    /**
     * Update an existing product.
     */
    public function update()
    {
        $this->validate();

        $imagePath = $this->oldImage;

        if ($this->image) {
            if ($this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }
            $imagePath = $this->image->store('products', 'public');
        }

        $this->productModel->update([
            'product' => $this->product,
            'description' => $this->description,
            'image' => $imagePath,
        ]);

        $this->reset();
    }

    /**
     * Reset the form fields.
     */
    public function resetFields()
    {
        $this->reset(['product', 'description', 'image', 'oldImage', 'productModel']);
    }
}
