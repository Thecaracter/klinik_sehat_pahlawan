<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereNot('role', 'admin')->count();
        $obatCount = Obat::count();
        $pasienCount = Pasien::count();
        $kunjunganFinishCount = Kunjungan::where('status', 'selesai')->count();

        $period = $request->input('period', 'yearly');
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $chartData = $this->getCompletedVisits($period, $year, $month);
        $earningsData = $this->getUserEarnings($period, $year, $month);

        return view('pages.dashboard', compact('users', 'obatCount', 'pasienCount', 'kunjunganFinishCount', 'chartData', 'earningsData', 'period', 'year', 'month'));
    }

    private function getCompletedVisits($period, $year, $month)
    {
        $query = Kunjungan::where('status', 'selesai');

        if ($period === 'yearly') {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = $startDate->copy()->endOfYear();
            $groupBy = '%Y-%m';
            $format = 'M';
        } else { // monthly
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
            $groupBy = '%Y-%m-%d';
            $format = 'd';
        }

        $visits = $query->whereBetween('tanggal', [$startDate, $endDate])
            ->select(DB::raw("DATE_FORMAT(tanggal, '$groupBy') as date"), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format($period === 'yearly' ? 'Y-m' : 'Y-m-d');
            $formattedDate = $currentDate->format($format);

            $total = $visits->get($dateKey, ['total' => 0])['total'];

            $chartData[] = [
                'date' => $formattedDate,
                'total' => $total
            ];

            if ($period === 'yearly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addDay();
            }
        }

        return $chartData;
    }

    private function getUserEarnings($period, $year, $month)
    {
        if ($period === 'yearly') {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = $startDate->copy()->endOfYear();
            $dateColumn = DB::raw("DATE_FORMAT(kunjungan.tanggal, '%Y-%m') as date");
        } else { // monthly
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
            $dateColumn = DB::raw("DATE(kunjungan.tanggal) as date");
        }

        $earnings = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                $dateColumn,
                DB::raw('SUM(pembayaran.total_bayar) as total_earnings')
            )
            ->join('kunjungan', 'users.id', '=', 'kunjungan.user_id')
            ->join('pembayaran', 'kunjungan.id', '=', 'pembayaran.kunjungan_id')
            ->whereBetween('kunjungan.tanggal', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'date')
            ->orderBy('users.name')
            ->orderBy('date')
            ->get();

        $earningsData = [];
        foreach ($earnings as $earning) {
            if (!isset($earningsData[$earning->id])) {
                $earningsData[$earning->id] = [
                    'name' => $earning->name,
                    'earnings' => []
                ];
            }
            $earningsData[$earning->id]['earnings'][] = [
                'date' => $earning->date,
                'total' => $earning->total_earnings
            ];
        }

        return $earningsData;
    }
}
