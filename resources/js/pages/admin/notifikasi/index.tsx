'use client';

import AppAdminLayout from '@/layouts/app-admin-layout';
import { formatTanggalIndonesiaManual } from '@/utils/formatdate';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function NotifikasiIndex() {
    const { notifikasis }: any = usePage().props;

    const getStatusBadge = (status: string) => {
        if (status === 'diterima') {
            return (
                <span className="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                    Disetujui
                </span>
            );
        }
        return null;
    };

    return (
        <AppAdminLayout breadcrumbs={[{ title: 'Notifikasi', href: route('admin.dashboard.notifikasi.index') }]}>
            <Head title="Notifikasi Admin - PGRI Riau" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Notifikasi Pembayaran</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Kelola dan verifikasi notifikasi pembayaran dari kabupaten/kota
                    </p>
                </div>

                {/* Notification List */}
                {notifikasis.length === 0 ? (
                    <div className="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-12 text-center">
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-200">
                            <span className="text-sm font-medium text-gray-500">Kosong</span>
                        </div>
                        <h3 className="mt-4 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                        <p className="mt-1 text-sm text-gray-500">Belum ada notifikasi yang masuk</p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {notifikasis.map((notif: any, index: number) => (
                            <motion.div
                                key={notif.id}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.3, delay: index * 0.05 }}
                                className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
                            >
                                <div className="p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="flex items-center gap-3">
                                                <Link
                                                    href={route('admin.dashboard.notifikasi.show', notif.id)}
                                                    className="text-lg font-semibold text-gray-900 hover:text-blue-600"
                                                >
                                                    {notif.kabupaten?.name}
                                                </Link>
                                                {getStatusBadge(notif.terverifikasi)}
                                            </div>

                                            <p className="mt-2 text-sm text-gray-600">{notif.deskripsi}</p>

                                            <div className="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                <span>{formatTanggalIndonesiaManual(notif.tanggal)}</span>
                                                {notif.jumlah && (
                                                    <span className="font-medium text-green-600">
                                                        Rp {Number(notif.jumlah).toLocaleString()}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Lihat Detail */}
                                    <div className="mt-4 flex items-center border-t border-gray-100 pt-4">
                                        <Link
                                            href={route('admin.dashboard.notifikasi.show', notif.id)}
                                            className="text-sm font-medium text-blue-600 hover:text-blue-700"
                                        >
                                            Lihat Detail →
                                        </Link>
                                    </div>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                )}
            </div>
        </AppAdminLayout>
    );
}
