<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToOpdBarang extends Migration
{
    public function up()
    {
        $fields = [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at' // Sesuaikan posisi kolom
            ],
        ];
        $this->forge->addColumn('opd_barang', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('opd_barang', 'deleted_at');
    }
}