<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AddProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_render_component()
    {
        Livewire::test(AddProduct::class)
            ->assertStatus(200)
            ->assertSee('Product Manager');
    }

    /** @test */
    public function can_create_product()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->create('product.jpg', 100);

        Livewire::test(AddProduct::class)
            ->set('form.product', 'New Product')
            ->set('form.description', 'This is a new product description.')
            ->set('form.image', $image)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Product added successfully.');

        $this->assertDatabaseHas('products', [
            'product' => 'New Product',
            'description' => 'This is a new product description.',
        ]);

        $product = Product::first();
        Storage::disk('public')->assertExists($product->image);
    }

    /** @test */
    public function can_update_product()
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'product' => 'Old Name',
            'image' => 'old-image.jpg'
        ]);

        $newImage = UploadedFile::fake()->create('new-product.jpg', 100);

        Livewire::test(AddProduct::class)
            ->call('edit', $product->id)
            ->set('form.product', 'Updated Name')
            ->set('form.image', $newImage)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Product updated successfully.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'product' => 'Updated Name',
        ]);

        // Old image should be deleted if new one is uploaded
        Storage::disk('public')->assertMissing('old-image.jpg');
    }

    /** @test */
    public function can_delete_product()
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'image' => 'to-be-deleted.jpg'
        ]);

        Livewire::test(AddProduct::class)
            ->call('delete', $product->id)
            ->assertSee('Product deleted successfully.');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('to-be-deleted.jpg');
    }

    /** @test */
    public function creates_validation_errors()
    {
        Livewire::test(AddProduct::class)
            ->set('form.product', '')
            ->call('submit')
            ->assertHasErrors(['form.product' => 'required']);
    }
}
