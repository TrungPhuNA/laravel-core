<?php

namespace App\Core\Support;

enum UserType: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case SYSTEM = 'SYSTEM';
}

