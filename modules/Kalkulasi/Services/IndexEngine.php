<?php

namespace Modules\Kalkulasi\Services;

class IndexEngine
{
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public static function recalculate(int $idOpd): void
    {
        try {
            (new self())->run($idOpd);
        } catch (\Throwable $e) {
            log_message('error', '[IndexEngine] id_opd=' . $idOpd . ' | ' . $e->getMessage());
        }
    }

    private function run(int $idOpd): void
    {
        $opd = $this->db->table('opd')->where('id_opd', $idOpd)->get()->getRowArray();
        if (!$opd) return;

        if (empty($opd['jumlah_pegawai']) && empty($opd['rata_tamu']) && empty($opd['luas_ruangan'])) {
            $this->db->table('index_penilaian')->where('id_opd', $idOpd)->delete();
            return;
        }

        $kepemilikan = $this->db->table('opd_barang ob')
            ->select('ob.*, b.jenis_barang')
            ->join('barang b', 'b.id_barang = ob.id_barang')
            ->where('ob.id_opd', $idOpd)
            ->get()->getResultArray();

        if (empty($kepemilikan)) {
            $this->db->table('index_penilaian')->where('id_opd', $idOpd)->delete();
            return;
        }

        $variabels = $this->db->table('master_variabel mv')
            ->select('mv.*, md.bobot as bobot_dimensi')
            ->join('master_dimensi md', 'md.id_dimensi = mv.id_dimensi')
            ->orderBy('mv.id_dimensi')->orderBy('mv.id_variabel')
            ->get()->getResultArray();

        if (empty($variabels)) return;

        $dimensiScores = [];

        foreach ($variabels as $v) {
            $score100 = $this->computeVariabelScore($v, $opd, $kepemilikan);
            if ($score100 === null) continue;

            $idDim = $v['id_dimensi'];
            if (!isset($dimensiScores[$idDim])) {
                $dimensiScores[$idDim] = [
                    'bobot_dimensi' => (float)$v['bobot_dimensi'], // 40, 30, 30
                    'skor_bobot'    => 0.0,
                    'bobot_aktif'   => 0.0,
                ];
            }

            $dimensiScores[$idDim]['skor_bobot'] += $score100 * (float)$v['bobot'];
            $dimensiScores[$idDim]['bobot_aktif'] += (float)$v['bobot'];
        }

        if (empty($dimensiScores)) return;

        $indexFinal = 0.0;
        $totalBobotDimensi = 0.0;

        foreach ($dimensiScores as $d) {
            if ($d['bobot_aktif'] <= 0) continue;
            $skorDimensi100 = $d['skor_bobot'] / $d['bobot_aktif'];
            $indexFinal += $skorDimensi100 * $d['bobot_dimensi'];
            $totalBobotDimensi += $d['bobot_dimensi'];
        }

        if ($totalBobotDimensi <= 0) return;

        // Hasil terkunci pasti di rentang 0.00 - 100.00
        $indexFinal = round($indexFinal / $totalBobotDimensi, 2);
        $indexFinal = max(0.0, min(100.0, $indexFinal));

        $now = date('Y-m-d H:i:s');
        $existing = $this->db->table('index_penilaian')->where('id_opd', $idOpd)->get()->getRowArray();

        if ($existing) {
            $this->db->table('index_penilaian')->where('id_opd', $idOpd)
                ->update(['nilai_index' => $indexFinal, 'updated_at' => $now]);
        } else {
            $this->db->table('index_penilaian')->insert([
                'id_opd'      => $idOpd,
                'nilai_index' => $indexFinal,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $this->simpanHistori($idOpd, $indexFinal, $now);
    }

    private function computeVariabelScore(array $v, array $opd, array $kepemilikan): ?float
    {
        $subs = $this->db->table('master_sub_variabel')
            ->where('id_variabel', $v['id_variabel'])
            ->orderBy('id_sub_variabel', 'ASC')
            ->get()->getResultArray();

        if (!empty($subs)) {
            $skorWeighted = 0.0;
            $bobotAktif = 0.0;

            foreach ($subs as $sv) {
                $nilaiSkala = $this->computeSubSkala($sv, $opd, $kepemilikan);
                if ($nilaiSkala === null) continue;

                // Konversi nilai 1 - 5 ke skala 100 (Nilai * 20)
                $skor100 = $nilaiSkala * 20.0;
                $skorWeighted += $skor100 * (float)$sv['bobot'];
                $bobotAktif += (float)$sv['bobot'];
            }

            return $bobotAktif > 0 ? ($skorWeighted / $bobotAktif) : null;
        }

        $nilaiSkala = $this->computeFormulaSkala($v, $opd, $kepemilikan);
        return $nilaiSkala !== null ? ($nilaiSkala * 20.0) : null;
    }

    private function computeSubSkala(array $sv, array $opd, array $kepemilikan): ?float
    {
        $barangList = $kepemilikan;
        if (!empty($sv['jenis_barang_id'])) {
            $barangList = array_values(array_filter(
                $kepemilikan,
                fn($k) => (int)$k['id_barang'] === (int)$sv['jenis_barang_id']
            ));
            if (empty($barangList)) return null;
        }

        // 1. KONDISI FISIK (FUNGSI) -> Nilai 5, 3, 1
        if ($sv['field_source'] === 'kondisi_skor') {
            $skor = [];
            foreach ($barangList as $b) {
                $k = $b['kondisi'] ?? 'Baik';
                $skor[] = match ($k) {
                    'Baik'         => 5.0,
                    'Rusak Ringan' => 3.0,
                    'Rusak Berat'  => 1.0,
                    default        => 3.0
                };
            }
            return !empty($skor) ? (array_sum($skor) / count($skor)) : null;
        }

        // 2. UMUR PERANGKAT -> Nilai 1 s/d 5
        if ($sv['field_source'] === 'tahun') {
            $skor = [];
            $jenisName = strtolower($sv['nama_sub_variabel'] ?? '');
            $isAp = str_contains($jenisName, 'access point') || str_contains($jenisName, 'ap');

            foreach ($barangList as $b) {
                if (empty($b['tahun'])) continue;
                $umur = (int)date('Y') - (int)$b['tahun'];

                if ($isAp) {
                    $skor[] = match (true) {
                        $umur <= 1 => 5.0,
                        $umur <= 3 => 4.0,
                        $umur <= 5 => 3.0,
                        $umur <= 7 => 2.0,
                        default    => 1.0,
                    };
                } else {
                    $skor[] = match (true) {
                        $umur < 3  => 5.0,
                        $umur <= 4 => 4.0,
                        $umur <= 5 => 3.0,
                        $umur <= 6 => 2.0,
                        default    => 1.0,
                    };
                }
            }
            return !empty($skor) ? (array_sum($skor) / count($skor)) : null;
        }

        // 3. GRADE PERANGKAT -> High (5), Mid (3), Low (1)
        if ($sv['field_source'] === 'grade_perangkat') {
            $skor = [];
            foreach ($barangList as $b) {
                $grade = strtolower($b['keterangan'] ?? '');
                if (str_contains($grade, 'high')) $skor[] = 5.0;
                elseif (str_contains($grade, 'low')) $skor[] = 1.0;
                else $skor[] = 3.0; // Mid
            }
            return !empty($skor) ? (array_sum($skor) / count($skor)) : null;
        }

        return null;
    }

    private function computeFormulaSkala(array $v, array $opd, array $kepemilikan): ?float
    {
        $formula = $v['formula'] ?? '';
        $namaVar = strtolower($v['nama_variabel'] ?? '');
        if (empty($formula)) return null;

// 1. Ekstrak Total Port Switch (Tiap baris switch port-nya dijumlahkan langsung)
        $totalPortSwitch = 0;
        foreach ($kepemilikan as $k) {
            if (str_contains(strtolower($k['jenis_barang']), 'switch')) {
                $totalPortSwitch += (int)($k['jumlah_port'] ?? 0);
            }
        }

        // 2. Ekstrak Total AP (1 baris = 1 unit AP)
        $totalAp = count(array_filter($kepemilikan, fn($k) => 
            str_contains(strtolower($k['jenis_barang']), 'access point') || 
            str_contains(strtolower($k['jenis_barang']), 'ap')
        ));

        // 3. Ekstrak Total Router (1 baris = 1 unit Router)
        $totalRouter = count(array_filter($kepemilikan, fn($k) => 
            str_contains(strtolower($k['jenis_barang']), 'router')
        ));

        // 4. Ekstrak Total UPS (1 baris = 1 unit UPS)
        $totalUps = count(array_filter($kepemilikan, fn($k) => 
            str_contains(strtolower($k['jenis_barang']), 'ups')
        ));

        $pegawai = (float)($opd['jumlah_pegawai'] ?? 0);
        $tamu    = (float)($opd['rata_tamu'] ?? 0);
        $ruangan = (float)($opd['jumlah_ruangan'] ?? 1);
        $luas    = (float)($opd['luas_ruangan'] ?? 0);

        if (str_contains($namaVar, 'pengguna ap')) {
            if ($totalAp <= 0) return 1.0;
            $rasio = ($pegawai * 2 + $tamu) / $totalAp;
            return match (true) {
                $rasio < 20  => 5.0,
                $rasio <= 30 => 4.0,
                $rasio <= 40 => 3.0,
                $rasio <= 50 => 2.0,
                default      => 1.0,
            };
        }

        if (str_contains($namaVar, 'pengguna switch')) {
            if ($pegawai <= 0) return 5.0;
            $rasio = $totalPortSwitch / $pegawai;
            return match (true) {
                $rasio > 1.0   => 5.0,
                $rasio >= 0.75 => 4.0,
                $rasio >= 0.50 => 3.0,
                $rasio >= 0.25 => 2.0,
                default        => 1.0,
            };
        }

        if (str_contains($namaVar, 'setiap ruangan')) {
            if ($ruangan <= 0) return 1.0;
            $rasio = $totalAp / $ruangan;
            return match (true) {
                $rasio > 1.0   => 5.0,
                $rasio >= 0.75 => 4.0,
                $rasio >= 0.50 => 3.0,
                $rasio >= 0.25 => 2.0,
                default        => 1.0,
            };
        }

        if (str_contains($namaVar, 'luas ruangan')) {
            if ($totalAp <= 0) return 1.0;
            $rasio = $luas / $totalAp;
            return match (true) {
                $rasio <= 50  => 5.0,
                $rasio <= 70  => 4.0,
                $rasio <= 90  => 3.0,
                $rasio <= 150 => 2.0,
                default       => 1.0,
            };
        }

        if (str_contains($namaVar, 'router')) {
            $rasio = $totalRouter / 1.0;
            return match (true) {
                $rasio > 1.0   => 5.0,
                $rasio >= 0.75 => 4.0,
                $rasio >= 0.50 => 3.0,
                $rasio >= 0.25 => 2.0,
                default        => 1.0,
            };
        }

        if (str_contains($namaVar, 'wallmount') && !str_contains($namaVar, 'ups')) {
            $switchLuar = 1.0;
            return match ((int)$switchLuar) {
                0       => 5.0,
                1       => 4.0,
                2       => 3.0,
                3       => 2.0,
                default => 1.0,
            };
        }

        if (str_contains($namaVar, 'ups')) {
            if ($totalUps <= 0) return 1.0;
            $rasio = 1.0 / $totalUps;
            return match (true) {
                $rasio > 1.0   => 5.0,
                $rasio >= 0.75 => 4.0,
                $rasio >= 0.50 => 3.0,
                $rasio >= 0.25 => 2.0,
                default        => 1.0,
            };
        }

        return 3.0;
    }

    private function simpanHistori(int $idOpd, float $nilaiIndex, string $now): void
    {
        $this->db->table('histori_index')->insert([
            'id_opd'      => $idOpd,
            'nilai_index' => $nilaiIndex,
            'created_at'  => $now,
        ]);

        $kept = $this->db->table('histori_index')
            ->where('id_opd', $idOpd)
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()->getResultArray();

        if (!empty($kept)) {
            $keptIds = array_column($kept, 'id_histori');
            $this->db->table('histori_index')
                ->where('id_opd', $idOpd)
                ->whereNotIn('id_histori', $keptIds)
                ->delete();
        }
    }
}