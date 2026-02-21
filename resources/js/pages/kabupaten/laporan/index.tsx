'use client';

import { Head, usePage } from '@inertiajs/react';
import { pdf } from '@react-pdf/renderer';
import { useEffect, useRef, useState } from 'react';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

import { type BreadcrumbItem } from '@/types';

import { generateLaporan } from '@/utils/filterLaporan';
import { formatTanggalSekarang } from '@/utils/formatdate';
import { getRentangBulan } from '@/utils/rentangBulan';
import { terbilang } from '@/utils/terbilang';

import AppLayout from '@/layouts/app-layout';
import DocumentPDF from '@/pages/admin/pdf/document';
import KwitansiPDF from '@/pages/admin/pdf/kwitansi';
import { formatCurrency } from '@/utils/formatRupiah';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard Kabupaten', href: '/kabupaten/dashboard' },
    { title: 'Laporan', href: '/kabupaten/dashboard/laporan' },
];

// Type for laporan data
type LaporanData = {
    kabupaten: string;
    jumlahAnggota: number;
    totalIuran: number;
    totalSeharusnya: number;
    kekurangan: number;
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
};

export default function DashboardKabupatenLaporan() {
    const { iuran, kabupatens } = usePage().props as any;
    const { auth } = usePage().props as any;
    const [datas, setDatas] = useState<LaporanData[]>([]);
    const [yearSelect, setYearSelect] = useState(new Date().getFullYear().toString());
    const [isOpen, setIsOpen] = useState(false);
    const [dataKwitansi, setDataKwitansi] = useState<LaporanData | null>(null);
    // Year input ref
    const inputRef = useRef<HTMLInputElement>(null);

    const currentYear = new Date().getFullYear();
    const years = Array.from({ length: 5 }, (_, i) => currentYear + i);
    
    // Get current user's kabupaten name for highlighting
    const currentKabupatenName = auth.user.nama_kabupaten || auth.user.name;

    const handleFilterYear = (year: string) => {
        const laporan: any = generateLaporan(iuran, kabupatens || [], parseInt(year));
        setDatas(laporan);
    };

    const handlePrintAll = async () => {
        const blob = await pdf(<DocumentPDF data={datas} tahun={yearSelect} />).toBlob();
        const url = URL.createObjectURL(blob);
        window.open(url);
    };

    const handlePrint = async (data: any) => {
        // Validate data before printing
        if (!data || !data.totalIuran || !data.kabupaten) {
            alert('Data kwitansi tidak tersedia. Pastikan Anda memiliki transaksi yang sudah selesai.');
            return;
        }

        const jumlahTerbilang = terbilang(data.totalIuran);
        const bulanAktif = getRentangBulan(data);
        const tanggalCetak = formatTanggalSekarang();

        const blob = await pdf(
            <KwitansiPDF
                data={{
                    namaKabupaten: data.kabupaten,
                    jumlah: `Rp. ${data.totalIuran.toLocaleString()}`,
                    jumlahTerbilang,
                    bulan: bulanAktif,
                    tanggalCetak,
                }}
            />,
        ).toBlob();

        const url = URL.createObjectURL(blob);
        window.open(url);
    };
    const handleKwitansi = (datas: any) => {
        datas.map((data: any) => {
            if (data.kabupaten === currentKabupatenName) {
                setDataKwitansi(data);
            }
        });
    };

    useEffect(() => {
        handleFilterYear(yearSelect);
    }, [yearSelect]);

    useEffect(() => {
        handleKwitansi(datas);
    }, [datas]);

    useEffect(() => {
        if (isOpen && inputRef.current) {
            inputRef.current.focus();
        }
    }, [isOpen]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Admin" />
            <div className="w-full overflow-hidden p-6">
                {/* Select Tahun */}
                <div className="mb-4 flex items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <Select onValueChange={setYearSelect} open={isOpen} onOpenChange={setIsOpen}>
                            <SelectTrigger className="w-[100px]">
                                <SelectValue placeholder={yearSelect} />
                            </SelectTrigger>
                            <SelectContent>
                                <div className="px-2 pt-2 pb-1">
                                    <Input
                                        ref={inputRef}
                                        type="number"
                                        placeholder="Tulis manual"
                                        onKeyDown={(e) => {
                                            e.stopPropagation();
                                            if (e.key === 'Enter') {
                                                setYearSelect(e.currentTarget.value);
                                            }
                                        }}
                                    />
                                </div>
                                {years.map((tahun) => (
                                    <SelectItem key={tahun} value={tahun.toString()}>
                                        {tahun}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        <Button onClick={handlePrintAll}>Print Semua</Button>
                    </div>
                    <Button 
                        onClick={() => handlePrint(dataKwitansi)}
                        disabled={!dataKwitansi}
                        title={!dataKwitansi ? 'Tidak ada data kwitansi untuk kabupaten Anda' : 'Cetak kwitansi'}
                    >
                        Kwitansi
                    </Button>
                </div>

                {/* Info Box */}
                <div className="mb-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                    <div className="flex items-start gap-3">
                        <div className="flex-shrink-0">
                            {/* <svg className="h-5 w-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                            </svg> */}
                        </div>
                        <div className="flex-1">
                            <h3 className="text-sm font-semibold text-blue-900 dark:text-blue-300">Informasi Laporan</h3>
                            <p className="mt-1 text-sm text-blue-800 dark:text-blue-400">
                                Laporan ini menampilkan data iuran dari <strong>semua kabupaten</strong>. 
                                Baris dengan latar belakang biru adalah data kabupaten Anda.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Table Laporan */}
                <div className="mx-auto w-full px-4 md:max-w-[660px] xl:max-w-[1220px]">
                    <div className="overflow-x-auto">
                        <Table className="min-w-full">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>No</TableHead>
                                    <TableHead>Kabupaten</TableHead>
                                    <TableHead>Jumlah Anggota</TableHead>
                                    {[
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
                                    ].map((bulan) => (
                                        <TableHead key={bulan}>{bulan}</TableHead>
                                    ))}
                                    <TableHead>Total Iuran</TableHead>
                                    <TableHead>Total Seharusnya</TableHead>
                                    <TableHead>Kekurangan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {datas.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={20} className="text-center">
                                            Data laporan belum tersedia.
                                        </TableCell>
                                    </TableRow>
                                )}
                                {datas.map((item: any, index: number) => {
                                    // Check if this row is for the logged-in kabupaten
                                    const isCurrentUser = item.kabupaten === currentKabupatenName;
                                    
                                    return (
                                        <TableRow 
                                            key={item.kabupaten}
                                            className={isCurrentUser ? 'bg-blue-50 dark:bg-blue-900/20 font-semibold' : ''}
                                        >
                                            <TableCell>{index + 1}</TableCell>
                                            <TableCell className={isCurrentUser ? 'font-bold text-blue-700 dark:text-blue-400' : ''}>
                                                {item.kabupaten}
                                                {isCurrentUser && <span className="ml-2 text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full">Anda</span>}
                                            </TableCell>
                                            <TableCell>{item.jumlahAnggota}</TableCell>
                                            <TableCell>{formatCurrency(item.januari)}</TableCell>
                                            <TableCell>{formatCurrency(item.februari)}</TableCell>
                                            <TableCell>{formatCurrency(item.maret)}</TableCell>
                                            <TableCell>{formatCurrency(item.april)}</TableCell>
                                            <TableCell>{formatCurrency(item.mei)}</TableCell>
                                            <TableCell>{formatCurrency(item.juni)}</TableCell>
                                            <TableCell>{formatCurrency(item.juli)}</TableCell>
                                            <TableCell>{formatCurrency(item.agustus)}</TableCell>
                                            <TableCell>{formatCurrency(item.september)}</TableCell>
                                            <TableCell>{formatCurrency(item.oktober)}</TableCell>
                                            <TableCell>{formatCurrency(item.november)}</TableCell>
                                            <TableCell>{formatCurrency(item.desember)}</TableCell>
                                            <TableCell>{formatCurrency(item.totalIuran)}</TableCell>
                                            <TableCell>{formatCurrency(item.totalSeharusnya)}</TableCell>
                                            <TableCell className={item.kekurangan > 0 ? 'font-bold text-red-500' : ''}>
                                                Rp. {item.kekurangan.toLocaleString()}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
