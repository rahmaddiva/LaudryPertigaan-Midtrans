<?php

namespace Config;

use Midtrans\Config as MidtransConfig;

class Midtrans
{
    public static function init()
    {
        MidtransConfig::$serverKey = 'SB-Mid-server-xFcnK43bSot2QXKfv-RkBB3V'; // Ganti dengan server key dari akun Midtrans kamu
        MidtransConfig::$isProduction = false; // true untuk production
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = true;
    }
}
