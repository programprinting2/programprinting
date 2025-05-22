<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('karyawan')->insert([
            [
                'id_karyawan' => 'EMP-000001',
                'nama_lengkap' => 'Budi Santoso',
                'posisi' => 'Manager Produksi',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2020-01-15',
                'tanggal_lahir' => '1985-06-20',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567890',
                'email' => 'budi.santoso@example.com',
                'gaji_pokok' => 15000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Merdeka No. 123, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12345'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BCA',
                        'nomor_rekening' => '1234567890',
                        'nama_pemilik' => 'Budi Santoso'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012345',
                'status_pajak' => 'K/1',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 1000000,
                    'tunjangan_makan' => 1500000,
                    'tunjangan_kesehatan' => 500000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000002',
                'nama_lengkap' => 'Siti Aminah',
                'posisi' => 'Supervisor QC',
                'departemen' => 'Quality Control',
                'tanggal_masuk' => '2020-03-10',
                'tanggal_lahir' => '1990-08-15',
                'jenis_kelamin' => 'Perempuan',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567891',
                'email' => 'siti.aminah@example.com',
                'gaji_pokok' => 12000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Sudirman No. 45, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12346'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'Mandiri',
                        'nomor_rekening' => '0987654321',
                        'nama_pemilik' => 'Siti Aminah'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012346',
                'status_pajak' => 'K/2',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 800000,
                    'tunjangan_makan' => 1200000,
                    'tunjangan_kesehatan' => 400000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000003',
                'nama_lengkap' => 'Ahmad Rizki',
                'posisi' => 'Operator Printer',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2021-02-01',
                'tanggal_lahir' => '1995-03-25',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Belum Menikah',
                'nomor_telepon' => '081234567892',
                'email' => 'ahmad.rizki@example.com',
                'gaji_pokok' => 8000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Kost',
                        'alamat_lengkap' => 'Jl. Gatot Subroto No. 78, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12347'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BNI',
                        'nomor_rekening' => '1122334455',
                        'nama_pemilik' => 'Ahmad Rizki'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012347',
                'status_pajak' => 'TK/0',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000004',
                'nama_lengkap' => 'Dewi Lestari',
                'posisi' => 'Desainer Grafis',
                'departemen' => 'Kreatif',
                'tanggal_masuk' => '2021-04-15',
                'tanggal_lahir' => '1992-11-10',
                'jenis_kelamin' => 'Perempuan',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567893',
                'email' => 'dewi.lestari@example.com',
                'gaji_pokok' => 10000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Thamrin No. 90, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12348'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BCA',
                        'nomor_rekening' => '2233445566',
                        'nama_pemilik' => 'Dewi Lestari'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012348',
                'status_pajak' => 'K/1',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 700000,
                    'tunjangan_makan' => 1000000,
                    'tunjangan_kesehatan' => 400000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000005',
                'nama_lengkap' => 'Rudi Hermawan',
                'posisi' => 'Operator Finishing',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2021-06-01',
                'tanggal_lahir' => '1993-07-05',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567894',
                'email' => 'rudi.hermawan@example.com',
                'gaji_pokok' => 7500000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Asia Afrika No. 56, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12349'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'Mandiri',
                        'nomor_rekening' => '3344556677',
                        'nama_pemilik' => 'Rudi Hermawan'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012349',
                'status_pajak' => 'K/1',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000006',
                'nama_lengkap' => 'Maya Sari',
                'posisi' => 'Admin Produksi',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2021-08-15',
                'tanggal_lahir' => '1994-04-20',
                'jenis_kelamin' => 'Perempuan',
                'status_pernikahan' => 'Belum Menikah',
                'nomor_telepon' => '081234567895',
                'email' => 'maya.sari@example.com',
                'gaji_pokok' => 7000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Kost',
                        'alamat_lengkap' => 'Jl. Diponegoro No. 34, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12350'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BNI',
                        'nomor_rekening' => '4455667788',
                        'nama_pemilik' => 'Maya Sari'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012350',
                'status_pajak' => 'TK/0',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000007',
                'nama_lengkap' => 'Andi Wijaya',
                'posisi' => 'Operator Printer',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2022-01-10',
                'tanggal_lahir' => '1996-09-15',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Belum Menikah',
                'nomor_telepon' => '081234567896',
                'email' => 'andi.wijaya@example.com',
                'gaji_pokok' => 7500000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Kost',
                        'alamat_lengkap' => 'Jl. Hayam Wuruk No. 67, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12351'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BCA',
                        'nomor_rekening' => '5566778899',
                        'nama_pemilik' => 'Andi Wijaya'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012351',
                'status_pajak' => 'TK/0',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000008',
                'nama_lengkap' => 'Nina Putri',
                'posisi' => 'Desainer Grafis',
                'departemen' => 'Kreatif',
                'tanggal_masuk' => '2022-03-01',
                'tanggal_lahir' => '1995-12-25',
                'jenis_kelamin' => 'Perempuan',
                'status_pernikahan' => 'Belum Menikah',
                'nomor_telepon' => '081234567897',
                'email' => 'nina.putri@example.com',
                'gaji_pokok' => 9000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Kost',
                        'alamat_lengkap' => 'Jl. Gajah Mada No. 89, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12352'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'Mandiri',
                        'nomor_rekening' => '6677889900',
                        'nama_pemilik' => 'Nina Putri'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012352',
                'status_pajak' => 'TK/0',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 600000,
                    'tunjangan_makan' => 900000,
                    'tunjangan_kesehatan' => 350000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000009',
                'nama_lengkap' => 'Bambang Susilo',
                'posisi' => 'Operator Finishing',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2022-05-15',
                'tanggal_lahir' => '1994-02-10',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567898',
                'email' => 'bambang.susilo@example.com',
                'gaji_pokok' => 7500000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Veteran No. 45, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12353'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BNI',
                        'nomor_rekening' => '7788990011',
                        'nama_pemilik' => 'Bambang Susilo'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012353',
                'status_pajak' => 'K/1',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000010',
                'nama_lengkap' => 'Rina Wati',
                'posisi' => 'QC Inspector',
                'departemen' => 'Quality Control',
                'tanggal_masuk' => '2022-07-01',
                'tanggal_lahir' => '1993-05-20',
                'jenis_kelamin' => 'Perempuan',
                'status_pernikahan' => 'Menikah',
                'nomor_telepon' => '081234567899',
                'email' => 'rina.wati@example.com',
                'gaji_pokok' => 8500000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Rumah',
                        'alamat_lengkap' => 'Jl. Pangeran Antasari No. 78, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12354'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'BCA',
                        'nomor_rekening' => '8899001122',
                        'nama_pemilik' => 'Rina Wati'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012354',
                'status_pajak' => 'K/1',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 600000,
                    'tunjangan_makan' => 900000,
                    'tunjangan_kesehatan' => 350000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id_karyawan' => 'EMP-000011',
                'nama_lengkap' => 'Dedi Kurniawan',
                'posisi' => 'Operator Printer',
                'departemen' => 'Produksi',
                'tanggal_masuk' => '2022-09-15',
                'tanggal_lahir' => '1997-08-05',
                'jenis_kelamin' => 'Laki-laki',
                'status_pernikahan' => 'Belum Menikah',
                'nomor_telepon' => '081234567900',
                'email' => 'dedi.kurniawan@example.com',
                'gaji_pokok' => 7000000.00,
                'status' => 'Aktif',
                'alamat' => json_encode([
                    [
                        'tipe' => 'Kost',
                        'alamat_lengkap' => 'Jl. Sudirman No. 90, Jakarta',
                        'kota' => 'Jakarta',
                        'provinsi' => 'DKI Jakarta',
                        'kode_pos' => '12355'
                    ]
                ]),
                'rekening' => json_encode([
                    [
                        'bank' => 'Mandiri',
                        'nomor_rekening' => '9900112233',
                        'nama_pemilik' => 'Dedi Kurniawan'
                    ]
                ]),
                'alamat_utama' => 0,
                'rekening_utama' => 0,
                'npwp' => '123456789012355',
                'status_pajak' => 'TK/0',
                'tarif_pajak' => 5.00,
                'estimasi_hari_kerja' => 22,
                'jam_kerja_per_hari' => 8,
                'komponen_gaji' => json_encode([
                    'tunjangan_transport' => 500000,
                    'tunjangan_makan' => 800000,
                    'tunjangan_kesehatan' => 300000
                ]),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
} 