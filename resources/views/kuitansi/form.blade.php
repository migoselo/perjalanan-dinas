@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            max-width: 700px;
            margin: 0 auto;
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #0056b3 0%, #0047AB 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: -0.5px;
        }

        .form-header p {
            margin: 0;
            font-size: 15px;
            opacity: 0.95;
        }

        .form-body {
            padding: 40px;
        }

        .info-banner {
            background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
            border-left: 5px solid #0056b3;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 30px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-banner-icon {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-banner p {
            margin: 0;
            color: #004085;
            font-size: 14px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
        }

        .form-control {
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fafbfc;
        }

        .form-control:focus {
            border-color: #0056b3;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 86, 179, 0.1);
        }

        .form-text {
            font-size: 13px;
            color: #6c757d;
            margin-top: 8px;
            display: block;
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0056b3 0%, #0047AB 100%);
            border: none;
            color: white;
            padding: 12px 32px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 140px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #004499 0%, #003d94 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 56, 179, 0.25);
            color: white;
            text-decoration: none;
        }

        .btn-cancel {
            background: white;
            border: 2px solid #dee2e6;
            color: #6c757d;
            padding: 10px 28px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 140px;
            display: inline-block;
            text-decoration: none;
        }

        .btn-cancel:hover {
            border-color: #6c757d;
            color: #495057;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
    </style>

    <div class="form-card">
        <!-- Header -->
        <div class="form-header">
            <h2>📋 Input Data Kuitansi</h2>
            <p>Pegawai: <strong>{{ $travel->nama_pegawai }}</strong></p>
        </div>

        <!-- Body -->
        <div class="form-body">
            <!-- Info Banner -->
            <div class="info-banner">
                <div class="info-banner-icon">ℹ️</div>
                <p>Isi semua data di bawah ini. Data akan tersimpan dan ditampilkan di halaman kuitansi.</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('kuitansi.storeData', $travel->id) }}">
                @csrf

                <div class="form-group">
                    <label for="pemberi_uang">Sudah Terima Dari</label>
                    <input type="text" id="pemberi_uang" name="pemberi_uang" class="form-control"
                           value="{{ $travel->pemberi_uang ?? 'Pejabat Pembuat Komitmen Loka Monitor Spektrum Frekuensi Radio Tanjung Selor' }}"
                           placeholder="Masukkan nama pemberi uang" required>
                    <small class="form-text">Contoh: Pejabat Pembuat Komitmen</small>
                </div>

                <div class="form-group">
                    <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                    <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" class="form-control"
                           value="{{ $travel->tanggal_pembayaran ?? now()->format('Y-m-d') }}" required>
                    <small class="form-text">Tanggal saat kuitansi disetujui dan dibayarkan</small>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <a href="{{ route('kuitansi.index') }}" class="btn-cancel">← Batal</a>
                    <button type="submit" class="btn-submit">💾 Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection
