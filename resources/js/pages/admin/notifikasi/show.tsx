'use client';

import AppAdminLayout from '@/layouts/app-admin-layout';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Show() {
    const { notifikasi }: any = usePage().props;

    const statusBadge = (status: string) => {
        if (status === 'diterima') {
            return (
                <span className="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700">
                    Disetujui
                </span>
            );
        } else if (status === 'ditolak') {
            return (
                <span className="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700">
                    Ditolak
                </span>
            );
        } else {
            return (
                <span className="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-700">
                    Menunggu Verifikasi
                </span>
            );
        }
    };

    return (
        <AppAdminLayout
            breadcrumbs={[
                { title: 'Notifikasi', href: route('admin.dashboard.notifikasi.index') },
                { title: 'Detail', href: '#' },
            ]}
        >
            <Head title="Detail Notifikasi" />

            <div className="mx-auto max-w-4xl space-y-6 p-6">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div className="flex-1">
                        <div className="flex items-center gap-3">
                            <h1 className="text-2xl font-bold text-gray-900">
                                {notifikasi.kabupaten?.tipe ?? 'Kabupaten'} {notifikasi.kabupaten?.name}
                            </h1>
                            {statusBadge(notifikasi?.terverifikasi)}
                        </div>
                        <p className="mt-2 text-sm text-gray-600">{notifikasi.deskripsi}</p>
                    </div>
                    <Link
                        href={route('admin.dashboard.notifikasi.index')}
                        className="ml-4 text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        ← Kembali
                    </Link>
                </div>

                {/* Main Content */}
                <div className="space-y-6">
                    {/* Info Cards */}
                    <div className="grid gap-4 sm:grid-cols-2">
                        {/* Jumlah Iuran */}
                        <div className="rounded-lg border border-green-200 bg-green-50 p-6">
                            <div className="text-sm font-medium text-green-900">
                                Jumlah Iuran
                            </div>
                            <div className="mt-2 text-3xl font-bold text-green-700">
                                {notifikasi.jumlah
                                    ? `Rp ${parseInt(notifikasi.jumlah).toLocaleString('id-ID')}`
                                    : 'Rp 0'}
                            </div>
                        </div>

                        {/* Tanggal */}
                        <div className="rounded-lg border border-blue-200 bg-blue-50 p-6">
                            <div className="text-sm font-medium text-blue-900">
                                Tanggal Pembayaran
                            </div>
                            <div className="mt-2 text-xl font-semibold text-blue-700">
                                {notifikasi?.tanggal || '-'}
                            </div>
                        </div>
                    </div>

                    {/* Payment Info */}
                    <div className="rounded-lg border border-gray-200 bg-white p-6">
                        <h3 className="mb-4 text-base font-semibold text-gray-900">Informasi Pembayaran</h3>
                        <div className="rounded-lg bg-blue-50 p-4">
                            <div>
                                <p className="font-medium text-blue-900">Pembayaran via Midtrans</p>
                                <p className="mt-1 text-sm text-blue-700">
                                    Pembayaran dilakukan melalui Midtrans Payment Gateway dan terverifikasi secara otomatis.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Additional Info */}
                    <div className="rounded-lg border border-gray-200 bg-white p-6">
                        <h3 className="mb-4 text-base font-semibold text-gray-900">Informasi Tambahan</h3>
                        <dl className="space-y-3">
                            <div className="flex justify-between border-b border-gray-100 pb-3">
                                <dt className="text-sm font-medium text-gray-500">Wilayah</dt>
                                <dd className="text-sm font-semibold text-gray-900">
                                    {notifikasi.kabupaten?.tipe ?? 'Kabupaten'} {notifikasi.kabupaten?.name}
                                </dd>
                            </div>
                            <div className="flex justify-between border-b border-gray-100 pb-3">
                                <dt className="text-sm font-medium text-gray-500">Status</dt>
                                <dd>{statusBadge(notifikasi?.terverifikasi)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt className="text-sm font-medium text-gray-500">Tanggal</dt>
                                <dd className="text-sm font-semibold text-gray-900">{notifikasi?.tanggal || '-'}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </AppAdminLayout>
    );
}
