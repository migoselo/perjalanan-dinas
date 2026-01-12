<div class="card card-custom p-4 mb-4">
    <h5 class="section-title">C. Penginapan</h5>

    <div id="hotel-container">
        <div id="hotel-list">
            @php $oldHotels = old('accommodations', []); @endphp
            @if(count($oldHotels))
                @foreach($oldHotels as $i => $h)
                <div class="hotel-item small-card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="item-title">Penginapan {{ $loop->iteration }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-hotel-btn">Hapus</button>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="small text-muted">Nama Penginapan</label>
                            <input type="text" name="accommodations[{{ $i }}][name]" value="{{ $h['name'] ?? '' }}" class="form-control form-control-custom" placeholder="Nama hotel/penginapan">
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted">Lama Hari</label>
                            <input type="number" name="accommodations[{{ $i }}][nights]" value="{{ $h['nights'] ?? 0 }}" class="form-control form-control-custom" placeholder="0">
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted">Harga (Rp)</label>
                            <input type="number" name="accommodations[{{ $i }}][price]" value="{{ $h['price'] ?? 0 }}" class="form-control form-control-custom" placeholder="0">
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- template --}}
        <div id="hotel-template" class="hotel-item small-card p-3 mb-3 d-none template">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="item-title">Penginapan 1</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-hotel-btn">Hapus</button>
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="small text-muted">Nama Penginapan</label>
                    <input type="text" data-field="name" data-name="accommodations[][name]" class="form-control form-control-custom" placeholder="Nama hotel/penginapan">
                </div>

                <div class="col-md-3">
                    <label class="small text-muted">Lama Hari</label>
                    <input type="number" data-field="nights" data-name="accommodations[][nights]" class="form-control form-control-custom" value="0" placeholder="0">
                </div>

                <div class="col-md-3">
                    <label class="small text-muted">Harga (Rp)</label>
                    <input type="number" data-field="price" data-name="accommodations[][price]" class="form-control form-control-custom" value="0" placeholder="0">
                </div>
            </div>
        </div>

        <div class="mb-0">
            <div id="add-hotel-btn" class="add-button mt-2">
                <strong>＋ Tambah Penginapan</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('hotel-list');
    const template = document.getElementById('hotel-template');
    const addBtn = document.getElementById('add-hotel-btn');

    function updateIndexes() {
        const items = Array.from(list.querySelectorAll('.hotel-item:not(.template)'));
        items.forEach((item, idx) => {
            const title = item.querySelector('.item-title');
            if (title) title.textContent = 'Penginapan ' + (idx + 1);

            item.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                el.name = `accommodations[${idx}][${field}]`;
            });
        });
    }

    function addHotel(values = {}) {
        const clone = template.cloneNode(true);
        clone.classList.remove('d-none', 'template');
        clone.removeAttribute('id');

        clone.querySelectorAll('[data-field]').forEach(el => {
            const field = el.getAttribute('data-field');
            if (values[field] !== undefined) el.value = values[field];
            else el.value = '';
        });

        list.appendChild(clone);
        updateIndexes();
    }

    if (list.querySelectorAll('.hotel-item:not(.template)').length === 0) {
        addHotel();
    }

    addBtn.addEventListener('click', function () {
        addHotel();
    });

    list.addEventListener('click', function (e) {
        if (e.target.closest('.remove-hotel-btn')) {
            const btn = e.target.closest('.remove-hotel-btn');
            const item = btn.closest('.hotel-item');
            if (item) item.remove();
            updateIndexes();
        }
    });
});
</script>
@endpush