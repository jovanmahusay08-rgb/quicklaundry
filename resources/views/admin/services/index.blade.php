@extends('admin.layouts.app')

@section('title', 'Services')
@section('pageTitle', 'Services')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Create Service</h2>
        <form method="POST" action="{{ route('admin.services.store') }}" class="grid md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Service Name</label>
                <input type="text" name="service_name" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Base Price</label>
                <input type="number" step="0.01" name="base_price" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Price Per Kilo</label>
                <input type="number" step="0.01" name="price_per_kilo" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-600 mb-1">Pricing Type</label>
                <select name="pricing_type" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="variable">Variable (base + extra kilos)</option>
                    <option value="flat">Flat (fixed price regardless of weight)</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button class="rounded-lg bg-brand-600 px-4 py-2 text-white">Save Service</button>
            </div>
        </form>
    </div>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($services as $service)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">{{ $service->service_name }}</h3>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs text-emerald-700">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ $service->description }}</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Base Price</span><span class="font-medium text-slate-800">₱{{ number_format($service->base_price, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Per Kilo</span><span class="font-medium text-slate-800">₱{{ number_format($service->price_per_kilo, 2) }}</span></div>
                </div>
                <div class="mt-5 flex gap-2">
                    <button type="button" onclick="openEditModal({{ $service->id }}, '{{ $service->service_name }}', '{{ $service->description }}', {{ $service->base_price }}, {{ $service->price_per_kilo }}, '{{ $service->pricing_type }}')" class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">Edit</button>
                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this service?')" class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-400">No services found.</div>
        @endforelse
    </div>

    <div class="{{ $services->hasPages() ? 'flex justify-center' : '' }}">
        {{ $services->links() }}
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl border border-slate-200 p-6 w-full max-w-md mx-4">
        <h2 class="font-semibold text-slate-800 mb-4">Edit Service</h2>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Service Name</label>
                <input type="text" id="editServiceName" name="service_name" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Description</label>
                <input type="text" id="editDescription" name="description" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Base Price</label>
                <input type="number" step="0.01" id="editBasePrice" name="base_price" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Price Per Kilo</label>
                <input type="number" step="0.01" id="editPricePerKilo" name="price_per_kilo" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Pricing Type</label>
                <select id="editPricingType" name="pricing_type" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="variable">Variable (base + extra kilos)</option>
                    <option value="flat">Flat (fixed price regardless of weight)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeEditModal()" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700 transition">Update Service</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(serviceId, serviceName, description, basePrice, pricePerKilo, pricingType) {
    document.getElementById('editServiceName').value = serviceName;
    document.getElementById('editDescription').value = description;
    document.getElementById('editBasePrice').value = basePrice;
    document.getElementById('editPricePerKilo').value = pricePerKilo;
    document.getElementById('editPricingType').value = pricingType;
    document.getElementById('editForm').action = `/admin/services/${serviceId}`;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('editModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
