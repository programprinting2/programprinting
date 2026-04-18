<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PembukuanController extends Controller
{
    public function index(): View
    {
        $menus = [
            [
                'title' => 'Akun Perkiraan',
                'description' => 'Kelola daftar akun (dummy display).',
                'route' => route('pembukuan.akun-perkiraan'),
                'icon' => 'briefcase',
                'color' => 'primary',
            ],
            [
                'title' => 'Pencatatan Beban',
                'description' => 'Input beban operasional (dummy display).',
                'route' => route('pembukuan.pencatatan-beban'),
                'icon' => 'edit-3',
                'color' => 'success',
            ],
            [
                'title' => 'Pencatatan Gaji',
                'description' => 'Rekap penggajian periode (dummy display).',
                'route' => route('pembukuan.pencatatan-gaji'),
                'icon' => 'file-text',
                'color' => 'warning',
            ],
            [
                'title' => 'Jurnal Umum',
                'description' => 'Daftar jurnal transaksi (dummy display).',
                'route' => route('pembukuan.jurnal-umum'),
                'icon' => 'calendar',
                'color' => 'info',
            ],
            [
                'title' => 'Histori Akun',
                'description' => 'Mutasi akun per periode (dummy display).',
                'route' => route('pembukuan.histori-akun'),
                'icon' => 'archive',
                'color' => 'secondary',
            ],
            [
                'title' => 'Log Aktivitas Jurnal',
                'description' => 'Riwayat aktivitas jurnal (dummy display).',
                'route' => route('pembukuan.log-aktivitas-jurnal'),
                'icon' => 'search',
                'color' => 'dark',
            ],
        ];

        return view('pages.pembukuan.index', compact('menus'));
    }

    public function akunPerkiraan(): View
    {
        $accounts = [
            ['kode' => '1101', 'nama' => 'Kas & Bank', 'tipe' => 'Kas & Bank', 'saldo' => -25335714, 'level' => 0],
            ['kode' => '110101', 'nama' => 'Kas Kecil', 'tipe' => 'Kas & Bank', 'saldo' => -25335714, 'level' => 1],
            ['kode' => '110102', 'nama' => 'Bank', 'tipe' => 'Kas & Bank', 'saldo' => 0, 'level' => 1],
            ['kode' => '1102', 'nama' => 'Setara Kas', 'tipe' => 'Kas & Bank', 'saldo' => 0, 'level' => 0],
            ['kode' => '110201', 'nama' => 'Deposito Bank', 'tipe' => 'Kas & Bank', 'saldo' => 0, 'level' => 1],
            ['kode' => '1103', 'nama' => 'Piutang Usaha', 'tipe' => 'Piutang Usaha', 'saldo' => 0, 'level' => 0],
            ['kode' => '110301', 'nama' => 'Piutang Usaha IDR', 'tipe' => 'Piutang Usaha', 'saldo' => 0, 'level' => 1],
            ['kode' => '110302', 'nama' => 'Uang Muka Pembelian IDR', 'tipe' => 'Piutang Usaha', 'saldo' => 0, 'level' => 1],
            ['kode' => '1104', 'nama' => 'Persediaan', 'tipe' => 'Persediaan', 'saldo' => 217793904, 'level' => 0],
            ['kode' => '110401', 'nama' => 'Persediaan Barang Jadi', 'tipe' => 'Persediaan', 'saldo' => 193079750, 'level' => 1],
            ['kode' => '110402', 'nama' => 'Persediaan Terkirim', 'tipe' => 'Persediaan', 'saldo' => 0, 'level' => 1],
            ['kode' => '110403', 'nama' => 'Persediaan Dalam Proses', 'tipe' => 'Persediaan', 'saldo' => 22826154, 'level' => 1],
            ['kode' => '110404', 'nama' => 'Persediaan Bahan Baku', 'tipe' => 'Persediaan', 'saldo' => 0, 'level' => 1],
            ['kode' => '110405', 'nama' => 'Persediaan NG Goods', 'tipe' => 'Persediaan', 'saldo' => 1888000, 'level' => 1],
            ['kode' => '1105', 'nama' => 'Aset Lancar Lainnya', 'tipe' => 'Aset Lancar Lainnya', 'saldo' => 0, 'level' => 0],
            ['kode' => '110501', 'nama' => 'Perlengkapan Kantor', 'tipe' => 'Aset Lancar Lainnya', 'saldo' => 0, 'level' => 1],
            ['kode' => '110502', 'nama' => 'Sewa Gedung Dibayar Dimuka', 'tipe' => 'Aset Lancar Lainnya', 'saldo' => 0, 'level' => 1],
        ];

        $selectedAccount = [
            'tipe' => 'Kas & Bank',
            'kode' => '1101',
            'nama' => 'Kas & Bank',
            'saldo' => -25335714,
        ];

        return view('pages.pembukuan.akun-perkiraan', compact('accounts', 'selectedAccount'));
    }

    public function pencatatanBeban(): View
    {
        $bebanDraft = [
            [
                'tanggal' => '2026-04-05',
                'nomor' => 'BBN-2026-0405-01',
                'jenis' => 'Listrik & Air',
                'akun_beban' => '5120 - Beban Operasional',
                'akun_bayar' => '1110 - Kas',
                'nominal' => 1750000,
                'status' => 'Draft',
            ],
            [
                'tanggal' => '2026-04-09',
                'nomor' => 'BBN-2026-0409-02',
                'jenis' => 'Servis Mesin',
                'akun_beban' => '5120 - Beban Operasional',
                'akun_bayar' => '1120 - Bank',
                'nominal' => 980000,
                'status' => 'Posted',
            ],
            [
                'tanggal' => '2026-04-12',
                'nomor' => 'BBN-2026-0412-03',
                'jenis' => 'Internet Kantor',
                'akun_beban' => '5120 - Beban Operasional',
                'akun_bayar' => '1110 - Kas',
                'nominal' => 450000,
                'status' => 'Draft',
            ],
        ];

        return view('pages.pembukuan.pencatatan-beban', compact('bebanDraft'));
    }

    public function pencatatanGaji(): View
    {
        $periode = 'April 2026';

        $payrollDraft = [
            ['nama' => 'Agus Pratama', 'jabatan' => 'Operator Cetak', 'gaji_pokok' => 3500000, 'tunjangan' => 350000, 'potongan' => 50000],
            ['nama' => 'Rina Marlina', 'jabatan' => 'Admin', 'gaji_pokok' => 4200000, 'tunjangan' => 500000, 'potongan' => 75000],
            ['nama' => 'Budi Santoso', 'jabatan' => 'Finishing', 'gaji_pokok' => 3300000, 'tunjangan' => 300000, 'potongan' => 25000],
        ];

        return view('pages.pembukuan.pencatatan-gaji', compact('periode', 'payrollDraft'));
    }

    public function jurnalUmum(): View
    {
        $journals = [
            [
                'tanggal' => '2026-04-05',
                'nomor' => 'JU-2026-00031',
                'keterangan' => 'Pencatatan beban listrik (dummy)',
                'debit' => 1750000,
                'kredit' => 1750000,
                'status' => 'Posted',
            ],
            [
                'tanggal' => '2026-04-09',
                'nomor' => 'JU-2026-00032',
                'keterangan' => 'Pencatatan servis mesin (dummy)',
                'debit' => 980000,
                'kredit' => 980000,
                'status' => 'Posted',
            ],
            [
                'tanggal' => '2026-04-12',
                'nomor' => 'JU-2026-00033',
                'keterangan' => 'Draft beban internet (dummy)',
                'debit' => 450000,
                'kredit' => 450000,
                'status' => 'Draft',
            ],
        ];

        return view('pages.pembukuan.jurnal-umum', compact('journals'));
    }

    public function historiAkun(): View
    {
        $akunTerpilih = '1110 - Kas';

        $ledgerRows = [
            ['tanggal' => '2026-04-01', 'ref' => 'SALDO-AWAL', 'keterangan' => 'Saldo awal periode (dummy)', 'debit' => 10000000, 'kredit' => 0],
            ['tanggal' => '2026-04-05', 'ref' => 'JU-2026-00031', 'keterangan' => 'Pembayaran beban listrik (dummy)', 'debit' => 0, 'kredit' => 1750000],
            ['tanggal' => '2026-04-12', 'ref' => 'JU-2026-00033', 'keterangan' => 'Draft pembayaran internet (dummy)', 'debit' => 0, 'kredit' => 450000],
            ['tanggal' => '2026-04-18', 'ref' => 'ADJ-2026-00001', 'keterangan' => 'Koreksi kas kecil (dummy)', 'debit' => 250000, 'kredit' => 0],
        ];

        return view('pages.pembukuan.histori-akun', compact('akunTerpilih', 'ledgerRows'));
    }

    public function logAktivitasJurnal(): View
    {
        $logs = [
            ['waktu' => '2026-04-12 08:22:11', 'user' => 'Administrator', 'aksi' => 'Membuat draft jurnal', 'target' => 'JU-2026-00033', 'level' => 'Info'],
            ['waktu' => '2026-04-12 08:35:54', 'user' => 'Administrator', 'aksi' => 'Mengubah nominal debit', 'target' => 'JU-2026-00033', 'level' => 'Warning'],
            ['waktu' => '2026-04-12 09:10:03', 'user' => 'Supervisor', 'aksi' => 'Mem-posting jurnal', 'target' => 'JU-2026-00032', 'level' => 'Info'],
            ['waktu' => '2026-04-12 09:25:19', 'user' => 'Supervisor', 'aksi' => 'Membatalkan posting', 'target' => 'JU-2026-00031', 'level' => 'Critical'],
        ];

        return view('pages.pembukuan.log-aktivitas-jurnal', compact('logs'));
    }
}
