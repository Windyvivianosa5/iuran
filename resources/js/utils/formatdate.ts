export function formatToMySQLDatetime(date: any) {
    const d = new Date(date);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const dateTime = new Date();

    const hh = String(dateTime.getHours()).padStart(2, '0');
    const mi = String(dateTime.getMinutes()).padStart(2, '0');
    const ss = String(dateTime.getSeconds()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd} ${hh}:${mi}:${ss}`;
}

export function formatTanggalIndonesiaManual(inputTanggal: any) {
    const bulanIndonesia = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    // Data dari server sudah dalam timezone WIB (Asia/Jakarta)
    // Parse langsung tanpa menambahkan Z
    const tanggal = new Date(inputTanggal.replace(' ', 'T'));

    const hari = String(tanggal.getDate()).padStart(2, '0');
    const bulan = bulanIndonesia[tanggal.getMonth()];
    const tahun = tanggal.getFullYear();
    const jam = String(tanggal.getHours()).padStart(2, '0');
    const menit = String(tanggal.getMinutes()).padStart(2, '0');
    const detik = String(tanggal.getSeconds()).padStart(2, '0');

    return `${hari} ${bulan} ${tahun} ${jam}:${menit}:${detik} WIB`;
}

export function formatTanggalSekarang(): string {
    const tanggal = new Date();
    const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const tanggalCetak = tanggal.getDate();
    const namaBulan = bulanIndo[tanggal.getMonth()];
    const tahun = tanggal.getFullYear();

    return `${tanggalCetak} ${namaBulan} ${tahun}`;
}
