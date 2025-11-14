<?php

namespace App\Enum;

enum AssignmentStatus: string {
    case REQUESTED = 'REQUESTED'; // demande parent sans prof (teacher = null)
    case APPLIED   = 'APPLIED';   // candidature prof
    case PROPOSED  = 'PROPOSED';  // proposition admin
    case ACCEPTED  = 'ACCEPTED';
    case DECLINED  = 'DECLINED';
    case CANCELLED = 'CANCELLED';
}
