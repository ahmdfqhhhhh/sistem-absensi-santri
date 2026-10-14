# Anti Gravity Project Rules - E-Absensi Santri

## Tech Stack & Architecture
- Framework: Laravel (Latest / 11+)
- Database: MySQL / MariaDB
- Authentication: Laravel Sanctum / Session RBAC
- UI & Admin: Blade / TailwindCSS / Filament (Responsive Mobile-First)
- Secondary Packages: simple-qrcode, option/maatwebsite-excel

## Coding Standards & Conventions
1. Gunakan Type Hinting dan Return Types ketat di Controller, Service, dan Repository.
2. Tempatkan logika bisnis di Service Layer (`app/Services/`), jangan menumpuk kode di Controller.
3. Selalu sertakan Form Request Validation untuk setiap endpoint mutasi data.
4. Gunakan API Resource untuk pembentukan format respons JSON yang konsisten.

## Database Guidelines
- Setiap tabel wajib memiliki `id`, `created_at`, dan `updated_at`.
- Relasi Foreign Key harus ditegaskan dengan `constrained()` dan `onDelete('cascade')` atau `nullOnDelete()`.
- Indeks wajib ditambahkan pada kolom yang sering di-query (`nis`, `status`, `created_at`).