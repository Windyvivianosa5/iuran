'use client';

import React from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { formatNumber, parseNumber } from '@/utils/formatInput';
import { Head, Link, router, usePage } from '@inertiajs/react';

import { toast } from 'sonner';

// Extend Window interface for Midtrans
declare global {
    interface Window {
        snap?: {
            pay: (token: string, options: {
                onSuccess?: (result: any) => void;
                onPending?: (result: any) => void;
                onError?: (result: any) => void;
                onClose?: () => void;
            }) => void;
        };
    }
}

// Define props interface
interface PageProps {
    midtransClientKey: string;
    [key: string]: any; // Index signature for Inertia compatibility
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard Kabupaten',
        href: '/kabupaten/dashboard',
    },
    {
        title: 'Iuran',
        href: '/kabupaten/dashboard/iuran',
    },
    {
        title: 'Bayar Iuran',
        href: '/kabupaten/dashboard/iuran/create',
    },
];

export default function Create() {
    const { midtransClientKey } = usePage<PageProps>().props;
    const [amount, setAmount] = React.useState('');
    const [description, setDescription] = React.useState('');
    const [isProcessing, setIsProcessing] = React.useState(false);
    const [paymentStatus, setPaymentStatus] = React.useState<'idle' | 'success' | 'pending' | 'failed'>('idle');
    const [configError, setConfigError] = React.useState(false);

    // Load Midtrans Snap script
    React.useEffect(() => {
        console.log('Midtrans Client Key:', midtransClientKey);
        
        if (!midtransClientKey || midtransClientKey === '') {
            console.error('Midtrans Client Key is not configured!');
            setConfigError(true);
            toast.error('Konfigurasi Midtrans belum lengkap. Silakan hubungi administrator.');
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', midtransClientKey);
        script.onload = () => {
            console.log('Midtrans Snap.js loaded successfully');
        };
        script.onerror = () => {
            console.error('Failed to load Midtrans Snap.js');
            toast.error('Gagal memuat Midtrans. Silakan refresh halaman.');
        };
        document.body.appendChild(script);

        return () => {
            if (document.body.contains(script)) {
                document.body.removeChild(script);
            }
        };
    }, [midtransClientKey]);

    const handlePayment = async (e: React.FormEvent) => {
        e.preventDefault();

        const numericAmount = parseNumber(amount);
        
        if (!numericAmount || numericAmount < 1000) {
            toast.error('Jumlah pembayaran minimal Rp 1.000');
            return;
        }

        if (configError) {
            toast.error('Konfigurasi Midtrans belum lengkap. Silakan hubungi administrator.');
            return;
        }

        setIsProcessing(true);

        try {
            console.log('Creating transaction...');
            const response = await fetch('/kabupaten/transaction/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    amount: numericAmount,
                    description: description || 'Pembayaran Iuran PGRI',
                }),
            });

            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server error:', errorText);
                throw new Error(`Server error: ${response.status}`);
            }

            const data = await response.json();
            console.log('Response data:', data);

            if (data.success && data.snap_token) {
                // Check if snap is loaded
                if (typeof window.snap === 'undefined' || !window.snap) {
                    console.error('Midtrans Snap not loaded');
                    toast.error('Midtrans belum siap. Silakan refresh halaman.');
                    setIsProcessing(false);
                    return;
                }

                console.log('Opening Midtrans payment popup...');
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result: any) {
                        console.log('Payment success:', result);
                        setPaymentStatus('success');
                        toast.success('Pembayaran berhasil!');
                        setTimeout(() => {
                            router.visit('/kabupaten/dashboard/iuran');
                        }, 2000);
                    },
                    onPending: function(result: any) {
                        console.log('Payment pending:', result);
                        setPaymentStatus('pending');
                        toast.info('Pembayaran sedang diproses');
                        setIsProcessing(false);
                    },
                    onError: function(result: any) {
                        console.log('Payment error:', result);
                        setPaymentStatus('failed');
                        toast.error('Pembayaran gagal');
                        setIsProcessing(false);
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        setIsProcessing(false);
                    }
                });
            } else {
                console.error('Invalid response:', data);
                toast.error(data.message || 'Gagal membuat transaksi');
                setIsProcessing(false);
            }
        } catch (error) {
            console.error('Payment error:', error);
            toast.error('Terjadi kesalahan saat memproses pembayaran. Silakan cek console untuk detail.');
            setIsProcessing(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bayar Iuran dengan Midtrans" />
            <div className="flex flex-col gap-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-800">Bayar Iuran dengan Midtrans</h1>
                </div>

                {/* Payment Status Alert */}
                {paymentStatus === 'success' && (
                    <div className="rounded-lg border border-green-200 bg-green-50 p-4">
                        <div className="flex items-center gap-3">
                            <div>
                                <h3 className="font-semibold text-green-800">Pembayaran Berhasil!</h3>
                                <p className="text-sm text-green-700">Transaksi Anda telah berhasil diproses. Anda akan diarahkan kembali...</p>
                            </div>
                        </div>
                    </div>
                )}

                {paymentStatus === 'pending' && (
                    <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                        <div className="flex items-center gap-3">
                            <div>
                                <h3 className="font-semibold text-yellow-800">Pembayaran Sedang Diproses</h3>
                                <p className="text-sm text-yellow-700">Transaksi Anda sedang dalam proses verifikasi.</p>
                            </div>
                        </div>
                    </div>
                )}

                {paymentStatus === 'failed' && (
                    <div className="rounded-lg border border-red-200 bg-red-50 p-4">
                        <div className="flex items-center gap-3">
                            <div>
                                <h3 className="font-semibold text-red-800">Pembayaran Gagal</h3>
                                <p className="text-sm text-red-700">Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.</p>
                            </div>
                        </div>
                    </div>
                )}

                <Card className="w-full max-w-3xl">
                    <CardContent className="p-6">
                        <form onSubmit={handlePayment} className="space-y-6">
                            {/* Info Box */}
                            <div className="rounded-lg bg-blue-50 p-4">
                                <h3 className="mb-2 font-semibold text-blue-900">Informasi Pembayaran</h3>
                                <ul className="space-y-1 text-sm text-blue-800">
                                    <li>• Pembayaran menggunakan Midtrans Payment Gateway</li>
                                    <li>• Mendukung berbagai metode pembayaran (Kartu Kredit, Transfer Bank, E-Wallet, dll)</li>
                                    <li>• Transaksi aman dan terenkripsi</li>
                                    <li>• Jumlah minimal pembayaran: Rp 1.000</li>
                                </ul>
                            </div>

                            {/* Amount Input */}
                            <div className="grid gap-2">
                                <Label htmlFor="amount" className="text-base font-semibold">
                                    Jumlah Pembayaran <span className="text-red-500">*</span>
                                </Label>
                                <div className="relative">
                                    <span className="pointer-events-none absolute top-1/2 left-3 z-10 -translate-y-1/2 text-lg font-semibold text-gray-700">
                                        Rp
                                    </span>
                                    <Input
                                        type="text"
                                        id="amount"
                                        placeholder="0"
                                        value={formatNumber(amount)}
                                        onChange={(e) => setAmount(e.target.value)}
                                        className="h-12 pl-12 text-lg font-semibold"
                                        required
                                    />
                                </div>
                                <p className="text-xs text-gray-500">Minimal pembayaran Rp 1.000</p>
                            </div>

                            {/* Description Input */}
                            <div className="grid gap-2">
                                <Label htmlFor="description" className="text-base font-semibold">
                                    Deskripsi Pembayaran
                                </Label>
                                <Textarea
                                    id="description"
                                    placeholder="Contoh: Iuran Bulan Desember 2025"
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    className="min-h-[100px]"
                                />
                                <p className="text-xs text-gray-500">Opsional - Tambahkan catatan untuk pembayaran ini</p>
                            </div>

                            {/* Payment Methods Info */}
                            <div className="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <h4 className="mb-3 font-semibold text-gray-800">Metode Pembayaran yang Tersedia:</h4>
                                <div className="grid grid-cols-2 gap-3 text-sm text-gray-700 md:grid-cols-4">
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <span>Kartu Kredit</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-green-500"></div>
                                        <span>Transfer Bank</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-purple-500"></div>
                                        <span>E-Wallet</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-orange-500"></div>
                                        <span>Convenience Store</span>
                                    </div>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex flex-col gap-3 pt-4 sm:flex-row sm:justify-between">
                                <Link href="/kabupaten/dashboard/iuran">
                                    <Button type="button" variant="outline" className="w-full sm:w-auto" disabled={isProcessing}>
                                        ← Kembali
                                    </Button>
                                </Link>
                                <Button
                                    type="submit"
                                    disabled={isProcessing || !amount}
                                    className="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 sm:w-auto"
                                >
                                    {isProcessing ? (
                                        <>
                                            <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                            Memproses...
                                        </>
                                    ) : (
                                        <>
                                            Bayar Sekarang
                                        </>
                                    )}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                {/* Security Notice */}
                <div className="max-w-3xl text-center text-xs text-gray-500">
                    <p>Pembayaran Anda dilindungi dengan enkripsi SSL dan 3D Secure</p>
                    <p className="mt-1">Powered by Midtrans Payment Gateway</p>
                </div>
            </div>
        </AppLayout>
    );
}
