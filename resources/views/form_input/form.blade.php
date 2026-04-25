@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-1">Form Input Perjalanan Dinas</h2>
    <p class="text-muted">Silakan lengkapi data perjalanan dinas Anda</p>

    <form action="{{ route('travel.store') }}" method="POST">
        @csrf

        {{-- A. Identitas (partial sederhana, bisa tetap dipisah) --}}
        @include('form_input.partials.identitas')

        {{-- B. Transportasi --}}
        @include('form_input.partials.transportasi')

        {{-- C. Penginapan --}}
        @include('form_input.partials.penginapan')

        {{-- D. Uang Harian (bisa dibuat partial juga jika perlu) --}}
        @include('form_input.partials.uang_harian')

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>
</div>

<script>
// NIP Input Handler - Auto clean spasi dan hanya angka
const nipInput = document.getElementById('nip_input');
const nipCounter = document.getElementById('nip_counter');

if (nipInput) {
    nipInput.addEventListener('input', function(e) {
        // Remove semua non-digit characters
        let value = e.target.value.replace(/\D/g, '');
        
        // Limit to 18 digits
        value = value.substring(0, 18);
        
        // Update input
        e.target.value = value;
        
        // Update counter
        if (nipCounter) {
            const remaining = 18 - value.length;
            if (value.length === 18) {
                nipCounter.textContent = `${value.length}/18 ✓`;
                nipCounter.style.color = '#28a745';
            } else {
                nipCounter.textContent = `${value.length}/18 (${remaining} digit lagi)`;
                nipCounter.style.color = '#17a2b8';
            }
        }
    });
    
    // Initial counter update
    const initialValue = nipInput.value;
    if (nipCounter) {
        if (initialValue.length === 18) {
            nipCounter.textContent = `${initialValue.length}/18 ✓`;
            nipCounter.style.color = '#28a745';
        } else {
            nipCounter.textContent = `${initialValue.length}/18`;
        }
    }
}

// ⭐ INITIALIZATION - Setup all form arrays when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Form initialization started...');
    
    // ============ TRANSPORTASI SETUP ============
    const transportList = document.getElementById('transport-list');
    const transportTemplate = document.getElementById('transport-template');
    const addTransportBtn = document.getElementById('add-transport-btn');
    
    if (transportList && transportTemplate) {
        console.log('✅ Transport list found');
        
        function updateTransportIndexes() {
            const items = Array.from(transportList.querySelectorAll('.transport-item:not(.template)'));
            console.log(`🔄 Updating ${items.length} transport items`);
            items.forEach((item, idx) => {
                const title = item.querySelector('.item-title');
                if (title) title.textContent = 'Transportasi ' + (idx + 1);
                
                item.querySelectorAll('[data-field]').forEach(el => {
                    const field = el.getAttribute('data-field');
                    const newName = `transportations[${idx}][${field}]`;
                    el.name = newName;
                    console.log(`  ✏️  ${field} → ${newName}`);
                });
            });
        }
        
        function addTransport(values = {}) {
            const clone = transportTemplate.cloneNode(true);
            clone.classList.remove('d-none', 'template');
            clone.removeAttribute('id');
            
            clone.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                if (values[field] !== undefined) el.value = values[field];
                else el.value = '';
            });
            
            transportList.appendChild(clone);
            updateTransportIndexes();
            console.log('➕ Transport item added');
        }
        
        // Initial setup
        if (transportList.querySelectorAll('.transport-item:not(.template)').length === 0) {
            addTransport();
        } else {
            updateTransportIndexes();
        }
        
        addTransportBtn?.addEventListener('click', function() {
            addTransport();
        });
        
        transportList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-transport-btn')) {
                e.target.closest('.transport-item')?.remove();
                updateTransportIndexes();
                console.log('🗑️  Transport item removed');
            }
        });
    }
    
    // ============ PENGINAPAN SETUP ============
    const hotelList = document.getElementById('hotel-list');
    const hotelTemplate = document.getElementById('hotel-template');
    const addHotelBtn = document.getElementById('add-hotel-btn');
    
    if (hotelList && hotelTemplate) {
        console.log('✅ Hotel list found');
        
        function updateHotelIndexes() {
            const items = Array.from(hotelList.querySelectorAll('.hotel-item:not(.template)'));
            console.log(`🔄 Updating ${items.length} hotel items`);
            items.forEach((item, idx) => {
                const title = item.querySelector('.item-title');
                if (title) title.textContent = 'Penginapan ' + (idx + 1);
                
                item.querySelectorAll('[data-field]').forEach(el => {
                    const field = el.getAttribute('data-field');
                    const newName = `accommodations[${idx}][${field}]`;
                    el.name = newName;
                    console.log(`  ✏️  ${field} → ${newName}`);
                });
            });
        }
        
        function addHotel(values = {}) {
            const clone = hotelTemplate.cloneNode(true);
            clone.classList.remove('d-none', 'template');
            clone.removeAttribute('id');
            
            clone.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                if (values[field] !== undefined) el.value = values[field];
                else el.value = '';
            });
            
            hotelList.appendChild(clone);
            updateHotelIndexes();
            console.log('➕ Hotel item added');
        }
        
        // Initial setup
        if (hotelList.querySelectorAll('.hotel-item:not(.template)').length === 0) {
            addHotel();
        } else {
            updateHotelIndexes();
        }
        
        addHotelBtn?.addEventListener('click', function() {
            addHotel();
        });
        
        hotelList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-hotel-btn')) {
                e.target.closest('.hotel-item')?.remove();
                updateHotelIndexes();
                console.log('🗑️  Hotel item removed');
            }
        });
    }
    
    // ============ PERDIEM SETUP ============
    const perdiemList = document.getElementById('perdiem-list');
    const perdiemTemplate = document.getElementById('perdiem-template');
    const addPerdiemBtn = document.getElementById('add-perdiem-btn');
    
    if (perdiemList && perdiemTemplate) {
        console.log('✅ Perdiem list found');
        
        function updatePerdiemIndexes() {
            const items = Array.from(perdiemList.querySelectorAll('.perdiem-item:not(.template)'));
            console.log(`🔄 Updating ${items.length} perdiem items`);
            items.forEach((item, idx) => {
                const title = item.querySelector('.item-title');
                if (title) title.textContent = 'Uang Harian ' + (idx + 1);
                
                item.querySelectorAll('[data-field]').forEach(el => {
                    const field = el.getAttribute('data-field');
                    const newName = `perdiems[${idx}][${field}]`;
                    el.name = newName;
                    console.log(`  ✏️  ${field} → ${newName}`);
                });
            });
        }
        
        function addPerdiem(values = {}) {
            const clone = perdiemTemplate.cloneNode(true);
            clone.classList.remove('d-none', 'template');
            clone.removeAttribute('id');
            
            clone.querySelectorAll('[data-field]').forEach(el => {
                const field = el.getAttribute('data-field');
                if (values[field] !== undefined) el.value = values[field];
                else el.value = '';
            });
            
            perdiemList.appendChild(clone);
            updatePerdiemIndexes();
            console.log('➕ Perdiem item added');
        }
        
        // Initial setup
        if (perdiemList.querySelectorAll('.perdiem-item:not(.template)').length === 0) {
            addPerdiem();
        } else {
            updatePerdiemIndexes();
        }
        
        addPerdiemBtn?.addEventListener('click', function() {
            addPerdiem();
        });
        
        perdiemList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-perdiem-btn')) {
                e.target.closest('.perdiem-item')?.remove();
                updatePerdiemIndexes();
                console.log('🗑️  Perdiem item removed');
            }
        });
    }
    
    console.log('✅ Form initialization complete!');
});

// Form Submit Debug
const form = document.querySelector('form[action="{{ route('travel.store') }}"]');
if (form) {
    form.addEventListener('submit', function(e) {
        console.log('📝 Form submitted!');
        
        // Check all input names
        console.log('🔍 All form inputs:');
        form.querySelectorAll('input, textarea, select').forEach(input => {
            if (input.name) {
                console.log(`  ${input.name} = ${input.value}`);
            }
        });
        
        // Verify array indices
        console.log('📊 Data Summary:');
        console.log(`  Transportations: ${form.querySelectorAll('[name^="transportations"]').length} fields`);
        console.log(`  Accommodations: ${form.querySelectorAll('[name^="accommodations"]').length} fields`);
        console.log(`  Perdiems: ${form.querySelectorAll('[name^="perdiems"]').length} fields`);
    });
}
</script>
@endsection