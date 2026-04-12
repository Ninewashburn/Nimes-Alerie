<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING   = 'pending';
    case PREPARING = 'preparing';
    case SHIPPED   = 'shipped';
    case REFUNDED  = 'refunded';
}
