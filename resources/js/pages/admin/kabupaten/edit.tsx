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
    { title: 'Edit Kabupaten', href: '#' },
];

export default function EditKabupaten({ kabupaten }: any) {
    const { data, setData, put, processing, errors } = useForm({
        nama_kabupaten: kabupaten.nama_kabupaten || '',
        kode_kabupaten: kabupaten.kode_kabupaten || '',
        jumlah_anggota: kabupaten.jumlah_anggota || '',
        status: kabupaten.status || 'aktif',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('admin.dashboard.kabupaten.update', kabupaten.id));
    };

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Kabupaten" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Edit Kabupaten/Kota</h1>
                    <p className="mt-1 text-sm text-gray-500">
                        Ubah data kabupaten/kota: {kabupaten.nama_kabupaten}
                    </p>
                </div>

                {/* Form */}
                <Card className="max-w-2xl">
                    <CardContent className="p-6">
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {/* Nama Kabupaten */}
                            <div className="space-y-2">
                                <Label htmlFor="nama_kabupaten">
                                    Nama Kabupaten/Kota <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="nama_kabupaten"
                                    type="text"
                                    value={data.nama_kabupaten}
                                    onChange={(e) => setData('nama_kabupaten', e.target.value)}
                                    placeholder="Contoh: Kabupaten Bandung"
                                    className={errors.nama_kabupaten ? 'border-red-500' : ''}
                                />
                                {errors.nama_kabupaten && (
                                    <p className="text-sm text-red-500">{errors.nama_kabupaten}</p>
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
                                    onChange={(e) => setData('kode_kabupaten', e.target.value.toUpperCase())}
                                    placeholder="Contoh: BDG"
                                    maxLength={10}
                                    className={errors.kode_kabupaten ? 'border-red-500' : ''}
                                />
                                {errors.kode_kabupaten && (
                                    <p className="text-sm text-red-500">{errors.kode_kabupaten}</p>
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
                                    onChange={(e) => setData('jumlah_anggota', e.target.value)}
                                    placeholder="Contoh: 500"
                                    min="0"
                                    className={errors.jumlah_anggota ? 'border-red-500' : ''}
                                />
                                {errors.jumlah_anggota && (
                                    <p className="text-sm text-red-500">{errors.jumlah_anggota}</p>
                                )}
                                <p className="text-xs text-gray-500">Jumlah anggota PGRI di kabupaten ini</p>
                            </div>

                            {/* Status */}
                            <div className="space-y-2">
                                <Label htmlFor="status">
                                    Status <span className="text-red-500">*</span>
                                </Label>
                                <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                    <SelectTrigger className={errors.status ? 'border-red-500' : ''}>
                                        <SelectValue placeholder="Pilih status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="aktif">Aktif</SelectItem>
                                        <SelectItem value="nonaktif">Nonaktif</SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.status && (
                                    <p className="text-sm text-red-500">{errors.status}</p>
                                )}
                            </div>

                            {/* Buttons */}
                            <div className="flex gap-3 pt-4">
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
                                    {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppAdminLayout>
    );
}
