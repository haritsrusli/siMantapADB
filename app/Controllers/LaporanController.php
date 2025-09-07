<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\User;

class LaporanController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->findAll();

        // Set default date range to current date
        $data['start_date'] = date('Y-m-d');
        $data['end_date'] = date('Y-m-d');

        return view('admin/rekap_harian_absensi', $data);
    }

    public function rekapHarian()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $userModel = new User();
        $absensiModel = new Absensi();

        // Get filter parameters
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $id_kelas = $this->request->getGet('id_kelas');

        // Set default values if not provided
        if (empty($start_date)) {
            $start_date = date('Y-m-d');
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d');
        }

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['id_kelas'] = $id_kelas;
        $data['kelas'] = $kelasModel->findAll();

        // Initialize rekap data
        $data['rekap'] = [];

        if (!empty($id_kelas)) {
            // Get students in the selected class
            $students = $userModel->where('role', 'siswa')
                                  ->where('id_kelas', $id_kelas)
                                  ->findAll();

            // Get all attendance records for the date range and class
            $attendanceRecords = $absensiModel->select('user_id, DATE(waktu_presensi) as tanggal')
                                              ->where('DATE(waktu_presensi) >=', $start_date)
                                              ->where('DATE(waktu_presensi) <=', $end_date)
                                              ->whereIn('user_id', array_column($students, 'id'))
                                              ->findAll();

            // Group attendance by user and date
            $attendanceMap = [];
            foreach ($attendanceRecords as $record) {
                $userId = $record['user_id'];
                $date = $record['tanggal'];
                if (!isset($attendanceMap[$userId])) {
                    $attendanceMap[$userId] = [];
                }
                $attendanceMap[$userId][$date] = true;
            }

            // Get national holidays for the date range
            $liburNasionalModel = new \App\Models\LiburNasional();
            $liburNasional = $liburNasionalModel->getLiburInRange($start_date, $end_date);
            $liburDates = array_column($liburNasional, 'tanggal');

            // Calculate working days in the date range (Monday-Friday excluding holidays)
            $workingDays = 0;
            $currentDate = new \DateTime($start_date);
            $endDateObj = new \DateTime($end_date);
            $endDateObj->modify('+1 day'); // Include end date

            $dateRange = [];
            while ($currentDate < $endDateObj) {
                $dayOfWeek = $currentDate->format('N'); // 1 (Monday) to 7 (Sunday)
                $dateStr = $currentDate->format('Y-m-d');
                
                // Check if it's a weekday (Monday-Friday) and not a holiday
                if ($dayOfWeek >= 1 && $dayOfWeek <= 5 && !in_array($dateStr, $liburDates)) {
                    $workingDays++;
                    $dateRange[] = $dateStr;
                }
                
                $currentDate->modify('+1 day');
            }

            // Process each student
            foreach ($students as $student) {
                $userId = $student['id'];
                $attendance = isset($attendanceMap[$userId]) ? $attendanceMap[$userId] : [];

                // Count attendance for the date range
                $hadir = 0;
                $detailKehadiran = [];
                
                foreach ($dateRange as $date) {
                    $status = isset($attendance[$date]) ? 'Hadir' : 'Tidak Hadir';
                    if ($status === 'Hadir') {
                        $hadir++;
                    }
                    $detailKehadiran[$date] = $status;
                }
                
                $tidakHadir = $workingDays - $hadir;
                $persentase = $workingDays > 0 ? round(($hadir / $workingDays) * 100, 2) : 0;

                $data['rekap'][] = [
                    'user_id' => $userId,
                    'nis' => $student['username'],
                    'nama' => $student['nama_lengkap'],
                    'hadir' => $hadir,
                    'tidak_hadir' => max(0, $tidakHadir), // Ensure it's not negative
                    'persentase' => $persentase,
                    'total_hari_kerja' => $workingDays,
                    'detail_kehadiran' => $detailKehadiran
                ];
            }
        }

        return view('admin/rekap_harian_absensi_detail', $data);
    }
}