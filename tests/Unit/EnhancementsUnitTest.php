<?php

namespace Tests\Unit;

use App\Models\Asrama;
use App\Models\Kamar;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use App\Services\PerizinanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnhancementsUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function user_gender_and_role_helpers_work_correctly(): void
    {
        $musyrifPutra = new User([
            'role' => User::ROLE_MUSYRIF,
            'gender' => 'putra',
        ]);
        $this->assertTrue($musyrifPutra->isMusyrif());
        $this->assertTrue($musyrifPutra->isPutra());
        $this->assertFalse($musyrifPutra->isPutri());
        $this->assertFalse($musyrifPutra->isWali());
        $this->assertFalse($musyrifPutra->isKeamanan());

        $musyrifPutri = new User([
            'role' => User::ROLE_MUSYRIF,
            'gender' => 'putri',
        ]);
        $this->assertTrue($musyrifPutri->isMusyrif());
        $this->assertTrue($musyrifPutri->isPutri());
        $this->assertFalse($musyrifPutri->isPutra());

        $keamanan = new User([
            'role' => User::ROLE_KEAMANAN,
        ]);
        $this->assertTrue($keamanan->isKeamanan());

        $wali = new User([
            'role' => User::ROLE_WALI_SANTRI,
        ]);
        $this->assertTrue($wali->isWali());
    }

    #[Test]
    public function santri_foto_url_attribute_returns_correct_storage_url(): void
    {
        Storage::fake('public');

        $santri = new Santri([
            'nis' => 'SAN-001',
            'nama' => 'Ahmad Santri',
            'foto_path' => 'santri-foto/test_foto.jpg',
        ]);

        // Belum ada file di storage -> returns null
        $this->assertNull($santri->foto_url);

        // Buat file fake di storage
        Storage::disk('public')->put('santri-foto/test_foto.jpg', 'fake-image-content');

        // File sudah ada -> returns url
        $this->assertNotNull($santri->foto_url);
        $this->assertStringContainsString('santri-foto/test_foto.jpg', $santri->foto_url);
    }

    #[Test]
    public function perizinan_approve_saves_nama_pemberi_izin(): void
    {
        Storage::fake('public');

        $santri = new Santri([
            'nis' => 'SAN-002',
            'nama' => 'Zaid bin Tsabit',
        ]);
        $santri->id = 5;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Al-Farabi']));

        /** @var Perizinan&Mockery\MockInterface $perizinan */
        $perizinan = Mockery::mock(Perizinan::class)->makePartial();
        $perizinan->id = 10;
        $perizinan->status = 'pending';
        $perizinan->nomor_surat = 'IZN/2026/11/0002';
        $perizinan->jenis_izin = 'pulang';
        $perizinan->tanggal_mulai = Carbon::now();
        $perizinan->tanggal_selesai = Carbon::now()->addDays(2);
        $perizinan->setRelation('santri', $santri);

        $perizinan->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($attributes) {
                return $attributes['status'] === 'disetujui'
                    && $attributes['nama_pemberi_izin'] === 'KH. Ahmad Dahlan M.Pd'
                    && $attributes['approved_by'] === 2;
            }))
            ->andReturn(true);

        $perizinan->shouldReceive('fresh')
            ->once()
            ->andReturn($perizinan);

        $service = new PerizinanService;
        $result = $service->approve($perizinan, 2, 'Izin diberikan pengasuh.', 'KH. Ahmad Dahlan M.Pd');

        $this->assertInstanceOf(Perizinan::class, $result);
    }

    #[Test]
    public function kamar_and_asrama_models_have_correct_relationships(): void
    {
        $asrama = new Asrama([
            'nama_asrama' => 'Asrama Al-Ghazali',
            'gender' => 'putra',
        ]);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $asrama->kamars());

        $kamar = new Kamar([
            'nama_kamar' => 'Kamar Abu Bakar 1',
            'kapasitas' => 8,
        ]);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $kamar->asrama());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $kamar->santris());
    }
}
