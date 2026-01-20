```blade
@extends('layouts.app')

@section('content')
<div style="max-width:1000px;margin:24px auto;padding:18px;background:#fff;border:1px solid #ddd;">
  <h2>Form SPD / Isi Surat</h2>

  <p>
    Kamu bisa mengisi manual di form ini, atau unggah file CSV (format seperti contoh) lalu klik "Parse CSV" untuk mengisi otomatis.
  </p>

  <input type="file" id="csvFile" accept=".csv" />
  <button id="parseCsv" type="button">Parse CSV</button>

  <form method="post" action="{{ route('spd.preview') }}" style="margin-top:12px;">
    @csrf

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <div style="flex:1; min-width:300px;">
        <label>Pejabat berwenang</label>
        <input type="text" name="pejabat" id="pejabat" class="form-control" />

        <label style="margin-top:8px;">Nama penerima</label>
        <input type="text" name="recipient_name" id="recipient_name" class="form-control" />

        <label style="margin-top:8px;">NIP penerima</label>
        <input type="text" name="recipient_nip" id="recipient_nip" class="form-control" />

        <label style="margin-top:8px;">Pangkat / Golongan</label>
        <input type="text" name="rank" id="rank" class="form-control" />

        <label style="margin-top:8px;">Jabatan / Instansi</label>
        <input type="text" name="position" id="position" class="form-control" />

        <label style="margin-top:8px;">Alat angkutan</label>
        <input type="text" name="transport" id="transport" class="form-control" />
      </div>

      <div style="flex:1; min-width:300px;">
        <label>Tempat Berangkat</label>
        <input type="text" name="from" id="from" class="form-control" />

        <label style="margin-top:8px;">Tempat Tujuan</label>
        <input type="text" name="to" id="to" class="form-control" />

        <label style="margin-top:8px;">Lamanya (hari)</label>
        <input type="text" name="days" id="days" class="form-control" />

        <label style="margin-top:8px;">Tanggal Berangkat</label>
        <input type="text" name="depart_date" id="depart_date" class="form-control" placeholder="22 Desember 2025" />

        <label style="margin-top:8px;">Tanggal Kembali</label>
        <input type="text" name="return_date" id="return_date" class="form-control" placeholder="24 Desember 2025" />
      </div>

      <div style="flex:1; min-width:300px;">
        <label>Lembar Ke</label>
        <input type="text" name="lembar_ke" id="lembar_ke" class="form-control" />

        <label style="margin-top:8px;">Kode No</label>
        <input type="text" name="kode_no" id="kode_no" class="form-control" />

        <label style="margin-top:8px;">Nomor SPD</label>
        <input type="text" name="nomor_spd" id="nomor_spd" class="form-control" />
      </div>
    </div>

    <div style="margin-top:12px;">
      <label>Untuk pembayaran / Maksud Perjalanan Dinas</label>
      <textarea name="purpose" id="purpose" rows="3" class="form-control" style="width:100%"></textarea>
    </div>

    <div style="display:flex; gap:12px; margin-top:12px;">
      <div style="flex:1">
        <label>Amount (Rp.)</label>
        <input type="text" name="amount" id="amount" class="form-control" />
      </div>
      <div style="flex:1">
        <label>Terbilang</label>
        <input type="text" name="amount_in_words" id="amount_in_words" class="form-control" />
      </div>
    </div>

    <div style="margin-top:12px;">
      <label>Kegiatan / Output / MAK</label>
      <input type="text" name="activity_mak" id="activity_mak" class="form-control" />
      <label style="margin-top:8px;">Kode</label>
      <input type="text" name="code" id="code" class="form-control" />
    </div>

    <h4 style="margin-top:12px;">Signatures (nama / nip / role)</h4>
    <div id="signatures">
      <!-- default 3 signature rows -->
      @for($i=0;$i<3;$i++)
      <div style="display:flex; gap:8px; margin-top:6px;">
        <input type="text" name="sign_name[]" placeholder="Nama" class="form-control" />
        <input type="text" name="sign_nip[]" placeholder="NIP" class="form-control" />
        <input type="text" name="sign_role[]" placeholder="Role (mis. Bendahara Pengeluaran)" class="form-control" />
      </div>
      @endfor
    </div>

    <div style="margin-top:14px;">
      <button type="submit">Preview / Print</button>
    </div>
  </form>
</div>

<script>
document.getElementById('parseCsv').addEventListener('click', function(){
  var f = document.getElementById('csvFile').files[0];
  if (!f) { alert('Pilih file CSV terlebih dahulu'); return; }
  var reader = new FileReader();
  reader.onload = function(e){
    var text = e.target.result;
    // simple CSV parse: split lines and commas
    var rows = text.split(/\r?\n/).map(r => r.split(','));
    // helper to find cell by content prefix
    function findCellContains(prefix){
      prefix = prefix.toString().trim().toLowerCase();
      for (var i=0;i<rows.length;i++){
        for (var j=0;j<rows[i].length;j++){
          var c = rows[i][j] ? rows[i][j].toString().trim().toLowerCase() : '';
          if (c.indexOf(prefix) !== -1) return {r:i,c:j};
        }
      }
      return null;
    }

    // map based on example CSV you provided
    // Pejabat (row with "Pejabat berwenang")
    var cell = findCellContains('pejabat berwenang');
    if(cell){
      var val = (rows[cell.r][cell.c+4] || '').trim();
      document.getElementById('pejabat').value = val;
    }

    // recipient name / NIP - search for "Nama / NIP"
    cell = findCellContains('nama / nip');
    if(cell){
      var val = (rows[cell.r][cell.c+3] || '').trim();
      // try to split name and nip
      var parts = val.split(/\n|\\n/);
      document.getElementById('recipient_name').value = (parts[0]||'').trim();
      // find NIP nearby
      // fallback: search for a cell that looks like a NIP (16 digits)
      var nipFound = '';
      for (var i=0;i<rows.length;i++){
        for (var j=0;j<rows[i].length;j++){
          var cellText = (rows[i][j]||'').trim();
          if (/\\d{15,18}/.test(cellText)) { nipFound = cellText.match(/\\d{15,18}/)[0]; break; }
        }
        if (nipFound) break;
      }
      document.getElementById('recipient_nip').value = nipFound;
    }

    // tempat berangkat / tujuan
    cell = findCellContains('tempat berangkat');
    if(cell) document.getElementById('from').value = (rows[cell.r][cell.c+3]||'').trim();
    cell = findCellContains('tempat tujuan');
    if(cell) document.getElementById('to').value = (rows[cell.r][cell.c+3]||'').trim();

    // tanggal berangkat, kembali
    cell = findCellContains('tanggal berangkat');
    if(cell) document.getElementById('depart_date').value = (rows[cell.r][cell.c+3]||'').trim();
    cell = findCellContains('tanggal harus kembali');
    if(cell) document.getElementById('return_date').value = (rows[cell.r][cell.c+3]||'').trim();

    // maksud / purpose - search "Maksud Perjalanan Dinas"
    cell = findCellContains('maksud perjalanan dinas');
    if(cell) document.getElementById('purpose').value = (rows[cell.r][cell.c+3]||'').trim();

    // activity / code
    cell = findCellContains('mata anggaran');
    if(cell) document.getElementById('activity_mak').value = (rows[cell.r][cell.c+3]||'').trim();
    cell = findCellContains('kode');
    if(cell) document.getElementById('code').value = (rows[cell.r][cell.c+1]||'').trim();

    // date of document (Dikeluarkan)
    cell = findCellContains('tanggal:');
    if(cell) {
      // try pick next column
      var v = (rows[cell.r][cell.c+1]||'').trim();
      // optional: set depart_date if empty
    }

    // signatures: search for Pejabat Pembuat Komitmen block
    cell = findCellContains('pejabat pembuat komitmen');
    if(cell){
      // try to find name and nip nearby (scan downward)
      for (var k=cell.r; k<Math.min(rows.length, cell.r+10); k++){
        for (var l=0;l<rows[k].length;l++){
          var t = (rows[k][l]||'').trim();
          if (t && /[A-Za-z]/.test(t) && t.indexOf('NIP')===-1){
            // first text name found (skip header)
            // fill first signature name if empty
            var sigInputs = document.querySelectorAll('input[name="sign_name[]"]');
            if(sigInputs.length>0 && !sigInputs[0].value) sigInputs[0].value = t;
          }
          if (t && /\\d{15,18}/.test(t)){
            var nip = t.match(/\\d{15,18}/)[0];
            var nipInputs = document.querySelectorAll('input[name="sign_nip[]"]');
            if(nipInputs.length>0 && !nipInputs[0].value) nipInputs[0].value = nip;
          }
        }
      }
    }

    alert('CSV parsed — periksa form, lalu klik Preview / Print.');
  };
  reader.readAsText(f);
});
</script>
@endsection
```