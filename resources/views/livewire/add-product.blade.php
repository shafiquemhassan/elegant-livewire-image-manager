<div class="container my-3 py-3">
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProduct">
        Add Product
    </button>

    @session("success")
    <div class="alert alert-success alert-dismissible fade show my-3" role="alert">
        <strong>{{ $value }}</strong> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endsession



    <!-- Modal -->
    <div class="modal fade" id="addProduct" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">
                        {{ $isEdit ? 'Edit Product' : 'Add Product' }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form wire:submit.prevent="submit">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" wire:model.defer="product" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Description</label>
                            <textarea wire:model.defer="description" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" wire:model="image" class="form-control">
                        </div>

                        @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" width="120">
                        @elseif ($oldImage)
                        <img src="{{ asset('storage/'.$oldImage) }}" class="img-thumbnail" width="120">
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="my-3">
        <input type="text" wire:model.live="search" class="form-control" placeholder="Search products...">
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Product</th>
                <th scope="col">Description</th>
                <th scope="col">Image</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->product }}</td>
                <td>{{ $product->description }}</td>
                <td>
                    @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}" width="60">
                    @endif
                </td>

                <td>
                    <button class="btn btn-sm btn-warning"
                        wire:click="edit({{ $product->id }})">
                        Edit
                    </button>

                    <button class="btn btn-sm btn-danger"
                        onclick="confirm('Delete this product?') || event.stopImmediatePropagation()"
                        wire:click="delete({{ $product->id }})">
                        Delete
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No products found</td>
            </tr>
            @endforelse
        </tbody>


    </table>
    <div class="mt-3">
        {{ $products->links() }}
    </div>

</div>

<script>
    window.addEventListener('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('addProduct'));
        modal.hide();
    });

    window.addEventListener('open-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('addProduct'));
        modal.show();
    });
</script>