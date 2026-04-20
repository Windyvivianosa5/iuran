'use client';

import React from 'react';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

import { Card, CardContent } from '@/components/ui/card';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard Kabupaten',
    href: 'kabupaten/dashboard/iuran',
  },
];

export default function DashboardKabupatenIuran({ transactions }: { transactions: any }) {
  const { midtransClientKey, isActive } = usePage().props as any;
  const [isProcessing, setIsProcessing] = React.useState(false);

  // Load Midtrans Snap script
  React.useEffect(() => {
    if (!midtransClientKey) return;

    const script = document.createElement('script');
    script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', midtransClientKey);
    document.body.appendChild(script);

    return () => {
      if (document.body.contains(script)) {
        document.body.removeChild(script);
      }
    };
  }, [midtransClientKey]);

  const formatRupiah = (value: number) =>
    `Rp ${Number(value).toLocaleString('id-ID')}`;

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID', {
      timeZone: 'Asia/Jakarta',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    }) + ' WIB';
  };

  const formatBulanPembayaran = (value: string) => {
    if (!value) return '-';
    const [year, month] = value.split('-');
    const date = new Date(parseInt(year), parseInt(month) - 1, 1);
    return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
  };

  const handleContinuePayment = (transaction: any) => {
    if (!transaction.snap_token) {
      toast.error('Token pembayaran tidak tersedia');
      return;
    }

    setIsProcessing(true);

    // @ts-ignore
    if (typeof window.snap === 'undefined') {
      toast.error('Midtrans belum siap. Silakan refresh halaman.');
      setIsProcessing(false);
      return;
    }

    // @ts-ignore
    window.snap.pay(transaction.snap_token, {
      onSuccess: function(result: any) {
        console.log('Payment success:', result);
        toast.success('Pembayaran berhasil!');
        setTimeout(() => {
          router.reload();
        }, 1500);
      },
      onPending: function(result: any) {
        console.log('Payment pending:', result);
        toast.info('Pembayaran sedang diproses');
        setIsProcessing(false);
      },
      onError: function(result: any) {
        console.log('Payment error:', result);
        toast.error('Pembayaran gagal');
        setIsProcessing(false);
      },
      onClose: function() {
        console.log('Payment popup closed');
        setIsProcessing(false);
      }
    });
  };

  const getPaymentStatusBadge = (status: string) => {
    const badges: Record<string, { color: string; text: string }> = {
      settlement: { color: 'bg-green-100 text-green-800 border-green-300', text: 'Berhasil' },
      pending: { color: 'bg-yellow-100 text-yellow-800 border-yellow-300', text: 'Menunggu' },
      cancel: { color: 'bg-red-100 text-red-800 border-red-300', text: 'Dibatalkan' },
      deny: { color: 'bg-red-100 text-red-800 border-red-300', text: 'Ditolak' },
      expire: { color: 'bg-gray-100 text-gray-800 border-gray-300', text: 'Kadaluarsa' },
      failure: { color: 'bg-red-100 text-red-800 border-red-300', text: 'Gagal' },
    };
    
    const badge = badges[status] || { color: 'bg-gray-100 text-gray-800 border-gray-300', text: status };
    
    return (
      <div className={`inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium ${badge.color}`}>
        {badge.text}
      </div>
    );
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Daftar Transaksi" />
      <div className="flex flex-col gap-6 p-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Daftar Transaksi Pembayaran</h1>
          </div>
         {
          isActive === "aktif" ? (
            <Link href="iuran/create">
              <Button variant="default" className="bg-gradient-to-r from-blue-600 to-blue-700 shadow hover:from-blue-700 hover:to-blue-800">
                Bayar Iuran
              </Button>
            </Link>
          ) : (
            <Button 
              variant="default" 
              className="bg-gray-400 cursor-not-allowed opacity-60 dark:bg-gray-600 dark:text-gray-300"
              onClick={() => toast.error('Akun tidak aktif, Hubungi Admin')}
            >
              Akun Tidak Aktif
            </Button>
          )
         }
        </div>

        {/* Info Box */}
        <div className="rounded-lg bg-blue-50 p-4">
          <h3 className="mb-2 font-semibold text-blue-900 dark:text-blue-900">Informasi</h3>
          <p className="text-sm text-blue-800 dark:text-blue-800">
            Semua pembayaran dilakukan melalui Midtrans Payment Gateway. Klik "Bayar Iuran" untuk melakukan pembayaran baru.
            Untuk transaksi pending, klik "Lanjutkan Bayar" untuk menyelesaikan pembayaran.
          </p>
        </div>

        <Card>
          <CardContent className="overflow-x-auto p-4">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID Pembayaran</TableHead>
                  <TableHead>Bulan Pembayaran</TableHead>
                  <TableHead>Deskripsi</TableHead>
                  <TableHead>Nominal</TableHead>
                  <TableHead>Metode</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Tanggal</TableHead>
                  <TableHead className="text-right">Aksi</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {transactions?.length > 0 ? (
                  transactions.map((transaction: any) => (
                    <TableRow
                      key={transaction.id}
                      className="border-b transition hover:bg-gray-50 dark:hover:bg-gray-800"
                    >
                      <TableCell className="font-mono text-xs">{transaction.order_id}</TableCell>
                      <TableCell>
                        {transaction.bulan_pembayaran ? (
                          <span className="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                            {formatBulanPembayaran(transaction.bulan_pembayaran)}
                          </span>
                        ) : (
                          <span className="text-gray-400 text-sm">-</span>
                        )}
                      </TableCell>
                      <TableCell className="font-medium">{transaction.description}</TableCell>
                      <TableCell className="font-semibold text-blue-700">
                        {formatRupiah(transaction.gross_amount)}
                      </TableCell>
                      <TableCell>
                        {transaction.payment_type ? (
                          <span className="capitalize text-sm">
                            {transaction.payment_type.replace('_', ' ')}
                          </span>
                        ) : (
                          <span className="text-gray-400 text-sm">-</span>
                        )}
                      </TableCell>
                      <TableCell>{getPaymentStatusBadge(transaction.status)}</TableCell>
                      <TableCell className="text-sm text-gray-600">
                        {formatDate(transaction.created_at)}
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          {transaction.status === 'pending' && transaction.snap_token && (
                            <Button
                              variant="default"
                              size="sm"
                              className="gap-1.5 bg-yellow-600 hover:bg-yellow-700"
                              onClick={() => handleContinuePayment(transaction)}
                              disabled={isProcessing}
                            >
                              Lanjutkan Bayar
                            </Button>
                          )}
                          <Link href={`/kabupaten/dashboard/iuran/${transaction.id}`}>
                            <Button variant="ghost" size="sm" className="gap-1.5">
                              Detail
                            </Button>
                          </Link>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={8} className="py-8 text-center text-gray-500">
                      <div className="flex flex-col items-center gap-2">
                        <p className="font-medium">Belum ada transaksi pembayaran</p>
                        <p className="text-sm">Klik "Bayar Iuran" untuk melakukan pembayaran pertama</p>
                      </div>
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        {/* Summary Card */}
        {transactions?.length > 0 && (
          <div className="grid gap-4 md:grid-cols-3">
            <Card className="border-l-4 border-l-blue-500">
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Total Transaksi</p>
                <p className="text-2xl font-bold text-gray-900">{transactions.length}</p>
              </CardContent>
            </Card>
            <Card className="border-l-4 border-l-green-500">
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Berhasil</p>
                <p className="text-2xl font-bold text-green-700">
                  {transactions.filter((t: any) => t.status === 'settlement').length}
                </p>
              </CardContent>
            </Card>
            <Card className="border-l-4 border-l-yellow-500">
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Menunggu</p>
                <p className="text-2xl font-bold text-yellow-700">
                  {transactions.filter((t: any) => t.status === 'pending').length}
                </p>
              </CardContent>
            </Card>
          </div>
        )}
      </div>
    </AppLayout>
  );
}

