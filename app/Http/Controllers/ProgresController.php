<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use App\Models\SPTProgres;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgresController extends Controller
{
    protected $googleDrive;

    public function __construct()
    {
        try {
            $this->googleDrive = new GoogleDriveService();
        } catch (\Exception $e) {
            \Log::warning('GoogleDriveService initialization skipped: ' . $e->getMessage());
            $this->googleDrive = null;
        }
    }

    /**
     * Tampilkan halaman progres dengan tabel SPT
     * Menampilkan SEMUA travels dengan status SPT-nya
     */
    public function index()
    {
        // Get semua travels dengan relasi
        $travels = Travel::with(['transportItems', 'accommodationItems', 'perdiemItems'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Map setiap travel dengan SPT progress-nya
        $progres = $travels->map(function($travel) {
            // Cari SPT untuk travel ini
            $spt = SPTProgres::where('travel_id', $travel->id)->first();
            
            if (!$spt) {
                // Travel belum punya SPT - buat dummy object
                $spt = new SPTProgres();
                $spt->id = null;
                $spt->travel_id = $travel->id;
                $spt->nomor_spt = null;
                $spt->laporan_file_id = null;
                $spt->penanggung_jawab_file_id = null;
                $spt->pembayaran_file_id = null;
                $spt->is_complete = false;
            }
            
            // Pastikan travel relationship ter-attach
            $spt->travel = $travel;
            return $spt;
        });

        // Hitung statistik
        $total = $progres->count();
        $complete = $progres->where('is_complete', true)->count();
        $incomplete = $total - $complete;

        return view('progres.index', compact('progres', 'total', 'complete', 'incomplete'));
    }

    /**
     * Tambah SPT baru via AJAX
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'travel_id' => 'required|exists:travels,id',
                'nomor_spt' => 'required|string',
                'nomor_spd' => 'nullable|string',
                'nama_pegawai' => 'nullable|string',
            ]);

            // Cek duplikasi
            $exists = SPTProgres::where('travel_id', $validated['travel_id'])
                ->where('nomor_spt', $validated['nomor_spt'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'SPT sudah ada dalam daftar',
                ], 422);
            }

            // Create SPT Progress
            $sptProgres = SPTProgres::create([
                'travel_id' => $validated['travel_id'],
                'nomor_spt' => $validated['nomor_spt'],
                'nomor_spd' => $validated['nomor_spd'],
                'nama_pegawai' => $validated['nama_pegawai'],
            ]);

            // Buat folder di Google Drive
            try {
                if ($this->googleDrive) {
                    $folderResult = $this->googleDrive->createSPTFolder($validated['nomor_spt']);
                    
                    $sptProgres->update([
                        'google_drive_folder_id' => $folderResult['folder_id'],
                        'google_drive_folder_name' => $folderResult['folder_name'],
                    ]);
                }
            } catch (\Exception $e) {
                // Log error tapi tetap lanjutkan
                \Log::warning('Google Drive folder creation skipped: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'SPT berhasil ditambahkan',
                'data' => $sptProgres,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload file untuk SPT tertentu
     * Final version dengan Google Drive integration & CSRF handling
     */
   public function uploadFile(Request $request, $id)
{
    try {

        $request->validate([
            'file' => 'required|file|max:10240',
            'file_type' => 'required|in:laporan,penanggung_jawab,pembayaran'
        ]);

        $progres = SPTProgres::findOrFail($id);

        if (!$this->googleDrive) {
            return response()->json([
                'success' => false,
                'message' => 'Google Drive belum dikonfigurasi'
            ], 500);
        }

        $file = $request->file('file');
        $fileType = $request->file_type;

        // Pastikan folder Google Drive sudah ada
        if (!$progres->google_drive_folder_id) {
            try {
                // Cek apakah folder sudah ada di Google Drive
                $folderCheck = $this->googleDrive->checkFolderExists($progres->nomor_spt);
                
                if ($folderCheck['exists']) {
                    // Folder sudah ada, update database
                    $progres->update([
                        'google_drive_folder_id' => $folderCheck['folder_id'],
                        'google_drive_folder_name' => $folderCheck['folder_name'],
                    ]);
                } else {
                    // Buat folder baru
                    $folderResult = $this->googleDrive->createSPTFolder($progres->nomor_spt);
                    
                    $progres->update([
                        'google_drive_folder_id' => $folderResult['folder_id'],
                        'google_drive_folder_name' => $folderResult['folder_name'],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create/check Google Drive folder: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat folder di Google Drive: ' . $e->getMessage()
                ], 500);
            }
        }

        // Upload ke Google Drive
        $driveFileId = $this->googleDrive->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            $progres->google_drive_folder_id,
            $fileType
        );

        // Simpan ke database
        $column = $fileType . '_file_id';

        $progres->$column = $driveFileId;

        // cek status lengkap
        $progres->is_complete =
            $progres->laporan_file_id &&
            $progres->penanggung_jawab_file_id &&
            $progres->pembayaran_file_id;

        $progres->save();

        return response()->json([
            'success' => true,
            'message' => 'Upload berhasil'
        ]);

    } catch (\Exception $e) {

        \Log::error($e);

        return response()->json([
            'success' => false,
            'message' => 'Upload gagal: '.$e->getMessage()
        ], 500);
    }
}


    /**
     * Delete file dari SPT
     */
    public function deleteFile(Request $request, $id)
    {
        try {
            // Find SPT
            $sptProgres = SPTProgres::find($id);
            if (!$sptProgres) {
                return response()->json(['success' => false, 'message' => 'Data SPT tidak ditemukan'], 404);
            }
            
            $validated = $request->validate([
                'file_type' => 'required|in:laporan,penanggung_jawab,pembayaran',
            ]);

            $fileType = $validated['file_type'];
            $fileIdColumn = $fileType . '_file_id';
            $fileId = $sptProgres->{$fileIdColumn};

            if (!$fileId) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan',
                ], 404);
            }

            // Delete dari Google Drive
            try {
                $this->googleDrive->deleteFile($fileId);
            } catch (\Exception $e) {
                \Log::error('Google Drive deletion failed: ' . $e->getMessage());
                // Tetap lanjutkan untuk clear dari database
            }

            // Clear dari database
            $updateData = [
                $fileType . '_file_id' => null,
                $fileType . '_file_name' => null,
                $fileType . '_uploaded_at' => null,
            ];

            $sptProgres->update($updateData);

            // Update completion status
            $sptProgres->checkCompletion();

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus',
                'data' => [
                    'file_type' => $fileType,
                    'is_complete' => $sptProgres->is_complete,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data SPT tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data SPT untuk AJAX (refresh tabel)
     */
    public function getData()
    {
        try {
            $progres = SPTProgres::with('travel')
                ->orderBy('created_at', 'desc')
                ->get();

            $total = $progres->count();
            $complete = $progres->where('is_complete', true)->count();
            $incomplete = $total - $complete;

            return response()->json([
                'success' => true,
                'data' => $progres,
                'stats' => [
                    'total' => $total,
                    'complete' => $complete,
                    'incomplete' => $incomplete,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete SPT dari daftar
     */
    public function destroy(SPTProgres $sptProgres)
    {
        try {
            // Delete semua file dari Google Drive jika ada
            $files = $sptProgres->getUploadedFiles();
            
            foreach ($files as $file) {
                try {
                    $this->googleDrive->deleteFile($file['file_id']);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete file: ' . $e->getMessage());
                }
            }

            // Delete folder dari Google Drive jika ada
            if ($sptProgres->google_drive_folder_id) {
                try {
                    $this->googleDrive->deleteFile($sptProgres->google_drive_folder_id);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete folder: ' . $e->getMessage());
                }
            }

            // Delete dari database
            $sptProgres->delete();

            return response()->json([
                'success' => true,
                'message' => 'SPT berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list Travel untuk dropdown di modal
     */
    public function getTravelList()
    {
        try {
            $travels = Travel::select('id', 'nomor_surat_tugas', 'nomor_spd', 'nama_pegawai')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $travels,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
