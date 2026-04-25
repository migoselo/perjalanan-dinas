<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Perjalanan Dinas</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7f8;
        }

        .sidebar {
            width: 220px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            background: #fff;
            border-right: 1px solid #eee;
        }

        .content {
            margin-left: 240px;
            padding: 40px;
        }

        .card-custom {
            border-radius: 12px;
            box-shadow: none;
            border: 1px solid #e9ecef;
        }

        .form-control-custom {
            border: 0;
            background: #f1f2f4;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .label-number {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .add-button {
            border: 1px dashed #dcdcdc;
            border-radius: 8px;
            background: transparent;
            padding: 12px;
            text-align: center;
            color: #333;
            cursor: pointer;
        }

        .small-card {
            border-radius: 8px;
            border: 1px solid #ececec;
            background: #fff;
        }

        .list-group-item.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <div class="sidebar d-flex flex-column">
        <div class="px-3 mb-4">
            <h5 class="mb-0">Perjalanan Dinas</h5>
            <small class="text-muted">Sistem Manajemen SPD</small>
        </div>

        <div class="list-group list-group-flush">
            <a href="{{ route('travel.create') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('travel.create') ? 'active' : '' }}">Form
                Input</a>
            <a href="{{ route('data.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('data.index', 'data.show', 'data.edit') ? 'active' : '' }}">Data</a>
            <a href="{{ route('spby.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('spby.index', 'spby.create') ? 'active' : '' }}">SPBY</a>
            <a href="{{ route('spd.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('spd.*', 'data.spd') ? 'active' : '' }}">SPD
                Lembar Depan</a>
            <a href="{{ route('rincian.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('rincian.*', 'surat.*', 'rincian-surat*') ? 'active' : '' }}">Rincian</a>
            <a href="{{ route('kuitansi.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('kuitansi.*') ? 'active' : '' }}">Kuitansi</a>
            <a href="{{ route('progres.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('progres.*') ? 'active' : '' }}">Progres</a>
        </div>

        <div class="mt-auto p-3 small text-muted">
            © {{ date(format: 'Y') }} Sistem Perjalanan Dinas
        </div>
    </div>

    <div class="content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertEl = document.querySelector('.content .alert');
            if (!alertEl) return;
            // Auto-close after 2 seconds (2000 ms)
            setTimeout(function () {
                if (window.bootstrap && bootstrap.Alert) {
                    try {
                        new bootstrap.Alert(alertEl).close();
                        return;
                    } catch (e) {
                        // fallback to remove
                    }
                }
                alertEl.remove();
            }, 2000);
        });
    </script>

    <!-- allow partials to push scripts -->
    @stack('scripts')
</body>

</html>