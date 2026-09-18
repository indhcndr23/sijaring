<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTables extends Migration
{
    public function up()
    {
        // 1. TABEL ROLE_GROUP
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('role_group', true);

        // 2. TABEL ROLE
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_group_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('role', true);

        // 3. TABEL KELOMPOK_OPD
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kelompok_opd', true);

        // 4. TABEL BARANG
        $this->forge->addField([
            'id_barang'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis_barang' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'   => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'   => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_barang', true);
        $this->forge->createTable('barang', true);

        // 5. TABEL OPD
        $this->forge->addField([
            'id_opd'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kelompok_opd_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_opd'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'jumlah_pegawai'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jumlah_perangkat' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'luas_ruangan'     => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'rata_tamu'        => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jenis_dinding'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jumlah_lantai'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jumlah_ruangan'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'jenis_kabel'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alamat'           => ['type' => 'TEXT', 'null' => true],
            'nama_pic'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_hp_pic'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'latitude'         => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'longitude'        => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'created_at'       => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'       => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_opd', true);
        $this->forge->createTable('opd', true);

        // 6. TABEL OPD_BARANG
        $this->forge->addField([
            'id_opd_barang' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_opd'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_barang'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'merk'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tahun'         => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'kondisi'       => ['type' => 'ENUM', 'constraint' => ['Baik', 'Rusak Ringan', 'Rusak Berat'], 'default' => 'Baik'],
            'foto_bukti'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'keterangan'    => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_opd_barang', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('opd_barang', true);

        // 7. TABEL MASTER_DIMENSI
        $this->forge->addField([
            'id_dimensi'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_dimensi' => ['type' => 'VARCHAR', 'constraint' => 255],
            'bobot'        => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_dimensi', true);
        $this->forge->createTable('master_dimensi', true);

        // 8. TABEL MASTER_VARIABEL
        $this->forge->addField([
            'id_variabel'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_dimensi'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_variabel' => ['type' => 'VARCHAR', 'constraint' => 255],
            'sumber_tipe'   => ['type' => 'ENUM', 'constraint' => ['OPD', 'KEPEMILIKAN', 'KOMBINASI']],
            'field_source'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'formula'       => ['type' => 'TEXT', 'null' => true],
            'bobot'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00],
            'nilai_min'     => ['type' => 'FLOAT', 'null' => true],
            'nilai_max'     => ['type' => 'FLOAT', 'null' => true],
            'arah'          => ['type' => 'ENUM', 'constraint' => ['MAX', 'MIN'], 'default' => 'MAX'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_variabel', true);
        $this->forge->addForeignKey('id_dimensi', 'master_dimensi', 'id_dimensi', 'CASCADE', 'CASCADE');
        $this->forge->createTable('master_variabel', true);

        // 9. TABEL INDEX_PENILAIAN
        $this->forge->addField([
            'id_index'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_opd'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_index' => ['type' => 'INT', 'constraint' => 11],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_index', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->createTable('index_penilaian', true);

        // 10. TABEL HISTORI_INDEX
        $this->forge->addField([
            'id_histori'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_opd'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_index' => ['type' => 'INT', 'constraint' => 11],
            'jenis_kabel' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_histori', true);
        $this->forge->addForeignKey('id_opd', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->createTable('histori_index', true);

        // 11. TABEL JARINGAN
        $this->forge->addField([
            'id_jaringan'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_opd_asal'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_opd_tujuan' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_kabel'   => ['type' => 'ENUM', 'constraint' => ['FO', 'Backbone', 'Broadband'], 'default' => 'FO'],
            'keterangan'    => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id_jaringan', true);
        $this->forge->addForeignKey('id_opd_asal', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_opd_tujuan', 'opd', 'id_opd', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jaringan', true);
    }

    public function down()
    {
        $this->forge->dropTable('jaringan', true);
        $this->forge->dropTable('histori_index', true);
        $this->forge->dropTable('index_penilaian', true);
        $this->forge->dropTable('master_variabel', true);
        $this->forge->dropTable('master_dimensi', true);
        $this->forge->dropTable('opd_barang', true);
        $this->forge->dropTable('opd', true);
        $this->forge->dropTable('barang', true);
        $this->forge->dropTable('kelompok_opd', true);
        $this->forge->dropTable('role', true);
        $this->forge->dropTable('role_group', true);
    }
}