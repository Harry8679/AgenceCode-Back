<?php

namespace App\Enum;
enum CouponStatus: string { 
    case NEW='NEW';
    case PARTIAL='PARTIAL';
    case USED='USED';
    case EXPIRED='EXPIRED';
}
