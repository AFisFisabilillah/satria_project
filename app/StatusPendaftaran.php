<?php

namespace App;

enum StatusPendaftaran: string
{
    case Submitted = 'submitted'; // Lamaran didaftarkan
    case ReviewedByHR = 'reviewed_by_hr'; // Diterima HRD
    case Interview = 'interview'; // Wawancara
    case Accepted = 'accepted'; // Diterima
    case Rejected = 'rejected'; // Ditolak

    public function label(): string
    { 
        return match ($this) {
            self::Submitted => 'Lamaran dikirim',
            self::ReviewedByHR => 'Diperiksa oleh HRD',
            self::Interview => 'Menunggu Wawancara',
            self::Accepted => 'Diterima',
            self::Rejected => 'Ditolak',
        };
    }
}
