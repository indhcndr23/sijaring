<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterSubVariabel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sub_variabel' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_variabel' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_sub_variabel' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            // Filter opsional: kalau diisi, kepemilikan yang dihitung
            // cuma yang jenis_barang-nya sama (mis. sub-variabel "Switch"
            // cuma menghitung opd_barang dengan id_barang ini)
            'jenis_barang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'sumber_tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['OPD', 'KEPEMILIKAN', 'KOMBINASI'],
                'default'    => 'KEPEMILIKAN',
            ],
            'field_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'formula' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nilai_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'nilai_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 100,
            ],
            'arah' => [
                'type'       => 'ENUM',
                'constraint' => ['MAX', 'MIN'],
                'default'    => 'MAX',
            ],
            'bobot' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_sub_variabel', true);
        $this->forge->addForeignKey('id_variabel', 'master_variabel', 'id_variabel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jenis_barang_id', 'barang', 'id_barang', 'SET NULL', 'CASCADE');
        $this->forge->createTable('master_sub_variabel');
    }

    public function down()
    {
        $this->forge->dropTable('master_sub_variabel');
    }
}