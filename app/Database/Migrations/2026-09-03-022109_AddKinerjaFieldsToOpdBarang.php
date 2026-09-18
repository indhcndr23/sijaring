<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKinerjaFieldsToOpdBarang extends Migration
{
    public function up()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        // Tambah kolom jumlah unit (NULL = 1 unit)
        if (!$db->fieldExists('jumlah', 'opd_barang')) {
            $this->forge->addColumn('opd_barang', [
                'jumlah' => [
                    'type'       => 'TINYINT',
                    'constraint' => 3,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'id_barang',
                ],
            ]);
        }

        // Tambah kolom jumlah reset dalam 6 bulan
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

        // Tambah kolom total port (khusus switch)
        if (!$db->fieldExists('jumlah_port', 'opd_barang')) {
            $this->forge->addColumn('opd_barang', [
                'jumlah_port' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'jumlah_reset',
                ],
            ]);
        }

        // Tambah kolom port bermasalah
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

    public function down()
    {
        /** @var \CodeIgniter\Database\BaseConnection $db */
        $db = \Config\Database::connect();

        $cols = ['jumlah_port_bermasalah', 'jumlah_port', 'jumlah_reset', 'jumlah'];
        foreach ($cols as $col) {
            if ($db->fieldExists($col, 'opd_barang')) {
                $this->forge->dropColumn('opd_barang', $col);
            }
        }
    }
}