<?php

namespace App\Enums;

enum IncreaseFrequencyEnum: string
{
    case ONEOFF = 'Never';
    case MONTHLY = 'Monthly';
    case QUARTERLY = 'Quarterly';
    case YEARLY = 'Yearly';
    case MATCH_INFLATION = 'Match Inflation';
}