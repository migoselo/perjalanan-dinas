<div class="card card-custom p-4 mb-4">
    <h5 class="section-title">B. Transportasi</h5>

    <div id="transport-container">
        <div id="transport-list">
            {{-- render old values (jika validasi gagal) --}}
            @php $oldTrans = old('transportations', []); @endphp
            @if(count($oldTrans))
                @foreach($oldTrans as $i => $t)
                <div class="transport-item small-card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="item-title">Transportasi {{ $loop->iteration }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-transport-btn">Hapus</button>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="small text-muted">Transportasi</label>
                            <input type="text" name="transportations[{{ $i }}][mode]" value="{{ $t['mode'] ?? '' }}" class="form-control form-control-custom" placeholder="Contoh: Pesawat, Kereta">
                        </div>

                        <div class="col-md-5">
                            <label class="small text-muted">Uraian</label>
                            <input type="text" name="transportations[{{ $i }}][description]" value="{{ $t['description'] ?? '' }}" class="form-control form-control-custom" placeholder="Detail perjalanan">
                        </div>

                        <div class="col-md-2">
                            <label class="small text-muted">Jumlah (Rp)</label>
                            <input type="number" name="transportations[{{ $i }}][amount]" value="{{ $t['amount'] ?? 0 }}" class="form-control form-control-custom" placeholder="0">
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- template (hidden) --}}
        <div id="transport-template" class="transport-item small-card p-3 mb-3 d-none template">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="item-title">Transportasi 1</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-transport-btn">Hapus</button>
            </div>

            <div class="row g-2">
                <div class="col-md-5">
                    <label class="small text-muted">Transportasi</label>
                    <input type="text" data-field="mode" data-name="transportations[][mode]" class="form-control form-control-custom" placeholder="Contoh: Pesawat, Kereta">
                </div>

                <div class="col-md-5">
                    <label class="small text-muted">Uraian</label>
                    <input type="text" data-field="description" data-name="transportations[][description]" class="form-control form-control-custom" placeholder="Detail perjalanan">
                </div>

                <div class="col-md-2">
                    <label class="small text-muted">Jumlah (Rp)</label>
                    <input type="number" data-field="amount" data-name="transportations[][amount]" class="form-control form-control-custom" value="0" placeholder="0">
                </div>
            </div>
        </div>

        <div class="mb-0">
            <div id="add-transport-btn" class="add-button mt-2">
                <strong>＋ Tambah Transportasi</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('transport-list');
    const template = document.getElementById('transport-template');
    const addBtn = document.getElementById('add-transport-btn');

    function updateIndexes() {
        const items = Array.from(list.querySelectorAll('.transport-item:not(.template)'));
        items.forEach((item, idx) => {
            const title = item.querySelector('.item-title');
            if (title) title.textContent = 'Transportasi ' + (idx + 1);

            item.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                el.name = `transportations[${idx}][${field}]`;
            });
        });
    }

    function addTransport(values = {}) {
        const clone = template.cloneNode(true);
        clone.classList.remove('d-none', 'template');
        clone.removeAttribute('id');

        // set values if passed
        clone.querySelectorAll('[data-field]').forEach(el => {
            const field = el.getAttribute('data-field');
            if (values[field] !== undefined) el.value = values[field];
            else el.value = '';
        });

        list.appendChild(clone);
        updateIndexes();
    }

    // add default one if none (and no old inputs)
    if (list.querySelectorAll('.transport-item:not(.template)').length === 0) {
        addTransport();
    }

    addBtn.addEventListener('click', function () {
        addTransport();
    });

    list.addEventListener('click', function (e) {
        if (e.target.closest('.remove-transport-btn')) {
            const btn = e.target.closest('.remove-transport-btn');
            const item = btn.closest('.transport-item');
            if (item) item.remove();
            updateIndexes();
        }
    });
});
</script>
@endpush