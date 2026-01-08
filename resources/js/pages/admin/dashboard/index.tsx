'use client';

import { Card, CardContent } from '@/components/ui/card';
import { ChartConfig, ChartContainer, ChartTooltip } from '@/components/ui/chart';
import AppAdminLayout from '@/layouts/app-admin-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { CartesianGrid, Line, LineChart, XAxis, YAxis } from 'recharts';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Admin',
        href: 'admin/dashboard',
    },
];

const chartConfig = {
    pemasukan: {
        label: 'Pemasukan',
        color: '#22c55e',
    },
    totalIuran: {
        label: 'Total Iuran',
        color: '#3b82f6',
    },
} satisfies ChartConfig;

export default function DashboardAdmin() {
    const {
        laporans = [],
        totalMasuk = 0,
        jumlahTransaksi = 0,
        transaksiTerbaru = [],
        totalKabupaten = 0,
        notifikasi = [],
        laporanKabupaten = [],
    } = usePage().props as any;

    const [filterType, setFilterType] = useState('monthly');
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    
    // Collapsible states
    const [isChartOpen, setIsChartOpen] = useState(true);
    const [isTopKabupatenOpen, setIsTopKabupatenOpen] = useState(true);
    const [isTransaksiOpen, setIsTransaksiOpen] = useState(true);
    const [isNotifikasiOpen, setIsNotifikasiOpen] = useState(true);

    const chartData = laporans.map((laporan: any) => ({
        month: laporan.bulan,
        pemasukan: laporan.total_iuran,
    }));

    const getHighestIuranData = (laporans: any[]) => {
        return laporans
            .map((laporan: any) => ({
                kabupaten: laporan.kabupaten,
                totalIuran: laporan.total_iuran,
                jumlahTransaksi: laporan.jumlah_transaksi,
                status: laporan.status,
            }))
            .sort((a, b) => b.totalIuran - a.totalIuran);
    };

    const highestIuranData = getHighestIuranData(laporanKabupaten);

    const filterOptions = [
        { value: 'monthly', label: 'Pemasukan Bulanan' },
        { value: 'highest', label: 'Kabupaten Tertinggi' },
    ];

    const selectedFilter = filterOptions.find((option) => option.value === filterType);

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Admin" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div className="transition-all duration-300">
                    <h1 className="text-2xl font-bold text-gray-900">Dashboard Admin PGRI</h1>
                    <p className="mt-1 text-sm text-gray-500">Pantau seluruh aktivitas iuran dan laporan</p>
                </div>

                {/* Stat Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Card className="border-l-4 border-l-red-500 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Total Saldo Masuk</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">
                                Rp {Number(totalMasuk).toLocaleString()}
                            </div>
                        </CardContent>
                    </Card>
                    <Card className="border-l-4 border-l-green-500 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Jumlah Transaksi</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">{jumlahTransaksi}</div>
                        </CardContent>
                    </Card>
                    <Card className="border-l-4 border-l-blue-500 transition-all duration-300 hover:shadow-lg hover:scale-105">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Jumlah Kabupaten/Kota</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">{laporanKabupaten.length}</div>
                        </CardContent>
                    </Card>
                </div>

                {/* Chart - Collapsible */}
                <Card className="transition-shadow duration-300 hover:shadow-md">
                    <CardContent className="p-6">
                        <div className="mb-4 flex items-center justify-between">
                            <button
                                onClick={() => setIsChartOpen(!isChartOpen)}
                                className="flex items-center gap-2 text-lg font-semibold text-gray-900 transition-colors duration-200 hover:text-gray-700"
                            >
                                <svg
                                    className={`h-5 w-5 transition-transform duration-300 ${isChartOpen ? 'rotate-90' : ''}`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                                Grafik Data
                            </button>

                            {/* Filter Dropdown */}
                            {isChartOpen && (
                                <div className="relative">
                                    <button
                                        onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                                        className="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-50 hover:shadow-md"
                                    >
                                        {selectedFilter?.label}
                                        <svg className={`h-4 w-4 transition-transform duration-200 ${isDropdownOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {isDropdownOpen && (
                                        <div className="absolute right-0 top-full z-10 mt-1 w-48 rounded-md border border-gray-200 bg-white shadow-lg">
                                            {filterOptions.map((option) => (
                                                <button
                                                    key={option.value}
                                                    onClick={() => {
                                                        setFilterType(option.value);
                                                        setIsDropdownOpen(false);
                                                    }}
                                                    className={`w-full px-4 py-2 text-left text-sm transition-colors duration-150 hover:bg-gray-50 ${
                                                        filterType === option.value ? 'bg-blue-50 font-medium text-blue-700' : 'text-gray-700'
                                                    }`}
                                                >
                                                    {option.label}
                                                </button>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>

                        <div className={`overflow-hidden transition-all duration-500 ease-in-out ${isChartOpen ? 'max-h-[500px] opacity-100' : 'max-h-0 opacity-0'}`}>
                            <div className="!h-[400px] w-full">
                                <ChartContainer config={chartConfig} className="h-full w-full">
                                    {filterType === 'monthly' ? (
                                        <LineChart width={800} height={300} data={chartData}>
                                            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                                            <XAxis
                                                dataKey="month"
                                                tick={{ fontSize: 12, fill: '#64748b' }}
                                                tickLine={{ stroke: '#64748b' }}
                                                axisLine={{ stroke: '#64748b' }}
                                                tickFormatter={(value) => value.slice(0, 3)}
                                            />
                                            <YAxis
                                                tick={{ fontSize: 12, fill: '#64748b' }}
                                                tickLine={{ stroke: '#64748b' }}
                                                axisLine={{ stroke: '#64748b' }}
                                                tickFormatter={(value) => `${(value / 1000000).toFixed(0)}Jt`}
                                            />
                                            <ChartTooltip
                                                content={({ active, payload, label }) => {
                                                    if (active && payload && payload.length) {
                                                        return (
                                                            <div className="rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                                                                <p className="font-medium">{label}</p>
                                                                <p className="text-green-600">
                                                                    Pemasukan: Rp {Number(payload[0].value).toLocaleString()}
                                                                </p>
                                                            </div>
                                                        );
                                                    }
                                                    return null;
                                                }}
                                            />
                                            <Line
                                                type="monotone"
                                                dataKey="pemasukan"
                                                stroke="#22c55e"
                                                strokeWidth={3}
                                                dot={{ fill: '#22c55e', strokeWidth: 2, r: 6 }}
                                                activeDot={{ r: 8, fill: '#16a34a' }}
                                            />
                                        </LineChart>
                                    ) : (
                                        <LineChart width={800} height={300} data={highestIuranData}>
                                            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
                                            <XAxis
                                                dataKey="kabupaten"
                                                tick={{ fontSize: 10, fill: '#64748b' }}
                                                tickLine={{ stroke: '#64748b' }}
                                                axisLine={{ stroke: '#64748b' }}
                                                angle={-45}
                                                textAnchor="end"
                                                height={80}
                                                interval={0}
                                                tickFormatter={(value) => {
                                                    const words = value.split(' ');
                                                    if (words.length > 2) {
                                                        return words.slice(1, 3).join(' ');
                                                    }
                                                    return value;
                                                }}
                                            />
                                            <YAxis
                                                tick={{ fontSize: 12, fill: '#64748b' }}
                                                tickLine={{ stroke: '#64748b' }}
                                                axisLine={{ stroke: '#64748b' }}
                                                tickFormatter={(value) => `${(value / 1000000).toFixed(0)}Jt`}
                                            />
                                            <ChartTooltip
                                                content={({ active, payload, label }) => {
                                                    if (active && payload && payload.length) {
                                                        return (
                                                            <div className="rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                                                                <p className="font-medium">{label}</p>
                                                                <p className="text-blue-600">
                                                                    Total Iuran: Rp {Number(payload[0].value).toLocaleString()}
                                                                </p>
                                                                <p className="text-gray-600">
                                                                    Jumlah Transaksi: {payload[0].payload.jumlahTransaksi}
                                                                </p>
                                                            </div>
                                                        );
                                                    }
                                                    return null;
                                                }}
                                            />
                                            <Line
                                                type="monotone"
                                                dataKey="totalIuran"
                                                stroke="#3b82f6"
                                                strokeWidth={3}
                                                dot={{ fill: '#3b82f6', strokeWidth: 2, r: 6 }}
                                                activeDot={{ r: 8, fill: '#2563eb' }}
                                            />
                                        </LineChart>
                                    )}
                                </ChartContainer>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Top Kabupaten - Collapsible */}
                <Card className="transition-shadow duration-300 hover:shadow-md">
                    <CardContent className="p-6">
                        <button
                            onClick={() => {
                                const newState = !isTopKabupatenOpen;
                                setIsTopKabupatenOpen(newState);
                            }}
                            className="mb-4 flex w-full items-center gap-2 text-lg font-semibold text-gray-900 transition-colors duration-200 hover:text-gray-700"
                        >
                            <svg
                                className={`h-5 w-5 transition-transform duration-300 ${isTopKabupatenOpen ? 'rotate-90' : ''}`}
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                            </svg>
                            Top 5 Kabupaten/Kota
                        </button>

                        <div className={`overflow-hidden transition-all duration-500 ease-in-out ${isTopKabupatenOpen ? 'max-h-[600px] opacity-100' : 'max-h-0 opacity-0'}`}>
                            <div className="space-y-3">
                                {highestIuranData.slice(0, 5).map((item: any, index: number) => (
                                    <div
                                        key={index}
                                        className="flex items-center justify-between rounded-lg border border-gray-200 p-4 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className={`flex h-10 w-10 items-center justify-center rounded-full font-bold text-white ${
                                                index === 0 ? 'bg-yellow-500' : 
                                                index === 1 ? 'bg-gray-400' : 
                                                index === 2 ? 'bg-orange-600' : 
                                                'bg-blue-500'
                                            }`}>
                                                {index + 1}
                                            </div>
                                            <div>
                                                <div className="font-semibold text-gray-900">{item.kabupaten}</div>
                                                <div className="text-sm text-gray-500">{item.jumlahTransaksi} transaksi</div>
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <div className="font-bold text-green-600">
                                                Rp {Number(item.totalIuran).toLocaleString()}
                                            </div>
                                            <div className="text-xs text-gray-500">Total Iuran</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Transaksi & Notifikasi */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Transaksi Terbaru - Collapsible */}
                    <Card className="transition-shadow duration-300 hover:shadow-md">
                        <CardContent className="p-6">
                            <button
                                onClick={() => setIsTransaksiOpen(!isTransaksiOpen)}
                                className="mb-4 flex w-full items-center gap-2 text-lg font-semibold text-gray-900 transition-colors duration-200 hover:text-gray-700"
                            >
                                <svg
                                    className={`h-5 w-5 transition-transform duration-300 ${isTransaksiOpen ? 'rotate-90' : ''}`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                                Transaksi Terbaru
                            </button>
                            
                            <div className={`overflow-hidden transition-all duration-500 ease-in-out ${isTransaksiOpen ? 'max-h-[600px] opacity-100' : 'max-h-0 opacity-0'}`}>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="border-b border-gray-200 text-left">
                                                <th className="pb-3 font-medium text-gray-500">Bulan</th>
                                                <th className="pb-3 font-medium text-gray-500">Kabupaten</th>
                                                <th className="pb-3 font-medium text-gray-500">Total Iuran</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100">
                                            {transaksiTerbaru.map((item: any, index: number) => (
                                                <tr key={index} className="transition-colors duration-150 hover:bg-gray-50">
                                                    <td className="py-3 text-gray-900">{item.bulan}</td>
                                                    <td className="py-3 text-gray-600">{item.kabupaten}</td>
                                                    <td className="py-3 font-medium text-gray-900">
                                                        Rp {Number(item.total_iuran).toLocaleString()}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Notifikasi - Collapsible */}
                    <Card className="transition-shadow duration-300 hover:shadow-md">
                        <CardContent className="p-6">
                            <button
                                onClick={() => setIsNotifikasiOpen(!isNotifikasiOpen)}
                                className="mb-4 flex w-full items-center gap-2 text-lg font-semibold text-gray-900 transition-colors duration-200 hover:text-gray-700"
                            >
                                <svg
                                    className={`h-5 w-5 transition-transform duration-300 ${isNotifikasiOpen ? 'rotate-90' : ''}`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                                Notifikasi
                            </button>
                            
                            <div className={`overflow-hidden transition-all duration-500 ease-in-out ${isNotifikasiOpen ? 'max-h-[600px] opacity-100' : 'max-h-0 opacity-0'}`}>
                                <div className="space-y-3">
                                    {notifikasi.map((item: any, index: number) => (
                                        <a
                                            key={index}
                                            href={route('admin.dashboard.notifikasi.show', item.id)}
                                            className="block rounded-lg border border-gray-200 p-3 transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 hover:shadow-sm"
                                        >
                                            <div className="flex items-start justify-between">
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2">
                                                        <span className="inline-block rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                                            Baru
                                                        </span>
                                                        <span className="text-sm text-gray-900">{item.pesan}</span>
                                                    </div>
                                                </div>
                                                <span className="text-xs text-gray-500">{item.waktu}</span>
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppAdminLayout>
    );
}
