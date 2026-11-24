# DOKUMEN UTAMA: PRD, SRS, DAN PLAN STRATEGI

**Nama Sistem:** Web-App Absensi Santri (*E-Absensi Pesantren*)  
---

# BAGIAN 1: PRODUCT REQUIREMENTS DOCUMENT (PRD)

## 1\. Ringkasan & Latar Belakang

Proses pencatatan kehadiran santri di pondok pesantren umumnya masih menggunakan metode manual berbasis kertas (*presensi fisik*), baik untuk kegiatan Kegiatan Belajar Mengajar (KBM) madrasah/diniyah, shalat berjamaah 5 waktu, pengajian kitab, maupun jam malam asrama.

Metode manual ini menghadapi beberapa tantangan utama:

* **Rekapitulasi Lambat:** Pengurus memerlukan waktu lama untuk mengompilasi data absensi dari berbagai ustadz/musyrif.  
* **Risiko Human Error:** Data hilang, terselip, atau terjadi manipulasi absensi.  
* **Minim Transparansi Wali Santri:** Orang tua/wali santri sering kesulitan memantau kedisiplinan dan keberadaan anak secara *real-time*.

### Tujuan Sistem

Membangun aplikasi web responsive (*PWA-ready*) yang memungkinkan pengurus/ustadz mencatat absensi santri secara cepat (via Scan QR Code Card atau input cepat), mengelola perizinan secara terstruktur, menyediakan rekapitulasi otomatis, dan mengintegrasikan notifikasi langsung ke WhatsApp wali santri.

## 2\. Peran Pengguna (User Roles & Permissions)

| Role | Deskripsi & Hak Akses Utama |
| :---- | :---- |
| **Super Admin / Pengurus Pusat** | Akses penuh ke seluruh sistem: Kelola data master (Santri, Asrama, Kelas, Ustadz), pengaturan sesi absensi, konfigurasi WA Gateway, dan akses laporan global. |
| **Musyrif** | Melakukan pencatatan absensi harian pada kelas, pengajian, shalat berjamaah, atau asrama yang diampu. Melihat rekap kehadiran santri binaan. |
| **Pengurus Keamanan** | Memvalidasi dan mencatat keluar-masuk santri berdasarkan Surat Izin resmi (Scan QR Code Surat Izin). |
| **Wali Santri** | Mengakses Portal Wali untuk melihat riwayat kehadiran, rekap kedisiplinan, serta mengajukan perizinan (*izin pulang/sakit*). |

## 3\. Spesifikasi Kebutuhan Fungsional PRD

### 3.1 Modul Manajemen Data Master

* **Sistem Informasi Santri:** CRUD data santri (NIS, Nama, Foto, Kamar, Kelas Diniyah/Formal, Nama Wali, No. WA Wali).  
* **Generasi QR Code ID Card:** Auto-generate QR Code unik berdasarkan NIS/ID Santri untuk dicetak pada kartu identitas santri.  
* **Manajemen Kelas & Asrama:** Pengelompokan santri berdasarkan blok asrama, kamar, dan kelas pelajaran.

### 3.2 Modul Pencatatan Absensi (Presence Engine)

* **Mode Scan QR Code:** Ustadz/Musyrif melakukan scan kartu QR santri menggunakan kamera HP/Webcam. Sistem langsung memberikan tanggapan audio (*beep*) dan visual (Hadir).  
* **Mode Batch / Manual Input:** Daftar nama santri per kelas/kamar dengan tombol cepat (*Default: Hadir*, centang untuk *Izin / Sakit / Alpa*).  
* **Kategori Sesi Absensi:** Absensi KBM, Shalat Berjamaah, Pengajian Kitab, dan Cek Malam Asrama.

### 3.3 Modul Perizinan & Kamtib

* **Pengajuan Izin (Workflow):** Wali Santri mengajukan izin via web portal \-\> Musyrif/Pengurus menyetujui \-\> Sistem menerbitkan **Surat Izin Digital (QR Code)**.  
* **Pos Keamanan (Kamtib):** Petugas pos scan QR Surat Izin saat santri keluar dan masuk kembali ke area pesantren untuk mencatat timestamp riil.

### 3.4 Modul Rekapitulasi & Analytics

* **Dashboard Real-Time:** Grafik persentase kehadiran hari ini per kategori (Shalat, KBM, Asrama).  
* **Sistem Peringatan Otomatis (EWS):** Notifikasi ke pengurus jika santri Alpa \> 3 kali berturut-turut.  
* **Export Laporan:** Export data absensi bulanan/semesteran ke format Excel (.xlsx) dan PDF.

### 3.5 Modul Integrasi WhatsApp Gateway

* **Notifikasi Alpa Otomatis:** WhatsApp terkirim ke wali santri setiap kali santri tercatat *Alpa* pada sesi vital.  
* **Notifikasi Keluar-Masuk:** Pesan otomatis saat santri melakukan scan keluar/masuk di pos Kamtib.

## 4\. Alur Kerja Utama Absensi (Workflow)

1. **Persiapan Sesi Absensi:** Ustadz/Musyrif membuka aplikasi web, memilih lokasi/kelas, dan membuka sesi absensi.  
2. **Pemindaian / Input Kehadiran:** Santri menunjukkan kartu ID ber-QR Code, Ustadz melakukan scan berturut-turut via kamera HP.  
3. **Sinkronisasi & Validasi Status:** Sistem memeriksa perizinan aktif, menyetatuskan Hadir/Izin/Sakit/Alpa secara otomatis.  
4. **Pemicu Notifikasi WA:** WhatsApp Gateway mengirimkan pesan rincian absensi atau peringatan alpa ke Wali Santri.

## 5\. Spesifikasi Kebutuhan Non-Fungsional PRD

* **Performa & Kecepatan:** Waktu respons scan QR Code \< 500ms per santri.  
* **Offline-First Capability:** Dukungan PWA untuk menyimpan antrean absensi secara lokal jika internet tidak stabil.  
* **Keamanan Data:** Enkripsi password `BCrypt` / `Argon2id` dan kontrol akses RBAC.

## 6\. Roadmap Implementasi Project

* **Minggu 1-2:** Perancangan & Fondasi Sistem (Database, UI/UX, RBAC).  
* **Minggu 3-4:** Core Presence Engine & QR Generator.  
* **Minggu 5-6:** Modul Perizinan, Kamtib, & WA Gateway.  
* **Minggu 7-8:** Dashboard Analytics, Reporting, & PWA Offline Mode.  
* **Minggu 9:** Pilot Project & Deployment.

---

# BAGIAN 2: SOFTWARE REQUIREMENTS SPECIFICATION (SRS)

**Standar Dokumen:** IEEE Std 830-1998

## 1\. Pendahuluan

### 1.1 Tujuan

Mendefinisikan spesifikasi kebutuhan fungsional dan non-fungsional dari Web-App Absensi Santri secara detail sebagai acuan tim pengembang dan penguji software.

### 1.2 Ruang Lingkup Sistem

Aplikasi Web Responsive berbasis PWA mencakup data master, engine scan QR real-time, perizinan Kamtib, WA Gateway, serta dashboard laporan Excel/PDF.

### 1.3 Definisi & Akronim

* **PWA:** Progressive Web App  
* **NIS:** Nomor Induk Santri  
* **Kamtib:** Keamanan dan Ketertiban Pesantren  
* **RBAC:** Role-Based Access Control  
* **WA Gateway:** API Pengirim Pesan WhatsApp Otomatis

## 2\. Deskripsi Umum Sistem

### 2.1 Perspektif Produk

Sistem terpusat berbasis cloud/server lokal yang diakses via browser pada smartphone, tablet, atau PC.

### 2.2 Karakteristik Pengguna & Hak Akses

* **Super Admin:** Full control master data, jadwal, WA API, system log.  
* **Ustadz / Musyrif:** Buka sesi, scan QR, input batch manual, lihat rekap harian.  
* **Petugas Kamtib:** Scan QR Surat Izin keluar/masuk, validasi kepulangan.  
* **Wali Santri:** Portal rekap kehadiran, pengajuan izin sakit/pulang.

### 2.3 Batasan Sistem (Constraints)

* Kamera HP resolusi minimal 2 MP.  
* Pengiriman pesan terikat kuota/koneksi provider WA Gateway.  
* Offline mode PWA terbatas pada caching sesi aktif dan offline queue.

## 3\. Kebutuhan Antarmuka Eksternal

* **UI:** Mobile-first layout, audio-visual feedback (beep), high-contrast mode.  
* **Hardware Interface:** Akses kamera seluler via WebRTC API.  
* **Software Interface:** MySQL, REST HTTP POST API WA Gateway, TLS 1.3 HTTPS.

## 4\. Kebutuhan Fungsional Spesifik (Functional Requirements)

### 4.1 Modul Data Master (`FR-MST`)

* `FR-MST-001`: CRUD Data Santri (NIS, Nama, Asrama, Kelas, No. WA Wali).  
* `FR-MST-002`: Auto-generate QR Code ID Card berbasis NIS.  
* `FR-MST-003`: Pengaturan jadwal rutin absensi (Shalat, KBM, Cek Malam).  
* `FR-MST-004`: Import/Export data master via `.xlsx` / `.csv`.

### 4.2 Modul Engine Absensi (`FR-ABS`)

* `FR-ABS-001`: Pemindaian QR Code \< 500ms per santri.  
* `FR-ABS-002`: Batch Manual Entry checklist per kelas/kamar.  
* `FR-ABS-003`: Auto-detect status izin aktif dari tabel perizinan.  
* `FR-ABS-004`: Antrean simpan lokal PWA saat offline (IndexedDB).

### 4.3 Modul Perizinan & Kamtib (`FR-PER`)

* `FR-PER-001`: Pengajuan Izin Wali Santri via portal.  
* `FR-PER-002`: Approval/Rejection oleh Musyrif/Pengurus.  
* `FR-PER-003`: Penerbitan Surat Izin Digital berbasis QR Code.  
* `FR-PER-004`: Scan Check-In/Out di Pos Kamtib.

### 4.4 Modul Rekapitulasi & Pelaporan (`FR-REP`)

* `FR-REP-001`: Dashboard Analitik grafik kehadiran.  
* `FR-REP-002`: Export Rekap Bulanan PDF & Excel.  
* `FR-REP-003`: Early Warning System (EWS) santri alpa \> 3x.

### 4.5 Modul WhatsApp Gateway (`FR-WAG`)

* `FR-WAG-001`: Auto WA Notification saat Santri Alpa.  
* `FR-WAG-002`: Auto WA Notification saat Santri Check-Out/In Kamtib.

## 5\. Kebutuhan Non-Fungsional (NFR)

* **Kinerja (`NFR-PERF`):** Waktu respons scan \< 500ms, menangani 100 concurrent scans.  
* **Keamanan (`NFR-SEC`):** Enkripsi password `Argon2id`/`BCrypt`, JWT/HttpOnly Session, Rate Limiting login.  
* **Keandalan (`NFR-REL`):** Target uptime 99.5%, Service Worker PWA offline resilience.  
* **Kemudahan (`NFR-USE`):** Waktu onboarding ustadz \< 5 menit, arsitektur modul bersih & API Swagger.

## 6\. Matriks Traceability (PRD to SRS)

* Scan QR & Batch Entry \-\> `FR-ABS-001`, `FR-ABS-002`, `FR-ABS-004` (P1 \- High)  
* Data Master Santri & ID Card \-\> `FR-MST-001`, `FR-MST-002` (P1 \- High)  
* Workflow Perizinan & Kamtib \-\> `FR-PER-001` s/d `FR-PER-004` (P2 \- Medium)  
* WA Gateway Integration \-\> `FR-WAG-001`, `FR-WAG-002` (P2 \- Medium)  
* Dashboard & Export Report \-\> `FR-REP-001` s/d `FR-REP-003` (P3 \- Low)

---

# BAGIAN 3: PLAN STRATEGI (STRATEGIC & IMPLEMENTATION PLAN)

## 1\. Strategi Manajemen Perubahan (Change Management)

* **Metode Parallel Run (2 Minggu Pertama):** Presensi kertas manual tetap berjalan berdampingan dengan aplikasi web selama 14 hari untuk memvalidasi akurasi data.  
* **Penerapan Bertahap (Phased Rollout):**  
  1. *Fase Pilot:* Uji coba di 1 Asrama Putra dan 1 Asrama Putri.  
  2. *Fase Evaluasi:* Penyesuaian UI/UX berdasarkan masukan ustadz.  
  3. *Fase Rollout Total:* Penerapan penuh di seluruh unit pesantren.

## 2\. Kesiapan Logistik & Infrastruktur

* **Kartu ID Santri Ber-QR:** Cetak bahan PVC tahan air berisi QR Code NIS unik santri.  
* **Perangkat Hardware:** Smartphone pribadi ustadz \+ 2 unit Tablet dedicated di Pos Kamtib.  
* **Konektivitas Jaringan:** Pemasangan Wi-Fi di Masjid, Gedung Diniyah, dan Pos Kamtib \+ PWA Offline Mode.  
* **WA Gateway:** Server API WhatsApp resmi pesantren.

## 3\. Program Pelatihan & Onboarding Pengguna

* **Ustadz & Musyrif (Workshop 2 Jam):** Simulasi buka sesi absensi, scan QR, dan input manual darurat.  
* **Petugas Kamtib (Briefing 1 Jam):** Simulasi scan Surat Izin QR Code keluar/masuk pos.  
* **Wali Santri (Panduan Digital):** Distribusi PDF & video tutorial 2 menit via WhatsApp.

## 4\. Mitigasi Risiko Operasional (Contingency Plan)

* **Koneksi Internet Mati:** Data tersimpan di IndexedDB PWA lokal & auto-sync saat online.  
* **HP Ustadz Bermasalah:** Alihkan sesi ke HP pengurus lain / gunakan mode Batch Entry Checklist.  
* **Kartu QR Hilang/Rusak:** Input pencarian NIS/Nama manual & cetak ulang kartu.  
* **Server/Listrik Down:** Catat sementara di lembar darurat, lalu import via file Excel (.xlsx).

## 5\. Indikator Keberhasilan (KPI)

* **Adoption Rate:** Minimal **90%** ustadz/musyrif aktif menggunakan aplikasi dalam 30 hari.  
* **Scan Speed Rate:** Kecepatan scan \< **1 detik** per santri.  
* **Data Accuracy:** **0%** manipulasi data kehadiran.  
* **Wali Engagement:** Minimal **80%** wali santri aktif menggunakan portal perizinan.