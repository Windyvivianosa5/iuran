type LaporanPerKabupaten = {
    kabupaten: string;
    jumlahAnggota: number;
    iuran: number;
    januari: number;
    februari: number;
    maret: number;
    april: number;
    mei: number;
    juni: number;
    juli: number;
    agustus: number;
    september: number;
    oktober: number;
    november: number;
    desember: number;
    totalIuran: number;
    totalSeharusnya: number;
    kekurangan: number;
};

export function generateLaporan(data: any[], kabupatens: any[] = [], tahun = new Date().getFullYear()) {
    const IURAN_TETAP_BASE = tahun == 2024 ? 1200 : 1600; // 2023-24(1200),2025 dari januari (1600)

    const IURAN_TETAP = IURAN_TETAP_BASE + (new Date().getFullYear() - tahun) * 400;

    const bulanMap = [
        'januari',
        'februari',
        'maret',
        'april',
        'mei',
        'juni',
        'juli',
        'agustus',
        'september',
        'oktober',
        'november',
        'desember',
    ] as const;

    const result: Record<string, LaporanPerKabupaten> = {};

    // Filter data sesuai tahun dan status settlement
    const filteredData = data.filter((item: any) => {
        // Cek status: 'settlement' (midtrans) atau 'diterima' (legacy/manual)
        const isSettled = item.status === 'settlement' || item.terverifikasi === 'diterima';
        if (!isSettled) return false;

        // Gunakan bulan_pembayaran (YYYY-MM) sebagai sumber utama penentuan tahun
        if (item.bulan_pembayaran) {
            const [bpTahun] = item.bulan_pembayaran.split('-');
            return parseInt(bpTahun) === tahun;
        }

        // Fallback: gunakan settlement_time / created_at / tanggal
        const dateString = item.settlement_time || item.created_at || item.tanggal;
        const tanggal = new Date(dateString);
        return tanggal.getFullYear() === tahun;
    });

    // Proses data transaksi yang difilter
    filteredData.forEach((item: any) => {
        const kabupatenName = item.user?.nama_kabupaten || 'Tidak Diketahui';
        const anggota = item.user?.jumlah_anggota || 0;

        // Tentukan bulan dari bulan_pembayaran lebih dulu, lalu fallback ke tanggal transaksi
        let bulanIndex: number;
        if (item.bulan_pembayaran) {
            const [, bpBulan] = item.bulan_pembayaran.split('-');
            bulanIndex = parseInt(bpBulan) - 1; // 0-indexed
        } else {
            const dateString = item.settlement_time || item.created_at || item.tanggal;
            bulanIndex = new Date(dateString).getMonth();
        }

        const bulanKey = bulanMap[bulanIndex];

        // Gunakan gross_amount (midtrans) atau jumlah (legacy)
        const jumlah = parseFloat(item.gross_amount || item.jumlah);

        if (!result[kabupatenName]) {
            result[kabupatenName] = {
                kabupaten: kabupatenName,
                jumlahAnggota: anggota,
                iuran: IURAN_TETAP,
                januari: 0,
                februari: 0,
                maret: 0,
                april: 0,
                mei: 0,
                juni: 0,
                juli: 0,
                agustus: 0,
                september: 0,
                oktober: 0,
                november: 0,
                desember: 0,
                totalIuran: 0,
                totalSeharusnya: anggota * IURAN_TETAP_BASE * 12,
                kekurangan: 0,
            };
        }

        result[kabupatenName][bulanKey] += jumlah;
        result[kabupatenName].totalIuran += jumlah;
    });

    // Pastikan semua kabupaten dari database tetap muncul, walau tanpa data iuran
    kabupatens.forEach((kab: any) => {
        const kabupatenName = kab.nama_kabupaten;
        if (!result[kabupatenName]) {
            result[kabupatenName] = {
                kabupaten: kabupatenName,
                jumlahAnggota: kab.jumlah_anggota || 0,
                iuran: IURAN_TETAP,
                januari: 0,
                februari: 0,
                maret: 0,
                april: 0,
                mei: 0,
                juni: 0,
                juli: 0,
                agustus: 0,
                september: 0,
                oktober: 0,
                november: 0,
                desember: 0,
                totalIuran: 0,
                totalSeharusnya: (kab.jumlah_anggota || 0) * IURAN_TETAP_BASE * 12,
                kekurangan: 0,
            };
        }
    });

    // Hitung kekurangan iuran
    Object.values(result).forEach((row) => {
        row.kekurangan = row.totalSeharusnya - row.totalIuran;
    });

    return Object.values(result);
}
