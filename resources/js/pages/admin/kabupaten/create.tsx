'use client';

import { Head, router, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppAdminLayout from '@/layouts/app-admin-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Kabupaten', href: '/admin/dashboard/kabupaten' },
    { title: 'Tambah Kabupaten', href: '/admin/dashboard/kabupaten/create' },
];

// Define form data interface
interface KabupatenFormData {
    nama_kabupaten: string;
    kode_kabupaten: string;
    jumlah_anggota: string;
    status: string;
    create_user: boolean;
    user_email: string;
    user_password: string;
    user_password_confirmation: string;
    [key: string]: any; // Index signature for Inertia compatibility
}

export default function CreateKabupaten() {
    const { data, setData, post, processing, errors } = useForm<KabupatenFormData>({
        nama_kabupaten: '',
        kode_kabupaten: '',
        jumlah_anggota: '',
        status: 'aktif',
        // User fields
        create_user: true,
        user_email: '',
        user_password: '',
        user_password_confirmation: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.dashboard.kabupaten.store'));
    };

    // Type-safe error checking
    const getError = (field: string) => {
        return (errors as any)?.[field];
    };

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Tambah Kabupaten" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Tambah Kabupaten/Kota</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Tambahkan data kabupaten/kota dan akun login ke sistem
                    </p>
                </div>

                {/* Form */}
                <Card className="max-w-2xl">
                    <CardContent className="p-6">
                        <form onSubmit={handleSubmit} className="space-y-8">
                            {/* Section: Data Kabupaten */}
                            <div className="space-y-4">
                                <div className="border-b border-gray-200 pb-2">
                                    <h3 className="text-lg font-semibold text-gray-900">Data Kabupaten</h3>
                                    <p className="text-sm text-gray-500">Informasi dasar kabupaten/kota</p>
                                </div>

                                {/* Nama Kabupaten */}
                                <div className="space-y-2">
                                    <Label htmlFor="nama_kabupaten">
                                        Nama Kabupaten/Kota <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="nama_kabupaten"
                                        type="text"
                                        value={data.nama_kabupaten}
                                        onChange={(e) => setData('nama_kabupaten', e.currentTarget.value)}
                                        placeholder="Contoh: Kabupaten Pelalawan"
                                        className={getError('nama_kabupaten') ? 'border-red-500' : ''}
                                    />
                                    {getError('nama_kabupaten') && (
                                        <p className="text-sm text-red-500">{getError('nama_kabupaten')}</p>
                                    )}
                                </div>

                                {/* Kode Kabupaten */}
                                <div className="space-y-2">
                                    <Label htmlFor="kode_kabupaten">
                                        Kode Kabupaten <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="kode_kabupaten"
                                        type="text"
                                        value={data.kode_kabupaten}
                                        onChange={(e) => setData('kode_kabupaten', e.currentTarget.value.toUpperCase())}
                                        placeholder="Contoh: PLW"
                                        maxLength={10}
                                        className={getError('kode_kabupaten') ? 'border-red-500' : ''}
                                    />
                                    {getError('kode_kabupaten') && (
                                        <p className="text-sm text-red-500">{getError('kode_kabupaten')}</p>
                                    )}
                                    <p className="text-xs text-gray-500">Maksimal 10 karakter</p>
                                </div>

                                {/* Jumlah Anggota */}
                                <div className="space-y-2">
                                    <Label htmlFor="jumlah_anggota">
                                        Jumlah Anggota <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="jumlah_anggota"
                                        type="number"
                                        value={data.jumlah_anggota}
                                        onChange={(e) => setData('jumlah_anggota', e.currentTarget.value)}
                                        placeholder="Contoh: 500"
                                        min="0"
                                        className={getError('jumlah_anggota') ? 'border-red-500' : ''}
                                    />
                                    {getError('jumlah_anggota') && (
                                        <p className="text-sm text-red-500">{getError('jumlah_anggota')}</p>
                                    )}
                                    <p className="text-xs text-gray-500">Jumlah anggota PGRI di kabupaten ini</p>
                                </div>

                                {/* Status */}
                                <div className="space-y-2">
                                    <Label htmlFor="status">
                                        Status <span className="text-red-500">*</span>
                                    </Label>
                                    <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                        <SelectTrigger className={getError('status') ? 'border-red-500' : ''}>
                                            <SelectValue placeholder="Pilih status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="aktif">Aktif</SelectItem>
                                            <SelectItem value="nonaktif">Nonaktif</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {getError('status') && (
                                        <p className="text-sm text-red-500">{getError('status')}</p>
                                    )}
                                </div>
                            </div>

                            {/* Section: Akun Login */}
                            <div className="space-y-4">
                                <div className="border-b border-gray-200 pb-2">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">Akun Login Kabupaten</h3>
                                            <p className="text-sm text-gray-500">Buat akun untuk user kabupaten</p>
                                        </div>
                                        <label className="flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                checked={data.create_user}
                                                onChange={(e) => setData('create_user', e.currentTarget.checked)}
                                                className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span className="text-sm font-medium text-gray-700">Buat akun</span>
                                        </label>
                                    </div>
                                </div>

                                {data.create_user && (
                                    <div className="space-y-4 rounded-lg bg-blue-50 p-4">
                                        {/* Akun Login */}

                                        {/* Email */}
                                        <div className="space-y-2">
                                            <Label htmlFor="user_email">
                                                Email <span className="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                id="user_email"
                                                type="email"
                                                value={data.user_email}
                                                onChange={(e) => setData('user_email', e.currentTarget.value)}
                                                placeholder="Contoh: pelalawan@pgri.com"
                                                className={getError('user_email') ? 'border-red-500' : ''}
                                            />
                                            {getError('user_email') && (
                                                <p className="text-sm text-red-500">{getError('user_email')}</p>
                                            )}
                                            <p className="text-xs text-gray-600">Email untuk login ke sistem</p>
                                        </div>

                                        {/* Password */}
                                        <div className="space-y-2">
                                            <Label htmlFor="user_password">
                                                Password <span className="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                id="user_password"
                                                type="password"
                                                value={data.user_password}
                                                onChange={(e) => setData('user_password', e.currentTarget.value)}
                                                placeholder="Minimal 8 karakter"
                                                className={getError('user_password') ? 'border-red-500' : ''}
                                            />
                                            {getError('user_password') && (
                                                <p className="text-sm text-red-500">{getError('user_password')}</p>
                                            )}
                                        </div>

                                        {/* Konfirmasi Password */}
                                        <div className="space-y-2">
                                            <Label htmlFor="user_password_confirmation">
                                                Konfirmasi Password <span className="text-red-500">*</span>
                                            </Label>
                                            <Input
                                                id="user_password_confirmation"
                                                type="password"
                                                value={data.user_password_confirmation}
                                                onChange={(e) => setData('user_password_confirmation', e.currentTarget.value)}
                                                placeholder="Ketik ulang password"
                                                className={getError('user_password_confirmation') ? 'border-red-500' : ''}
                                            />
                                            {getError('user_password_confirmation') && (
                                                <p className="text-sm text-red-500">{getError('user_password_confirmation')}</p>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Buttons */}
                            <div className="flex gap-3 border-t border-gray-200 pt-6">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => router.visit(route('admin.dashboard.kabupaten.index'))}
                                    disabled={processing}
                                >
                                    Batal
                                </Button>
                                <Button
                                    type="submit"
                                    className="bg-blue-600 hover:bg-blue-700"
                                    disabled={processing}
                                >
                                    {processing ? 'Menyimpan...' : 'Simpan'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppAdminLayout>
    );
}
