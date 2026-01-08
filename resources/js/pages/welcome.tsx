'use client';

import { generateLaporan } from '@/utils/filterLaporan';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Welcome() {
    const { auth, iuran } = usePage<any>().props;
    const [datas, setDatas] = useState([]);

    useEffect(() => {
        const laporan: any = generateLaporan(iuran);
        setDatas(laporan);
    }, []);

    const fiturList = [
        { label: 'Rekap Keuangan Bulanan', description: 'Pantau pemasukan dan pengeluaran setiap bulan', color: 'border-blue-500' },
        { label: 'Laporan Per Kabupaten', description: 'Lihat laporan detail setiap kabupaten', color: 'border-green-500' },
        { label: 'Pembayaran Digital', description: 'Bayar iuran dengan Midtrans Payment Gateway', color: 'border-yellow-500' },
        { label: 'Analisis Otomatis', description: 'Grafik dan statistik pembayaran real-time', color: 'border-pink-500' },
    ];

    return (
        <>
            <Head title="Beranda" />

            <div className="min-h-screen w-full bg-gradient-to-br from-indigo-50 via-white to-indigo-100 text-gray-800 dark:from-[#0e0e0e] dark:via-[#0e0e0e] dark:to-[#0e0e0e] dark:text-white">
                {/* NAVIGATION */}
                <header className="mx-auto flex max-w-7xl items-center justify-between p-6">
                    <h1 className="text-2xl font-bold text-indigo-700 dark:text-indigo-400">PGRI Iuran</h1>
                    <nav className="space-x-4">
                        {auth.user ? (
                            <Link href={route('dashboard')} className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link href={route('login')} className="text-indigo-600 hover:underline">
                                    Masuk
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                {/* HERO SECTION */}
                <section className="mx-auto flex max-w-7xl flex-col-reverse items-center justify-between gap-8 px-6 py-12 lg:flex-row">
                    <div className="max-w-xl">
                        <h2 className="mb-4 text-4xl leading-tight font-extrabold text-gray-900 dark:text-white">Sistem Rekap Iuran Digital</h2>
                        <p className="mb-6 text-lg text-gray-600 dark:text-gray-300">
                            Menyajikan laporan keuangan yang transparan, cepat, dan akurat untuk seluruh organisasi daerah.
                        </p>
                        {/* <Link href="" className="inline-block rounded-md bg-indigo-600 px-6 py-3 font-semibold text-white hover:bg-indigo-700">
                            Lihat Rekap Iuran
                        </Link> */}
                    </div>
                    <img src="/pgri1.png" alt="PGRI Logo" className="h-64 w-64 object-contain" />
                </section>

                {/* FITUR SECTION */}
                <section className="mx-auto max-w-7xl px-6 py-12">
                    <h3 className="mb-8 text-center text-3xl font-semibold">Fitur Utama</h3>
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {fiturList.map((fitur, index) => (
                            <div
                                key={index}
                                className={`flex flex-col justify-center rounded-xl border-l-4 ${fitur.color} bg-white p-6 shadow transition hover:shadow-lg dark:bg-[#1e1e1e]`}
                            >
                                <p className="text-base font-semibold text-gray-800 dark:text-gray-100">{fitur.label}</p>
                                <p className="mt-2 text-sm text-gray-600 dark:text-gray-300">{fitur.description}</p>
                            </div>
                        ))}
                    </div>
                </section>

                {/* FOOTER */}
                <footer className="mt-20 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    © 2025 PGRI Riau • Sistem Rekap Iuran Digital
                </footer>
            </div>
        </>
    );
}
