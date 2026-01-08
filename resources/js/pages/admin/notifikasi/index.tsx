'use client';

import { useState } from 'react';
import AppAdminLayout from '@/layouts/app-admin-layout';
import { formatTanggalIndonesiaManual } from '@/utils/formatdate';
import { Inertia } from '@inertiajs/inertia';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Toaster, toast } from 'react-hot-toast';

export default function NotifikasiIndex() {
    const { notifikasis }: any = usePage().props;
    const [activeTab, setActiveTab] = useState<'semua' | 'pending' | 'diterima' | 'ditolak'>('semua');

    const handleApprove = (id: number) => {
        if (confirm('Apakah Anda yakin ingin menyetujui notifikasi ini?')) {
            Inertia.post(
                route('admin.dashboard.notifikasi.markAsRead', id),
                {},
                {
                    onSuccess: () => toast.success('Notifikasi telah disetujui'),
                },
            );
        }
    };

    const handleCancel = (id: number) => {
        if (confirm('Apakah Anda yakin ingin menolak notifikasi ini?')) {
            Inertia.post(
                route('admin.dashboard.notifikasi.markAsCancel', id),
                {},
                {
                    onSuccess: () => toast.success('Notifikasi telah ditolak'),
                },
            );
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'diterima':
                return (
                    <span className="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                        Disetujui
                    </span>
                );
            case 'ditolak':
                return (
                    <span className="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">
                        Ditolak
                    </span>
                );
            default:
                return (
                    <span className="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700">
                        Menunggu
                    </span>
                );
        }
    };

    // Filter notifikasi berdasarkan tab
    const filteredNotifikasis = notifikasis.filter((notif: any) => {
        if (activeTab === 'semua') return true;
        return notif.terverifikasi === activeTab;
    });

    // Hitung jumlah untuk setiap status
    const counts = {
        semua: notifikasis.length,
        pending: notifikasis.filter((n: any) => n.terverifikasi === 'pending').length,
        diterima: notifikasis.filter((n: any) => n.terverifikasi === 'diterima').length,
        ditolak: notifikasis.filter((n: any) => n.terverifikasi === 'ditolak').length,
    };

    return (
        <AppAdminLayout breadcrumbs={[{ title: 'Notifikasi', href: route('admin.dashboard.notifikasi.index') }]}>
            <Head title="Notifikasi Admin" />
            <Toaster position="top-right" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Notifikasi Pembayaran</h1>
                    <p className="mt-1 text-sm text-gray-500">Kelola dan verifikasi notifikasi pembayaran dari kabupaten/kota</p>
                </div>

                {/* Tabs */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8">
                        <button
                            onClick={() => setActiveTab('semua')}
                            className={`whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors ${
                                activeTab === 'semua'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                            }`}
                        >
                            Semua
                            <span className="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                {counts.semua}
                            </span>
                        </button>
                        <button
                            onClick={() => setActiveTab('pending')}
                            className={`whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors ${
                                activeTab === 'pending'
                                    ? 'border-yellow-500 text-yellow-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                            }`}
                        >
                            Menunggu
                            {counts.pending > 0 && (
                                <span className="ml-2 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700">
                                    {counts.pending}
                                </span>
                            )}
                        </button>
                        <button
                            onClick={() => setActiveTab('diterima')}
                            className={`whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors ${
                                activeTab === 'diterima'
                                    ? 'border-green-500 text-green-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                            }`}
                        >
                            Disetujui
                            {counts.diterima > 0 && (
                                <span className="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                    {counts.diterima}
                                </span>
                            )}
                        </button>
                        <button
                            onClick={() => setActiveTab('ditolak')}
                            className={`whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors ${
                                activeTab === 'ditolak'
                                    ? 'border-red-500 text-red-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
                            }`}
                        >
                            Ditolak
                            {counts.ditolak > 0 && (
                                <span className="ml-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">
                                    {counts.ditolak}
                                </span>
                            )}
                        </button>
                    </nav>
                </div>

                {/* Notification List */}
                {filteredNotifikasis.length === 0 ? (
                    <div className="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-12 text-center">
                        <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-200">
                            <span className="text-sm font-medium text-gray-500">Kosong</span>
                        </div>
                        <h3 className="mt-4 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            {activeTab === 'semua' 
                                ? 'Belum ada notifikasi yang masuk'
                                : `Tidak ada notifikasi dengan status ${activeTab}`
                            }
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {filteredNotifikasis.map((notif: any, index: number) => (
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
                                                <span>
                                                    {formatTanggalIndonesiaManual(notif.tanggal)}
                                                </span>
                                                {notif.jumlah && (
                                                    <span className="font-medium text-green-600">
                                                        Rp {Number(notif.jumlah).toLocaleString()}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="mt-4 flex items-center gap-3 border-t border-gray-100 pt-4">
                                        <Link
                                            href={route('admin.dashboard.notifikasi.show', notif.id)}
                                            className="text-sm font-medium text-blue-600 hover:text-blue-700"
                                        >
                                            Lihat Detail →
                                        </Link>
                                        
                                        {notif.terverifikasi === 'pending' && (
                                            <div className="ml-auto flex gap-2">
                                                <button
                                                    onClick={() => handleCancel(notif.id)}
                                                    className="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50"
                                                >
                                                    Tolak
                                                </button>
                                                <button
                                                    onClick={() => handleApprove(notif.id)}
                                                    className="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                                                >
                                                    Setujui
                                                </button>
                                            </div>
                                        )}
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
