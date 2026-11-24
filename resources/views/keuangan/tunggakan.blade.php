@extends('layouts.app')

@section('title', 'Tunggakan Santri - Super Admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl text-white p-6 md:p-8 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-8 -bottom-8 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-2xl">
                    🚨
                </div>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-semibold">
                        <span>👁️ Monitoring Keuangan (View-Only)</span>
                    </div>
                    <p class="text-xs font-bold text-emerald-200 mt-0.5">Pondok Pesantren Miftahul Huda Kroya</p>
                </div>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                Daftar Tunggakan Santri
            </h1>
            <p class="mt-2 text-sm text-emerald-100/90 max-w-2xl">
                Pantau santri yang memiliki kewajiban tagihan belum lunas dan tunggakan yang melewati jatuh tempo. Data tersinkronisasi langsung dengan rekapan <strong>Bendahara Pondok</strong>.
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Total Santri Belum Lunas --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center text-xl">👤</div>
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2.5 py-0.5 rounded-full">Belum Lunas</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Belum Lunas</p>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                {{ number_format($totalSantriBelumLunas ?? 0) }} <span class="text-sm font-semibold text-slate-400">santri</span>
            </p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Santri yang memiliki tagihan aktif</p>
        </div>

        {{-- Total Nominal Belum Lunas --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-xl">💸</div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded-full">Total Nominal</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Grand Total Belum Lunas</p>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-0.5">
                Rp {{ number_format($grandTotalNunggak ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Total kewajiban yang belum dibayarkan</p>
        </div>

        {{-- Lewat Jatuh Tempo (Kritis) --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-xl">⏰</div>
                <span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 px-2.5 py-0.5 rounded-full">Perlu Tindakan</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Lewat Jatuh Tempo (Kritis)</p>
            <p class="text-2xl font-extrabold text-red-600 dark:text-red-400 mt-0.5">
                {{ number_format($totalSantriLewatTempo ?? 0) }} <span class="text-sm font-semibold text-slate-400">santri</span>
            </p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
                Nominal: <strong class="text-red-500">Rp {{ number_format($grandTotalLewatTempo ?? 0, 0, ',', '.') }}</strong>
            </p>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
        <form method="GET" action="{{ route('keuangan.tunggakan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            {{-- Cari Nama / NIS --}}
            <div>
                <label for="cari" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Cari Santri</label>
                <input type="text" id="cari" name="cari" value="{{ request('cari') }}" placeholder="Nama santri atau NIS..."
                       class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2.5 focus:ring-rose-500 focus:border-rose-500">
            </div>

            {{-- Filter Kelas --}}
            <div>
                <label for="kelas_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas</label>
                <select id="kelas_id" name="kelas_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2.5 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Asrama --}}
            <div>
                <label for="asrama_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Asrama</label>
                <select id="asrama_id" name="asrama_id" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2.5 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Asrama</option>
                    @foreach($asramaList as $asrama)
                        <option value="{{ $asrama->id }}" {{ request('asrama_id') == $asrama->id ? 'selected' : '' }}>
                            {{ $asrama->nama_asrama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Tempo --}}
            <div>
                <label for="status_tempo" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Tempo</label>
                <select id="status_tempo" name="status_tempo" class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2.5 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Semua Belum Lunas</option>
                    <option value="lewat" {{ request('status_tempo') === 'lewat' ? 'selected' : '' }}>🚨 Lewat Jatuh Tempo Saja</option>
                    <option value="belum_lewat" {{ request('status_tempo') === 'belum_lewat' ? 'selected' : '' }}>⏳ Belum Jatuh Tempo</option>
                </select>
            </div>

            {{-- Tombol Filter & Reset --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition active:scale-95 text-center">
                    🔍 Filter
                </button>
                @if(request()->hasAny(['cari', 'kelas_id', 'asrama_id', 'status_tempo']))
                    <a href="{{ route('keuangan.tunggakan') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Santri Nunggak --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Santri Belum Lunas / Menunggak</h3>
                <p class="text-xs text-slate-400 mt-0.5">Menampilkan seluruh santri yang memiliki kewajiban pembayaran aktif</p>
            </div>
            <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-3 py-1 rounded-full">
                Total: {{ $santris->total() }} santri
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Santri</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas & Asrama</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tagihan Belum Lunas</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tunggakan</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Tempo</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Wali Santri</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi Pengingat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($santris as $santri)
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', $santri->no_wa_wali ?? '');
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                            $pesanWa = rawurlencode("Assalamu'alaikum Wr. Wb. Bapak/Ibu {$santri->nama_wali}, wali dari ananda {$santri->nama} (Kelas: " . ($santri->kelas->nama_kelas ?? '-') . "). Mengingatkan perihal kewajiban administrasi santri di Pondok Pesantren Miftahul Huda Kroya sebesar Rp " . number_format($santri->total_nunggak ?? 0, 0, ',', '.') . " yang belum diselesaikan. Pembayaran dapat dilakukan langsung melalui Portal Wali Santri atau ke kantor Bendahara Pondok. Terima kasih. Wassalamu'alaikum Wr. Wb.");
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl {{ $santri->is_kritis ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }} font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($santri->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white text-xs">{{ $santri->nama }}</p>
                                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500">NIS: {{ $santri->nis ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block text-xs font-medium text-slate-700 dark:text-slate-300">{{ $santri->kelas->nama_kelas ?? '-' }}</span>
                                <span class="block text-[11px] text-slate-400 dark:text-slate-500">{{ $santri->asrama->nama_asrama ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $santri->is_kritis ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                    {{ $santri->jumlah_nunggak }} tagihan
                                </span>
                                @if($santri->tagihans->isNotEmpty())
                                    <p class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[150px] mx-auto">
                                        {{ $santri->tagihans->pluck('nama_tagihan')->unique()->take(2)->implode(', ') }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="text-xs font-extrabold text-rose-600 dark:text-rose-400">
                                    Rp {{ number_format($santri->total_nunggak ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($santri->is_kritis)
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                        <span>🚨</span>
                                        <span>Lewat Tempo</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                        <span>⏳</span>
                                        <span>Belum Tempo</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $santri->nama_wali ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $santri->no_wa_wali ?? 'Belum ada No WA' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if(!empty($santri->no_wa_wali))
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $pesanWa }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow transition active:scale-95"
                                       title="Kirim pengingat WhatsApp ke wali santri">
                                        <span>📲</span>
                                        <span>Ingatkan WA</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">- No WA Kosong -</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500">
                                <span class="text-3xl block mb-2">🎉</span>
                                <p class="text-sm font-semibold">Alhamdulillah, tidak ada santri yang memiliki tunggakan!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($santris->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $santris->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
