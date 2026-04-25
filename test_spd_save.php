<?php
// Test script untuk debug SPD save

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Travel;
use App\Models\SPBY;

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test buat Travel baru
    $travel = new Travel();
    $travel->user_id = 1;
    $travel->nomor_spd = 'TEST-' . date('YmdHis');
    $travel->save();
    echo "✅ Travel created with ID: " . $travel->id . "\n";
    
    // Test buat SPBY
    $spby = new SPBY();
    $spby->travel_id = $travel->id;
    $spby->nomor_spd = $travel->nomor_spd;
    $spby->nama_pegawai = 'Test Pegawai';
    $spby->nip_pegawai = '123456789';
    $spby->pangkat = 'Penata Muda';
    $spby->jabatan = 'Analis';
    $spby->save();
    echo "✅ SPBY created with ID: " . $spby->id . "\n";
    
    // Test retrieve
    $travel_loaded = Travel::with('spby')->find($travel->id);
    if ($travel_loaded && $travel_loaded->spby) {
        echo "✅ Data retrieved successfully!\n";
        echo "   Travel ID: " . $travel_loaded->id . "\n";
        echo "   SPBY ID: " . $travel_loaded->spby->id . "\n";
        echo "   SPBY Nama: " . $travel_loaded->spby->nama_pegawai . "\n";
    } else {
        echo "❌ Data tidak bisa diambil kembali\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
?>
