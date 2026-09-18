<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveKinerjaFieldsFromOpdBarang extends Migration
{
    public function up()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        // Hapus kolom jumlah reset dalam 6 bulan
        if ($db->fieldExists('jumlah_reset', 'opd_barang')) {
            $this->forge->dropColumn('opd_barang', 'jumlah_reset');
        }

        // Hapus kolom port bermasalah
        if ($db->fieldExists('jumlah_port_bermasalah', 'opd_barang')) {
            $this->forge->dropColumn('opd_barang', 'jumlah_port_bermasalah');
        }
    }

    public function down()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        // Kembalikan kolom jumlah_reset (sesuai definisi awal di AddKinerjaFieldsToOpdBarang)
        if (!$db->fieldExists('jumlah_reset', 'opd_barang')) {
            $this->forge->addColumn('opd_barang', [
                'jumlah_reset' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'jumlah',
                ],
            ]);
        }

        // Kembalikan kolom jumlah_port_bermasalah (sesuai definisi awal)
        if (!$db->fieldExists('jumlah_port_bermasalah', 'opd_barang')) {
            $this->forge->addColumn('opd_barang', [
                'jumlah_port_bermasalah' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'jumlah_port',
                ],
            ]);
        }
    }
}