'use client';

import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppAdminLayout from '@/layouts/app-admin-layout';
import { type BreadcrumbItem } from '@/types';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan', href: '/admin/dashboard/settings' },
];

export default function SettingsIndex({ adminFee }: { adminFee: number }) {
    const { props } = usePage<any>();
    const { data, setData, post, processing, errors } = useForm({
        adminFee: adminFee || 4000,
    });

    const [flashMessage, setFlashMessage] = React.useState<{ type: 'success' | 'error'; message: string } | null>(null);

    React.useEffect(() => {
        if (props.flash?.success) {
            setFlashMessage({ type: 'success', message: props.flash.success });
            toast.success(props.flash.success);
            setTimeout(() => setFlashMessage(null), 5000);
        }
        if (props.flash?.error) {
            setFlashMessage({ type: 'error', message: props.flash.error });
            toast.error(props.flash.error);
            setTimeout(() => setFlashMessage(null), 5000);
        }
    }, [props.flash]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/admin/dashboard/settings', {
            onSuccess: () => {
                // Success feedback handled by effect
            },
        });
    };

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Pengaturan Aplikasi" />

            <div className="space-y-6 p-6">
                {/* Flash Messages */}
                {flashMessage && (
                    <div className={`rounded-lg border p-4 ${
                        flashMessage.type === 'success' 
                            ? 'border-green-200 bg-green-50 text-green-800' 
                            : 'border-red-200 bg-red-50 text-red-800'
                    }`}>
                        <div className="flex items-center justify-between">
                            <p className="text-sm font-medium">{flashMessage.message}</p>
                            <button 
                                onClick={() => setFlashMessage(null)}
                                className="text-gray-400 hover:text-gray-600"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                )}

                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Aplikasi</h1>
                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola parameter dan biaya sistem yang digunakan secara global
                    </p>
                </div>

                <div className="max-w-2xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Biaya Transaksi / Layanan</CardTitle>
                            <CardDescription>
                                Tentukan nominal biaya admin tetap yang dikenakan kepada kabupaten/kota pada setiap transaksi pembayaran iuran.
                            </CardDescription>
                        </CardHeader>
                        <form onSubmit={handleSubmit}>
                            <CardContent className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="adminFee">Biaya Admin (Rp)</Label>
                                    <div className="relative">
                                        <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                            Rp
                                        </span>
                                        <Input
                                            id="adminFee"
                                            type="number"
                                            className="pl-10"
                                            value={data.adminFee}
                                            onChange={(e) => setData('adminFee', parseInt(e.target.value) || 0)}
                                            placeholder="4000"
                                            min={0}
                                            required
                                        />
                                    </div>
                                    {errors.adminFee && (
                                        <p className="text-sm text-red-600">{errors.adminFee}</p>
                                    )}
                                    <p className="text-xs text-gray-500">
                                        Biaya admin ini akan ditambahkan ke jumlah transfer total pembayaran iuran kabupaten/kota di Midtrans.
                                    </p>
                                </div>
                            </CardContent>
                            <CardFooter className="flex justify-end border-t px-6 py-4">
                                <Button type="submit" disabled={processing} className="bg-blue-600 hover:bg-blue-700">
                                    {processing ? 'Menyimpan...' : 'Simpan Pengaturan'}
                                </Button>
                            </CardFooter>
                        </form>
                    </Card>
                </div>
            </div>
        </AppAdminLayout>
    );
}
