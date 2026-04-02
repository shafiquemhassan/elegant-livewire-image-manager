<div class="container py-5">
    <!-- Premium Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-0">Product Manager</h2>
            <p class="text-muted mb-0">Manage your product catalog with ease</p>
        </div>
        <button type="button" class="btn btn-primary px-4 shadow-sm" 
            data-bs-toggle="modal" data-bs-target="#addProduct" wire:click="resetForm">
            <i class="bi bi-plus-lg me-2"></i> Add New Product
        </button>
    </div>

    <!-- Notifications -->
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Simple Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" wire:model.live="search" 
                    class="form-control border-start-0 ps-0" 
                    placeholder="Search by name or description...">
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase fs-xs fw-bold text-muted">ID</th>
                        <th class="py-3 text-uppercase fs-xs fw-bold text-muted">Product</th>
                        <th class="py-3 text-uppercase fs-xs fw-bold text-muted">Description</th>
                        <th class="py-3 text-uppercase fs-xs fw-bold text-muted">Image</th>
                        <th class="py-3 text-uppercase fs-xs fw-bold text-muted text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr>
                        <td class="ps-4 text-muted">#{{ $product->id }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $product->product }}</span>
                        </td>
                        <td>
                            <span class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                {{ $product->description }}
                            </span>
                        </td>
                        <td>
                            @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" 
                                 class="rounded shadow-sm border" 
                                 width="48" height="48" style="object-fit: cover;">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center border" 
                                 style="width: 48px; height: 48px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning border-0 me-1"
                                wire:click="edit({{ $product->id }})">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0"
                                onclick="confirm('Delete this product?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $product->id }})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-inbox text-muted fs-1 mb-3 d-block"></i>
                                <span class="text-muted">No products found for "{{ $search }}"</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    <!-- Modal for Add/Edit -->
    <div class="modal fade" id="addProduct" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        {{ $isEdit ? 'Edit Product' : 'Add New Product' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>

                <form wire:submit.prevent="submit">
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name</label>
                            <input type="text" wire:model="form.product" 
                                class="form-control @error('form.product') is-invalid @enderror" 
                                placeholder="Enter product name">
                            @error('form.product')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea wire:model="form.description" 
                                class="form-control @error('form.description') is-invalid @enderror" 
                                rows="3" placeholder="Describe the product"></textarea>
                            @error('form.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Product Image</label>
                            <div class="input-group mb-2">
                                <input type="file" wire:model="form.image" 
                                    class="form-control @error('form.image') is-invalid @enderror" 
                                    id="uploadImage">
                            </div>
                            @error('form.image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Area -->
                            <div class="mt-3 text-center p-3 border rounded bg-light position-relative">
                                <div wire:loading wire:target="form.image" class="position-absolute top-50 start-50 translate-middle" style="z-index: 10;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                
                                @if ($form->image)
                                    <img src="{{ $form->image->temporaryUrl() }}" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                    <div class="mt-1 small text-success"><i class="bi bi-check-circle"></i> Image ready for upload</div>
                                @elseif ($form->oldImage)
                                    <img src="{{ asset('storage/'.$form->oldImage) }}" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                    <div class="mt-1 small text-muted">Current Image</div>
                                @else
                                    <div class="text-muted py-4">
                                        <i class="bi bi-cloud-arrow-up fs-2"></i>
                                        <p class="mb-0 mt-2 small">Select an image to preview</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" wire:click="resetForm">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm me-1"></span>
                            {{ $isEdit ? 'Update Product' : 'Save Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Styles for specific UI elements -->
    <style>
        .fs-xs { font-size: 0.75rem; }
        .card { border-radius: 0.75rem; }
        .btn { border-radius: 0.5rem; transition: all 0.2s; }
        .form-control { border-radius: 0.5rem; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); border-color: #86b7fe; }
        .table thead th { border-top: 0; }
        .table tbody tr { transition: background-color 0.15s; }
    </style>

    <script>
        window.addEventListener('close-modal', () => {
            const modalElement = document.getElementById('addProduct');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
        });

        window.addEventListener('open-modal', () => {
            const modalElement = document.getElementById('addProduct');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        });
    </script>
</div>