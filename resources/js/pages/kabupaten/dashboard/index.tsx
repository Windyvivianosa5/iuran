'use client';

import React from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { ChartConfig, ChartContainer, ChartTooltip } from '@/components/ui/chart';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { formatCurrency } from '@/utils/formatRupiah';
import { Head, usePage, router } from '@inertiajs/react';

import { CartesianGrid, Line, LineChart, XAxis, YAxis } from 'recharts';

const chartConfig = {
    pemasukan: {
        label: 'Pemasukan',
        color: '#22c55e',
    },
} satisfies ChartConfig;

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard Kabupaten', href: 'kabupaten/dashboard' }];

export default function DashboardKabupaten() {
    const { 
        totalMasuk = 0, 
        jumlahTransaksi = 0, 
        jumlahAnggota = 0, 
        laporans = [], 
        transaksiTerbaru = [], 
        namaUser,
        recentTransactions = [],
        midtransClientKey,
        unpaidMonths = []
    } = usePage().props as any;

    const [showPaymentModal, setShowPaymentModal] = React.useState(false);
    const [paymentAmount, setPaymentAmount] = React.useState('');
    const [paymentDescription, setPaymentDescription] = React.useState('');
    const [isProcessing, setIsProcessing] = React.useState(false);
    const [showUnpaidAlert, setShowUnpaidAlert] = React.useState(true);

    // Load Midtrans Snap script
    React.useEffect(() => {
        const script = document.createElement('script');
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', midtransClientKey);
        document.body.appendChild(script);

        return () => {
            document.body.removeChild(script);
        };
    }, [midtransClientKey]);

    const handlePayment = async () => {
        if (!paymentAmount || parseFloat(paymentAmount) < 1000) {
            alert('Jumlah pembayaran minimal Rp 1.000');
            return;
        }

        setIsProcessing(true);

        try {
            const response = await fetch('/kabupaten/transaction/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    amount: parseFloat(paymentAmount),
                    description: paymentDescription || 'Pembayaran Iuran PGRI',
                }),
            });

            const data = await response.json();

            if (data.success && data.snap_token) {
                // @ts-ignore
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result: any) {
                        console.log('Payment success:', result);
                        setShowPaymentModal(false);
                        setPaymentAmount('');
                        setPaymentDescription('');
                        router.reload();
                    },
                    onPending: function(result: any) {
                        console.log('Payment pending:', result);
                        setShowPaymentModal(false);
                        alert('Pembayaran sedang diproses');
                    },
                    onError: function(result: any) {
                        console.log('Payment error:', result);
                        alert('Pembayaran gagal');
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                    }
                });
            } else {
                alert(data.message || 'Gagal membuat transaksi');
            }
        } catch (error) {
            console.error('Payment error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran');
        } finally {
            setIsProcessing(false);
        }
    };

    const getStatusBadge = (status: string) => {
        const badges: Record<string, { color: string; text: string }> = {
            pending: { color: 'bg-yellow-100 text-yellow-800', text: 'Menunggu' },
            settlement: { color: 'bg-green-100 text-green-800', text: 'Berhasil' },
            cancel: { color: 'bg-red-100 text-red-800', text: 'Dibatalkan' },
            deny: { color: 'bg-red-100 text-red-800', text: 'Ditolak' },
            expire: { color: 'bg-gray-100 text-gray-800', text: 'Kadaluarsa' },
            failure: { color: 'bg-red-100 text-red-800', text: 'Gagal' },
        };
        const badge = badges[status] || { color: 'bg-gray-100 text-gray-800', text: status };
        return <span className={`px-2 py-1 rounded-full text-xs font-medium ${badge.color}`}>{badge.text}</span>;
    };

    const chartData = laporans.map((laporan: any) => ({
        month: laporan.bulan,
        pemasukan: laporan.total_iuran,
    }));

    const lastMonth = chartData[chartData.length - 2]?.pemasukan || 0;
    const thisMonth = chartData[chartData.length - 1]?.pemasukan || 0;
    const growth = lastMonth > 0 ? (((thisMonth - lastMonth) / lastMonth) * 100).toFixed(2) : '0';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Kabupaten" />

            <div className="flex min-h-screen flex-col gap-6 bg-gray-50 p-6 dark:bg-black">
                {/* Header Sambutan */}
                <div className="rounded-lg bg-white border border-gray-200 p-6 shadow-sm dark:bg-black dark:border-white">
                    <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Selamat Datang, {namaUser}</h1>
                    <p className="text-gray-600 mt-1 dark:text-white">Pantau aktivitas iuran dan laporan secara cepat dan efisien</p>
                </div>

                {/* Notifikasi Peringatan - Bulan yang Belum Dibayar */}
                {unpaidMonths.length > 0 && showUnpaidAlert && (
                    <div className="rounded-lg bg-orange-50 border border-orange-200 p-5 shadow-sm">
                        <div className="flex items-start justify-between gap-4">
                            <div className="flex-1">
                                <h3 className="text-base font-semibold text-orange-900 mb-2">
                                    Peringatan: Iuran Belum Dibayar
                                </h3>
                                <p className="text-sm text-orange-800 mb-3">
                                    Anda memiliki <span className="font-semibold">{unpaidMonths.length} bulan</span> yang belum melakukan pembayaran iuran di tahun ini:
                                </p>
                                <div className="flex flex-wrap gap-2 mb-3">
                                    {unpaidMonths.map((month: any, index: number) => (
                                        <span 
                                            key={index}
                                            className="px-3 py-1 rounded-md text-sm font-medium bg-white text-orange-800 border border-orange-200"
                                        >
                                            {month.name}
                                        </span>
                                    ))}
                                </div>
                                <p className="text-sm text-orange-700">
                                    Segera lakukan pembayaran untuk menghindari tunggakan.
                                </p>
                            </div>
                            <button
                                onClick={() => setShowUnpaidAlert(false)}
                                className="text-orange-400 hover:text-orange-600 transition-colors text-sm"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                )}

                {/* Statistik Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Card className="text-red-600 shadow-md">
                        <CardContent className="p-5">
                            <p className="text-sm">Total Saldo Masuk</p>
                            <p className="text-3xl font-bold">{formatCurrency(totalMasuk)}</p>
                        </CardContent>
                    </Card>

                    <Card className="text-green-600 shadow-md">
                        <CardContent className="p-5">
                            <p className="text-sm">Jumlah Transaksi</p>
                            <p className="text-3xl font-bold">{jumlahTransaksi}</p>
                        </CardContent>
                    </Card>

                    <Card className="text-blue-600 shadow-md">
                        <CardContent className="p-5">
                            <p className="text-sm">Jumlah Anggota</p>
                            <p className="text-3xl font-bold">{jumlahAnggota}</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Grafik Pemasukan */}
                <Card className="bg-white shadow-md dark:bg-black dark:border-white">
                    <CardContent className="p-5 dark:bg-black dark:border-white">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-gray-800 dark:text-white">Grafik Pemasukan Bulanan</h3>
                            {/* <p className="text-sm font-medium text-green-600">Pertumbuhan: +{growth}%</p> */}
                        </div>
                        <div className="!h-[400px] w-full">
                            <ChartContainer config={chartConfig} className="h-full w-full">
                                <LineChart
                                    width={800}
                                    height={300}
                                    data={chartData}
                             
                                >
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
                                        tickFormatter={(value) => `${(value / 1000000).toFixed(0)} Juta`}
                                    />
                                    <ChartTooltip
                                        content={({ active, payload, label }) => {
                                            if (active && payload && payload.length) {
                                                return (
                                                    <div className="rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                                                        <p className="font-medium">{label}</p>
                                                        <p className="text-green-600">Pemasukan: Rp {Number(payload[0].value).toLocaleString()}</p>
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
                            </ChartContainer>
                        </div>
                    </CardContent>
                </Card>

                {/* Transaksi Terbaru */}
                <Card className="bg-white shadow-md dark:bg-black dark:border-white">
                    <CardContent className="p-5 dark:bg-black dark:border-white">
                        <h3 className="text-lg font-semibold text-gray-800 mb-4 dark:text-white">Transaksi Terbaru</h3>
                        <table className="w-full border-separate border-spacing-y-2 text-sm">
                            <thead className="bg-gray-100 text-gray-600 dark:bg-black dark:border-white">
                                <tr>
                                    <th className="px-3 py-2 text-left">Bulan</th>
                                    <th className="px-3 py-2 text-left">Kabupaten</th>
                                    <th className="px-3 py-2 text-left">Total Iuran</th>
                                </tr>
                            </thead>
                            <tbody>
                                {transaksiTerbaru.length > 0 ? (
                                    transaksiTerbaru.map((item: any, index: number) => (
                                        <tr key={index} className="rounded-md bg-white shadow-sm transition hover:bg-gray-50 dark:bg-black dark:border-white">
                                            <td className="px-3 py-2 dark:text-white">{item.bulan}</td>
                                            <td className="px-3 py-2 dark:text-white">{item.kabupaten}</td>
                                            <td className="px-3 py-2 font-medium text-green-700">Rp {Number(item.total_iuran).toLocaleString()}</td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={3} className="py-4 text-center text-gray-500">
                                            Belum ada transaksi terbaru.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {/* Payment Button */}
        

                {/* Recent Transactions */}
                {recentTransactions.length > 0 && (
                    <Card className="bg-white shadow-md dark:bg-black dark:border-white">
                        <CardContent className="p-5 dark:bg-black dark:border-white">
                            <h3 className="text-lg font-semibold text-gray-800 mb-4 dark:text-white">Riwayat Pembayaran</h3>
                            <table className="w-full border-separate border-spacing-y-2 text-sm">
                                <thead className="bg-gray-100 text-gray-600 dark:bg-black dark:border-white">
                                    <tr>
                                        <th className="px-3 py-2 text-left">ID Pembayaran</th>
                                        <th className="px-3 py-2 text-left">Deskripsi</th>
                                        <th className="px-3 py-2 text-left">Jumlah</th>
                                        <th className="px-3 py-2 text-left">Status</th>
                                        <th className="px-3 py-2 text-left">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {recentTransactions.map((transaction: any) => (
                                        <tr key={transaction.id} className="rounded-md bg-white shadow-sm transition hover:bg-gray-50 dark:bg-black dark:border-white">
                                            <td className="px-3 py-2 font-mono text-xs">{transaction.order_id}</td>
                                            <td className="px-3 py-2">{transaction.description}</td>
                                            <td className="px-3 py-2 font-medium text-blue-700">
                                                {formatCurrency(transaction.gross_amount)}
                                            </td>
                                            <td className="px-3 py-2">{getStatusBadge(transaction.status)}</td>
                                            <td className="px-3 py-2 text-gray-600">{transaction.created_at}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </CardContent>
                    </Card>
                )}

                {/* Payment Modal */}
                {showPaymentModal && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                            <h2 className="mb-4 text-xl font-bold text-gray-800">Pembayaran Iuran</h2>
                            
                            <div className="mb-4">
                                <label className="mb-2 block text-sm font-medium text-gray-700">
                                    Jumlah Pembayaran (Rp)
                                </label>
                                <input
                                    type="number"
                                    value={paymentAmount}
                                    onChange={(e) => setPaymentAmount(e.target.value)}
                                    placeholder="Minimal Rp 1.000"
                                    className="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    min="1000"
                                />
                            </div>

                            <div className="mb-6">
                                <label className="mb-2 block text-sm font-medium text-gray-700">
                                    Deskripsi (Opsional)
                                </label>
                                <input
                                    type="text"
                                    value={paymentDescription}
                                    onChange={(e) => setPaymentDescription(e.target.value)}
                                    placeholder="Contoh: Iuran Bulan Desember 2025"
                                    className="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                />
                            </div>

                            <div className="flex gap-3">
                                <button
                                    onClick={() => setShowPaymentModal(false)}
                                    className="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-50"
                                    disabled={isProcessing}
                                >
                                    Batal
                                </button>
                                <button
                                    onClick={handlePayment}
                                    disabled={isProcessing}
                                    className="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700 disabled:bg-gray-400"
                                >
                                    {isProcessing ? 'Memproses...' : 'Bayar Sekarang'}
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
