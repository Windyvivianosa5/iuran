'use client';

import { Head, usePage } from '@inertiajs/react';
import { pdf } from '@react-pdf/renderer';
import { useEffect, useRef, useState } from 'react';

import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

import AppAdminLayout from '@/layouts/app-admin-layout';
import { type BreadcrumbItem } from '@/types';

import { generateLaporan } from '@/utils/filterLaporan';
import { formatTanggalSekarang } from '@/utils/formatdate';
import { formatCurrency } from '@/utils/formatRupiah';
import { getRentangBulan } from '@/utils/rentangBulan';
import { terbilang } from '@/utils/terbilang';

import DocumentPDF from '../pdf/document';
import KwitansiPDF from '../pdf/kwitansi';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Laporan', href: 'admin/dashboard/laporan' }];

export default function DashboardAdminLaporan() {
    const { iuran } = usePage().props as any;

    const [datas, setDatas] = useState([]);
    const [yearSelect, setYearSelect] = useState(new Date().getFullYear().toString());
    const [isOpen, setIsOpen] = useState(false);

    const inputRef = useRef<HTMLInputElement>(null);
    const currentYear = 2024;
    const years = Array.from({ length: 5 }, (_, i) => 2024 + i);

    const handleFilterYear = (year: string) => {
        const laporan: any = generateLaporan(iuran, parseInt(year));
        setDatas(laporan);
        setYearSelect(year);
    };

    const handlePrintAll = async () => {
        const blob = await pdf(<DocumentPDF data={datas} tahun={yearSelect} />).toBlob();
        const url = URL.createObjectURL(blob);
        window.open(url);
    };

    const handlePrint = async (data: any) => {
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

    useEffect(() => {
        handleFilterYear(yearSelect);
    }, [yearSelect]);

    useEffect(() => {
        if (isOpen && inputRef.current) {
            inputRef.current.focus();
        }
    }, [isOpen]);

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Laporan Iuran" />
            <div className="space-y-6 p-6">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Rekapitulasi Iuran Kabupaten/Kota</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Total pemasukan iuran berdasarkan bulan dan kabupaten untuk tahun {yearSelect}
                        </p>
                    </div>
                    <Button onClick={handlePrintAll} className="bg-blue-600 hover:bg-blue-700">
                        Print Semua
                    </Button>
                </div>

                {/* Filter Tahun */}
                <div className="flex items-center gap-3">
                    <label className="text-sm font-medium text-gray-700">Tahun:</label>
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
                </div>

                {/* Tabel Laporan */}
                <div className="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                    <Table>
                        <TableHeader>
                            <TableRow className="bg-gray-50">
                                <TableHead className="font-semibold">No</TableHead>
                                <TableHead className="font-semibold">Kabupaten</TableHead>
                                <TableHead className="font-semibold">Jumlah Anggota</TableHead>
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
                                    <TableHead key={bulan} className="font-semibold">
                                        {bulan}
                                    </TableHead>
                                ))}
                                <TableHead className="font-semibold">Total Iuran</TableHead>
                                <TableHead className="font-semibold">Total Seharusnya</TableHead>
                                <TableHead className="font-semibold">Kekurangan</TableHead>
                                <TableHead className="text-right font-semibold">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                            {datas.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={20} className="py-8 text-center text-gray-500">
                                        Data laporan belum tersedia untuk tahun {yearSelect}
                                    </TableCell>
                                </TableRow>
                            )}
                            {datas.map((item: any, index: number) => (
                                <TableRow key={item.kabupaten} className="hover:bg-gray-50">
                                    <TableCell className="text-gray-600">{index + 1}</TableCell>
                                    <TableCell className="font-medium text-gray-900">{item.kabupaten}</TableCell>
                                    <TableCell className="text-gray-600">{item.jumlahAnggota}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.januari)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.februari)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.maret)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.april)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.mei)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.juni)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.juli)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.agustus)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.september)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.oktober)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.november)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.desember)}</TableCell>
                                    <TableCell className="font-semibold text-green-700">{formatCurrency(item.totalIuran)}</TableCell>
                                    <TableCell className="text-gray-600">{formatCurrency(item.totalSeharusnya)}</TableCell>
                                    <TableCell className={item.kekurangan > 0 ? 'font-semibold text-red-600' : 'text-gray-600'}>
                                        Rp. {item.kekurangan.toLocaleString()}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger asChild>
                                                <Button variant="outline" size="sm">
                                                    Aksi
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuItem onClick={() => handlePrint(item)}>Unduh Kwitansi</DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </AppAdminLayout>
    );
}
