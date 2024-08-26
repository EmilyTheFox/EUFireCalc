<?php

namespace App\Enums;

enum FrequencyEnum: string
{
    case ONE_OFF = 'One-Off';
    case MONTHLY = 'Monthly';
    case QUARTERLY = 'Quarterly';
    case YEARLY = 'Yearly';
}