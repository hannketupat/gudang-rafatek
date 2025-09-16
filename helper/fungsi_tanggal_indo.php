<?php
// fungsi untuk membuat format tanggal indonesia
function tanggal_indo($tanggal)
{
  $bulan = array(
    1 => 'Januari',
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
  );
  $pecahkan = explode('-', $tanggal);
  return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// fungsi untuk menampilkan nama hari dalam bahasa Indonesia
function hari_indo($tanggal)
{
  $hari_array = array(
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
  );
  $hari = date('l', strtotime($tanggal));
  return $hari_array[$hari];
}
