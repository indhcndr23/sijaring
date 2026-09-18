<?php

namespace Modules\KalenderKegiatan\Controllers;

use Modules\KalenderKegiatan\Controllers\BaseController as Controller;
use Modules\KalenderKegiatan\Models\Kegiatan;
use Modules\KalenderKegiatan\Models\User;

class KalenderKegiatanController extends Controller
{
    protected string $feature = 'Kalender Kegiatan & Progress Tracking';

    public function index()
    {
        $user = User::where('email', $this->auth->user()['email'])->first();

        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');

        $data = [
            'year' => $year,
            'month' => $month,
            'weeks' => $this->getWeeksInRange($month, $year),
            'penanggungJawab' => User::where('opd_id', $user->opd_id)->get()->toArray(),
            'email' => $user->email,
            'months' => [
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ],
            'dataKalender' =>  ''
        ];

        return $this->render("index", $data);
    }

    public function getData()
    {
        $skip = $this->request->getGet('skip');
        $take = $this->request->getGet('take');
        $orderBy = $this->request->getGet('orderBy');
        $orderDir = $this->request->getGet('orderDir') ?? 'asc';

        // filter
        $search = $this->request->getGet('search');
        $kalender = $this->request->getGet('kalender');
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month');
        $penanggungJawab = $this->request->getGet('penanggungJawab');

        $query = Kegiatan::with('user');

        // filter 
        if ($search) {
            $query->where('nama_kegiatan', 'like', "%{$search}%")
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('nama_lengkap', 'like', "%{$search}%");
                });
        }

        if ($kalender === 'year-calender') {
            $query->whereYear('tgl_mulai', $year);
        } elseif ($kalender === 'month-calender') {
            $month = $month ?? date('m');
            $query->whereYear('tgl_mulai', $year)
                ->whereMonth('tgl_mulai', $month);
        }

        if ($penanggungJawab) {
            $query->where('user_id', $penanggungJawab);
        }

        if ($orderBy === 'penanggung_jawab') {
            $query->orderBy(
                User::select('nama_lengkap')
                    ->whereColumn('user.id', 'kegiatan.user_id'),
                $orderDir
            );
        } elseif ($orderBy === 'nama_kegiatan') {
            $query->orderBy('nama_kegiatan', $orderDir);
        } elseif ($orderBy === 'id') {
            $query->orderBy('id', $orderDir);
        }
        if ($skip !== null) {
            $query->skip($skip);
        }

        if ($take !== null) {
            $query->take($take);
        }

        $kegiatan = $query->get();

        $data = [];
        foreach ($kegiatan as $value) {
            $data[] = [
                'penanggung_jawab' => $value->user->nama_lengkap,
                'nama_kegiatan' => $value['nama_kegiatan'],
                'bulan' => $this->getWeeksInRange($value['tgl_mulai'], $value['tgl_selesai'])
            ];
        }

        return $this->response->setJSON([
            'draw' => (int) $this->request->getGet('draw'),
            'recordsTotal' => Kegiatan::count(),
            'recordsFiltered' => Kegiatan::count(),
            'data' => $data
        ]);
    }

    public function submit()
    {
        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Aww!'
        ]);
    }

    private function getWeeksInRange($start, $end)
    {
        $result = [];

        $current = strtotime($start);
        $endDate = strtotime($end);

        while ($current <= $endDate) {
            $month = date('m', $current); // contoh: 03
            $week  = ceil(date('d', $current) / 7); // ⬅️ DI SINI PAKAINYA

            $result[$month]['m' . $week] = true;

            $current = strtotime('+1 day', $current);
        }

        return $result;
    }
}
