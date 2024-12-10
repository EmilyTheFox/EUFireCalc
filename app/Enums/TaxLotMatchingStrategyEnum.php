<?php

namespace App\Enums;

enum TaxLotMatchingStrategyEnum: string
{
    case FIFO = 'Fifo'; // First In First Out
    case LIFO = 'Lifo'; // Last In First Out
}