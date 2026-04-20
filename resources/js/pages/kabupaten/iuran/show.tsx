import AppLayout from '@/layouts/app-layout';
import { Card, CardContent } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard Kabupaten', href: '/kabupaten/dashboard/iuran' },
  { title: 'Detail Transaksi', href: '#' },
];

export default function Show({ transaction }: { transaction: any }) {
  const formatRupiah = (val: number) =>
    `Rp ${Number(val).toLocaleString('id-ID')}`;

  const formatTanggal = (tanggal: string) =>
    new Date(tanggal).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });

  const getPaymentStatusBadge = (status: string) => {
    const badges: Record<string, { color: string; text: string }> = {
      settlement: { color: 'bg-green-100 text-green-800 border-green-300', text: 'Berhasil' },
      pending:    { color: 'bg-yellow-100 text-yellow-800 border-yellow-300', text: 'Menunggu' },
      cancel:     { color: 'bg-red-100 text-red-800 border-red-300', text: 'Dibatalkan' },
      deny:       { color: 'bg-red-100 text-red-800 border-red-300', text: 'Ditolak' },
      expire:     { color: 'bg-gray-100 text-gray-800 border-gray-300', text: 'Kadaluarsa' },
      failure:    { color: 'bg-red-100 text-red-800 border-red-300', text: 'Gagal' },
    };
    const badge = badges[status] || { color: 'bg-gray-100 text-gray-800 border-gray-300', text: status };
    return (
      <div className={`inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm font-medium ${badge.color}`}>
        {badge.text}
      </div>
    );
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Detail Transaksi" />
      <div className="flex flex-col gap-6 p-6">
        <div>
          <h1 className="text-xl font-bold text-gray-800">Detail Transaksi</h1>
        </div>

        <Card className="w-full max-w-2xl border bg-white shadow">
          <CardContent className="space-y-4 p-6 text-sm text-gray-700">
            <div className="rounded-lg bg-blue-50 p-4">
              <h3 className="mb-2 font-semibold text-blue-900">Informasi Pembayaran</h3>
              <p className="text-sm text-blue-800">Pembayaran dilakukan melalui Midtrans Payment Gateway</p>
            </div>

            <div>
              <span className="font-medium text-gray-600">ID Pembayaran:</span>{' '}
              <span className="font-mono text-xs text-black">{transaction.order_id}</span>
            </div>

            {transaction.transaction_id && (
              <div>
                <span className="font-medium text-gray-600">ID Transaksi:</span>{' '}
                <span className="font-mono text-xs text-black">{transaction.transaction_id}</span>
              </div>
            )}

            <div>
              <span className="font-medium text-gray-600">Jumlah:</span>{' '}
              <span className="text-lg font-semibold text-black">{formatRupiah(transaction.gross_amount)}</span>
            </div>

            <div>
              <span className="font-medium text-gray-600">Deskripsi:</span>{' '}
              <span className="text-black">{transaction.description}</span>
            </div>

            {transaction.bulan_pembayaran && (
              <div>
                <span className="font-medium text-gray-600">Bulan Pembayaran:</span>{' '}
                <span className="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700 border border-blue-200">
                  {(() => {
                    const [year, month] = transaction.bulan_pembayaran.split('-');
                    return new Date(parseInt(year), parseInt(month) - 1, 1)
                      .toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                  })()}
                </span>
              </div>
            )}

            <div>
              <span className="font-medium text-gray-600">Status Pembayaran:</span>{' '}
              {getPaymentStatusBadge(transaction.status)}
            </div>

            {transaction.payment_type && (
              <div>
                <span className="font-medium text-gray-600">Metode Pembayaran:</span>{' '}
                <span className="capitalize text-black">{transaction.payment_type.replace('_', ' ')}</span>
              </div>
            )}

            <div>
              <span className="font-medium text-gray-600">Tanggal Transaksi:</span>{' '}
              <span className="text-black">{formatTanggal(transaction.created_at)}</span>
            </div>

            {transaction.settlement_time && (
              <div>
                <span className="font-medium text-gray-600">Waktu Selesai:</span>{' '}
                <span className="text-black">{formatTanggal(transaction.settlement_time)}</span>
              </div>
            )}

            {transaction.status === 'settlement' && (
              <div className="rounded-lg border border-green-200 bg-green-50 p-4">
                <h3 className="font-semibold text-green-800">Pembayaran Berhasil!</h3>
                <p className="text-sm text-green-700">Transaksi telah berhasil diproses dan dikonfirmasi.</p>
              </div>
            )}
          </CardContent>
        </Card>

        <div>
          <Link href="/kabupaten/dashboard/iuran">
            <Button variant="secondary">Kembali</Button>
          </Link>
        </div>
      </div>
    </AppLayout>
  );
}
