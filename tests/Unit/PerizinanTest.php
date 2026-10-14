<?php

namespace Tests\Unit;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Api\PerizinanController;
use App\Http\Requests\Api\ApprovePerizinanRequest;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use App\Services\PerizinanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PerizinanTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function approve_generates_unique_qr_code_and_saves_to_storage(): void
    {
        Storage::fake('public');

        $santri = new Santri([
            'nis' => 'SAN-2026-0099',
            'nama' => 'Muhammad Zaid',
            'jenis_kelamin' => 'L',
        ]);
        $santri->id = 12;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Al-Fatih']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '10 Agama A']));

        $perizinan = Mockery::mock(Perizinan::class)->makePartial();
        $perizinan->id = 7;
        $perizinan->status = 'pending';
        $perizinan->nomor_surat = 'IZN/2026/10/0007';
        $perizinan->jenis_izin = 'pulang';
        $perizinan->keperluan = 'Menghadiri walimah pernikahan kakak kandung';
        $perizinan->tanggal_mulai = Carbon::now();
        $perizinan->tanggal_selesai = Carbon::now()->addDays(3);
        $perizinan->setRelation('santri', $santri);

        $perizinan->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($attributes) use ($perizinan) {
                $perizinan->status = $attributes['status'];
                $perizinan->qr_code_path = $attributes['qr_code_path'];
                $perizinan->qr_token = $attributes['qr_token'];

                return $attributes['status'] === 'disetujui'
                    && ! empty($attributes['qr_token'])
                    && str_starts_with($attributes['qr_code_path'], 'qrcodes/perizinan/')
                    && str_ends_with($attributes['qr_code_path'], '.svg')
                    && $attributes['approved_by'] === 1;
            }))
            ->andReturn(true);

        $perizinan->shouldReceive('fresh')
            ->once()
            ->andReturn($perizinan);

        $service = new PerizinanService;
        $approvedPerizinan = $service->approve($perizinan, 1, 'Disetujui ustadz pengasuhan');

        $this->assertEquals('disetujui', $approvedPerizinan->status);

        // Pastikan file SVG tersimpan di fake storage disk public
        $files = Storage::disk('public')->allFiles('qrcodes/perizinan');
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.svg', $files[0]);

        $svgContent = Storage::disk('public')->get($files[0]);
        $this->assertStringContainsString('<svg', $svgContent);
    }

    #[Test]
    public function approve_throws_exception_if_already_approved(): void
    {
        $perizinan = new Perizinan([
            'status' => 'disetujui',
            'nomor_surat' => 'IZN/2026/10/0001',
        ]);
        $perizinan->id = 1;

        $service = new PerizinanService;

        $this->expectException(AbsensiException::class);
        $this->expectExceptionMessage('Perizinan ini sudah disetujui sebelumnya.');

        $service->approve($perizinan, 1);
    }

    #[Test]
    public function verify_qr_token_identifies_valid_permission(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0005',
            'nama' => 'Ahmad Dani',
        ]);
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Ibnu Sina']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '11 IPA']));

        $perizinan = new Perizinan([
            'nomor_surat' => 'IZN/2026/10/0010',
            'status' => 'disetujui',
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->addDays(2),
            'qr_token' => 'IZN-TESTTOKEN123',
        ]);
        $perizinan->setRelation('santri', $santri);

        $service = Mockery::mock(PerizinanService::class)->makePartial();
        $service->shouldReceive('verifyQrToken')
            ->once()
            ->with('IZN-TESTTOKEN123')
            ->andReturn([
                'is_valid' => true,
                'catatan_verifikasi' => 'Surat Izin Sah dan Berlaku Aktif.',
                'perizinan' => $perizinan,
            ]);

        $controller = new PerizinanController($service);
        $response = $controller->verify('IZN-TESTTOKEN123');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertTrue($content['success']);
        $this->assertTrue($content['is_valid']);
        $this->assertEquals('IZN/2026/10/0010', $content['data']['nomor_surat']);
    }

    #[Test]
    public function controller_approve_returns_json_with_qr_code_metadata(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0088',
            'nama' => 'Hilman Maulana',
        ]);

        $perizinan = new Perizinan([
            'nomor_surat' => 'IZN/2026/10/0088',
            'status' => 'disetujui',
            'qr_token' => 'IZN-APPROVED-TOKEN',
            'qr_code_path' => 'qrcodes/perizinan/IZN-APPROVED-TOKEN.svg',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
        ]);
        $perizinan->id = 88;
        $perizinan->setRelation('santri', $santri);

        $service = Mockery::mock(PerizinanService::class);
        $service->shouldReceive('approve')
            ->once()
            ->with(88, 5, 'Disetujui ustadz')
            ->andReturn($perizinan);

        $controller = new PerizinanController($service);

        $user = new User(['name' => 'Ustadz Pembina']);
        $user->id = 5;

        $request = ApprovePerizinanRequest::create('/api/perizinan/88/approve', 'POST', [
            'approved_by' => 5,
            'catatan_approval' => 'Disetujui ustadz',
        ]);
        $request->setUserResolver(fn () => $user);

        $response = $controller->approve(88, $request);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('IZN-APPROVED-TOKEN', $content['meta']['qr_token']);
        $this->assertEquals('qrcodes/perizinan/IZN-APPROVED-TOKEN.svg', $content['meta']['qr_code_path']);
        $this->assertStringContainsString('QR Code Surat Izin Digital telah dibuat', $content['message']);
    }
}
