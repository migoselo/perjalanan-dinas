<?php

namespace Tests\Feature;

use App\Models\SPTProgres;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgresControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $travel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test travel
        $this->travel = Travel::factory()->create([
            'nomor_spd' => 'SPD-001/2026',
            'nomor_surat_tugas' => 'SPT-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);
    }

    /** @test */
    public function it_can_display_progres_index_page()
    {
        $response = $this->get(route('progres.index'));

        $response->assertStatus(200);
        $response->assertViewIs('progres.index');
    }

    /** @test */
    public function it_can_add_new_spt_progres()
    {
        $response = $this->post(route('progres.store'), [
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'SPT berhasil ditambahkan',
        ]);

        $this->assertDatabaseHas('spt_progres', [
            'nomor_spt' => 'SPT-001/2026',
            'travel_id' => $this->travel->id,
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_spt()
    {
        // Create first SPT
        SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        // Try to create duplicate
        $response = $this->post(route('progres.store'), [
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'SPT sudah ada dalam daftar',
        ]);
    }

    /** @test */
    public function it_can_get_travel_list()
    {
        $response = $this->get(route('progres.getTravelList'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'nama_pegawai', 'nomor_surat_tugas', 'nomor_spd'],
            ],
        ]);
    }

    /** @test */
    public function it_can_get_progres_data()
    {
        // Create test data
        SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        $response = $this->get(route('progres.getData'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id', 'travel_id', 'nomor_spt', 'nomor_spd', 'nama_pegawai',
                    'is_complete', 'created_at', 'updated_at',
                ],
            ],
            'stats' => ['total', 'complete', 'incomplete'],
        ]);
    }

    /** @test */
    public function it_can_delete_spt_progres()
    {
        $progres = SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        $response = $this->delete(route('progres.destroy', $progres));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'SPT berhasil dihapus',
        ]);

        $this->assertDatabaseMissing('spt_progres', [
            'id' => $progres->id,
        ]);
    }

    /** @test */
    public function spt_progres_model_has_correct_relationships()
    {
        $progres = SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        $this->assertNotNull($progres->travel);
        $this->assertEquals($this->travel->id, $progres->travel->id);
    }

    /** @test */
    public function it_can_check_completion_status()
    {
        $progres = SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
        ]);

        // Initially incomplete
        $this->assertFalse($progres->is_complete);

        // Set one file
        $progres->update(['laporan_file_id' => 'file_id_1']);
        $this->assertFalse($progres->checkCompletion());

        // Set two files
        $progres->update(['penanggung_jawab_file_id' => 'file_id_2']);
        $this->assertFalse($progres->checkCompletion());

        // Set all three files
        $progres->update(['pembayaran_file_id' => 'file_id_3']);
        $this->assertTrue($progres->checkCompletion());
    }

    /** @test */
    public function it_can_get_file_status()
    {
        $progres = SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
            'laporan_file_id' => 'file_id_1',
        ]);

        $status = $progres->getFileStatus();

        $this->assertTrue($status['laporan']);
        $this->assertFalse($status['penanggung_jawab']);
        $this->assertFalse($status['pembayaran']);
    }

    /** @test */
    public function it_can_get_uploaded_files()
    {
        $progres = SPTProgres::create([
            'travel_id' => $this->travel->id,
            'nomor_spt' => 'SPT-001/2026',
            'nomor_spd' => 'SPD-001/2026',
            'nama_pegawai' => 'John Doe',
            'laporan_file_id' => 'file_id_1',
            'laporan_file_name' => 'report.pdf',
            'laporan_uploaded_at' => now(),
        ]);

        $files = $progres->getUploadedFiles();

        $this->assertCount(1, $files);
        $this->assertEquals('laporan', $files[0]['type']);
        $this->assertEquals('file_id_1', $files[0]['file_id']);
    }
}
