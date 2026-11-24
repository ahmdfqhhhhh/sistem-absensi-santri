<?php

namespace Tests\Feature;

use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RekapWebControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $musyrif;
    protected User $wali;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin_test_rekap@example.com'],
            ['name' => 'Super Admin Test', 'role' => 'super_admin', 'password' => bcrypt('password')]
        );

        $this->musyrif = User::firstOrCreate(
            ['email' => 'musyrif_test_rekap@example.com'],
            ['name' => 'Musyrif Test', 'role' => 'musyrif', 'password' => bcrypt('password')]
        );

        $this->wali = User::firstOrCreate(
            ['email' => 'wali_test_rekap@example.com'],
            ['name' => 'Wali Test', 'role' => 'wali_santri', 'password' => bcrypt('password')]
        );
    }

    public function test_guest_cannot_access_rekap_routes(): void
    {
        $response = $this->get('/rekap/absensi');
        $response->assertRedirect('/login');

        $responseHarian = $this->get('/rekap/absensi/harian');
        $responseHarian->assertRedirect('/login');
    }

    public function test_wali_cannot_access_rekap_routes(): void
    {
        $response = $this->actingAs($this->wali)->get('/rekap/absensi');
        $response->assertRedirect(route('wali.portal'));

        $responseHarian = $this->actingAs($this->wali)->get('/rekap/absensi/harian');
        $responseHarian->assertRedirect(route('wali.portal'));
    }

    public function test_super_admin_can_access_rekap_absensi_harian(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/rekap/absensi/harian');

        $response->assertOk();
        $response->assertViewIs('rekap.absensi');
        $response->assertViewHas('tipe', 'harian');
        $response->assertViewHas('santriIzin');
        $response->assertViewHas('santriTerlambat');
        $response->assertViewHas('santriSakit');
        $response->assertViewHas('santriAlpa');
        $response->assertViewHas('waText');

        $content = $response->getContent();
        $this->assertStringContainsString('Rekapitulasi Absensi Santri', $content);
        $this->assertStringContainsString('Harian', $content);
        $this->assertStringContainsString('Salin Teks WhatsApp', $content);
        $this->assertStringContainsString('Format Laporan WhatsApp Grup', $content);
    }

    public function test_super_admin_can_access_rekap_absensi_bulanan(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/rekap/absensi');

        $response->assertOk();
        $response->assertViewIs('rekap.absensi');
        $response->assertViewHas('tipe', 'bulanan');

        $content = $response->getContent();
        $this->assertStringContainsString('Rekapitulasi Absensi Santri', $content);
        $this->assertStringContainsString('Bulanan', $content);
    }

    public function test_rekap_harian_filters_by_date(): void
    {
        $targetDate = now()->subDays(2)->toDateString();
        $response = $this->actingAs($this->superAdmin)->get("/rekap/absensi/harian?tanggal={$targetDate}");

        $response->assertOk();
        $response->assertViewHas('tanggal', $targetDate);
        $response->assertViewHas('tipe', 'harian');
    }

    public function test_export_absensi_harian_excel_returns_streamed_download(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/rekap/absensi/harian/excel');

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.ms-excel', (string) $response->headers->get('content-type'));
    }

    public function test_export_absensi_harian_pdf_renders_printable_view(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/rekap/absensi/harian/pdf');

        $response->assertOk();
        $response->assertViewIs('rekap.absensi_harian_pdf');
        $response->assertViewHas('tipe', 'harian');
    }
}
