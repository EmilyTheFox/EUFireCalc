<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property float       $capital_gains
 * @property float       $wealth_tax
 * @property null|string $special_rules
 *
 * @method static Builder visible()
 * @method static Builder visibleWithUnlisted()
 *
 * @mixin \Eloquent
 */
class TaxSystem extends Model
{
    use HasFactory;
}
