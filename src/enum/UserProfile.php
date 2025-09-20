<?php

namespace App\Enum;

enum UserProfile: string
{
    case PARENT  = 'PARENT';
    case STUDENT = 'STUDENT';
    case TEACHER = 'TEACHER';
}