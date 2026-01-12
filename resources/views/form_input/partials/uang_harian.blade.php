<div class="card card-custom p-4 mb-4">
    <h5 class="section-title">D. Uang Harian</h5>

    <div id="perdiem-container">
        <div id="perdiem-list">
            {{-- render old values (jika validasi gagal) --}}
            @php $oldPerdiems = old('perdiems', []); @endphp    
            @if(count($oldPerdiems))
                @foreach($oldPerdiems as $i => $p)
                <div class="perdiem-item small-card p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="item-title">Uang Harian {{ $loop->iteration }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-perdiem-btn">Hapus</button>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="small text-muted">Nama Kota</label>
                            <input type="text" name="perdiems[{{ $i }}][city]" value="{{ $p['city'] ?? '' }}" class="form-control form-control-custom" placeholder="Nama kota (contoh: Jakarta)">
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted">Jumlah Hari</label>
                            <input type="number" min="0" name="perdiems[{{ $i }}][days]" value="{{ $p['days'] ?? 0 }}" class="form-control form-control-custom" placeholder="0">
                        </div>

                        <div class="col-md-3">
                            <label class="small text-muted">Uang Harian (Rp/hari)</label>
                            <input type="number" min="0" name="perdiems[{{ $i }}][amount]" value="{{ $p['amount'] ?? 0 }}" class="form-control form-control-custom" placeholder="0">
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- template --}}
        <div id="perdiem-template" class="perdiem-item small-card p-3 mb-3 d-none template">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="item-title">Uang Harian 1</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-perdiem-btn">Hapus</button>
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="small text-muted">Nama Kota</label>
                    <input type="text" data-field="city" class="form-control form-control-custom" placeholder="Nama kota (contoh: Jakarta)">
                </div>

                <div class="col-md-3">
                    <label class="small text-muted">Jumlah Hari</label>
                    <input type="number" min="0" data-field="days" class="form-control form-control-custom" value="0" placeholder="0">
                </div>

                <div class="col-md-3">
                    <label class="small text-muted">Uang Harian (Rp/hari)</label>
                    <input type="number" min="0" data-field="amount" class="form-control form-control-custom" value="0" placeholder="0">
                </div>
            </div>
        </div>

        <div class="mb-0">
            <div id="add-perdiem-btn" class="add-button mt-2">
                <strong>＋ Tambah Uang Harian</strong>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('perdiem-list');
    const template = document.getElementById('perdiem-template');
    const addBtn = document.getElementById('add-perdiem-btn');

    function updateIndexes() {
        const items = Array.from(list.querySelectorAll('.perdiem-item:not(.template)'));
        items.forEach((item, idx) => {
            const title = item.querySelector('.item-title');
            if (title) title.textContent = 'Uang Harian ' + (idx + 1);

            item.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                el.name = `perdiems[${idx}][${field}]`;
            });
        });
    }

    function addPerdiem(values = {}) {
        const clone = template.cloneNode(true);
        clone.classList.remove('d-none', 'template');
        clone.removeAttribute('id');

        clone.querySelectorAll('[data-field]').forEach(el => {
            const field = el.getAttribute('data-field');
            if (values[field] !== undefined) el.value = values[field];
            else {
                if (field === 'days' || field === 'amount') el.value = 0;
                else el.value = '';
            }
        });

        list.appendChild(clone);
        updateIndexes();
    }

    // add default one if empty
    if (list.querySelectorAll('.perdiem-item:not(.template)').length === 0) {
        addPerdiem();
    }

    addBtn.addEventListener('click', function () {
        addPerdiem();
    });

    list.addEventListener('click', function (e) {
        if (e.target.closest('.remove-perdiem-btn')) {
            const btn = e.target.closest('.remove-perdiem-btn');
            const item = btn.closest('.perdiem-item');
            if (item) item.remove();
            updateIndexes();
        }
    });
});
</script>
@endpush