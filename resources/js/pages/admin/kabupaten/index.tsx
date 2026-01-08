'use client';

import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppAdminLayout from '@/layouts/app-admin-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kelola Kabupaten', href: '/admin/dashboard/kabupaten' },
];

export default function KelolaKabupaten({ kabupaten }: any) {
    const [search, setSearch] = useState('');

    const filteredKabupaten = kabupaten.filter((item: any) =>
        item.nama_kabupaten.toLowerCase().includes(search.toLowerCase())
    );

    const handleDelete = (id: number) => {
        if (confirm('Apakah Anda yakin ingin menghapus kabupaten ini?')) {
            router.delete(route('admin.dashboard.kabupaten.destroy', id));
        }
    };

    return (
        <AppAdminLayout breadcrumbs={breadcrumbs}>
            <Head title="Kelola Kabupaten" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Kelola Kabupaten/Kota</h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Kelola data kabupaten/kota dan jumlah anggota PGRI
                        </p>
                    </div>
                    <Link href={route('admin.dashboard.kabupaten.create')}>
                        <Button className="bg-blue-600 hover:bg-blue-700">
                            Tambah Kabupaten
                        </Button>
                    </Link>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Card className="border-l-4 border-l-blue-500">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Total Kabupaten</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">{kabupaten.length}</div>
                        </CardContent>
                    </Card>
                    <Card className="border-l-4 border-l-green-500">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Total Anggota</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">
                                {kabupaten.reduce((sum: number, item: any) => sum + (item.jumlah_anggota || 0), 0).toLocaleString()}
                            </div>
                        </CardContent>
                    </Card>
                    <Card className="border-l-4 border-l-purple-500">
                        <CardContent className="p-6">
                            <div className="text-sm font-medium text-gray-500">Rata-rata Anggota</div>
                            <div className="mt-2 text-3xl font-bold text-gray-900">
                                {kabupaten.length > 0 
                                    ? Math.round(kabupaten.reduce((sum: number, item: any) => sum + (item.jumlah_anggota || 0), 0) / kabupaten.length).toLocaleString()
                                    : 0
                                }
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Search & Table */}
                <Card>
                    <CardContent className="p-6">
                        {/* Search */}
                        <div className="mb-4">
                            <Input
                                type="text"
                                placeholder="Cari kabupaten..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="max-w-sm"
                            />
                        </div>

                        {/* Table */}
                        <div className="overflow-x-auto rounded-lg border border-gray-200">
                            <Table>
                                <TableHeader>
                                    <TableRow className="bg-gray-50">
                                        <TableHead className="font-semibold">No</TableHead>
                                        <TableHead className="font-semibold">Nama Kabupaten/Kota</TableHead>
                                        <TableHead className="font-semibold">Kode</TableHead>
                                        <TableHead className="font-semibold">Jumlah Anggota</TableHead>
                                        <TableHead className="font-semibold">Status</TableHead>
                                        <TableHead className="font-semibold">User Login</TableHead>
                                        <TableHead className="text-right font-semibold">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {filteredKabupaten.length === 0 ? (
                                        <TableRow>
                                            <TableCell colSpan={7} className="py-8 text-center text-gray-500">
                                                {search ? 'Tidak ada kabupaten yang sesuai pencarian' : 'Belum ada data kabupaten'}
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        filteredKabupaten.map((item: any, index: number) => (
                                            <TableRow key={item.id} className="transition-colors duration-150 hover:bg-gray-50">
                                                <TableCell className="text-gray-600">{index + 1}</TableCell>
                                                <TableCell className="font-medium text-gray-900">{item.nama_kabupaten}</TableCell>
                                                <TableCell className="text-gray-600">{item.kode_kabupaten || '-'}</TableCell>
                                                <TableCell className="text-gray-600">
                                                    {item.jumlah_anggota?.toLocaleString() || 0} orang
                                                </TableCell>
                                                <TableCell>
                                                    <span className={`inline-block rounded-full px-2 py-1 text-xs font-medium ${
                                                        item.status === 'aktif' 
                                                            ? 'bg-green-100 text-green-700' 
                                                            : 'bg-gray-100 text-gray-700'
                                                    }`}>
                                                        {item.status === 'aktif' ? 'Aktif' : 'Nonaktif'}
                                                    </span>
                                                </TableCell>
                                                <TableCell>
                                                    {item.users && item.users.length > 0 ? (
                                                        <div className="flex items-center gap-2">
                                                            <span className="inline-block h-2 w-2 rounded-full bg-green-500"></span>
                                                            <span className="text-sm text-gray-700">{item.users[0].email}</span>
                                                        </div>
                                                    ) : (
                                                        <div className="flex items-center gap-2">
                                                            <span className="inline-block h-2 w-2 rounded-full bg-red-500"></span>
                                                            <span className="text-sm text-gray-500">Belum ada</span>
                                                        </div>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <DropdownMenu>
                                                        <DropdownMenuTrigger asChild>
                                                            <Button variant="outline" size="sm">
                                                                Aksi
                                                            </Button>
                                                        </DropdownMenuTrigger>
                                                        <DropdownMenuContent align="end">
                                                            <DropdownMenuItem asChild>
                                                                <Link href={route('admin.dashboard.kabupaten.edit', item.id)}>
                                                                    Edit
                                                                </Link>
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem
                                                                onClick={() => handleDelete(item.id)}
                                                                className="text-red-600"
                                                            >
                                                                Hapus
                                                            </DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </DropdownMenu>
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppAdminLayout>
    );
}
