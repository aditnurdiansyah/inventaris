<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT Sumber Pangan Makmur', 'phone' => '021-5550101', 'email' => 'sales@sumberpangan.test',   'address' => 'Jl. Raya Bekasi No. 12, Bekasi'],
            ['name' => 'CV Berkah Sembako',       'phone' => '0812-3456-7801', 'email' => 'berkah@sembako.test',     'address' => 'Jl. Pasar Induk No. 5, Jakarta Timur'],
            ['name' => 'PT Mitra Kebersihan',     'phone' => '021-5550123', 'email' => 'order@mitrakebersihan.test', 'address' => 'Jl. Industri Raya No. 8, Tangerang'],
            ['name' => 'UD Maju Jaya ATK',        'phone' => '0813-9876-5402', 'email' => 'majujaya@atk.test',       'address' => 'Jl. Pemuda No. 21, Depok'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
