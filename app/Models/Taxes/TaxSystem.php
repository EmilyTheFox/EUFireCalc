<?php

namespace App\Models\Taxes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property float       $capital_gains
 * @property float       $wealth_tax
 * @property null|string $special_rules
 * @property null|int    $created_at
 * @property null|int    $updated_at
 *
 * @mixin \Eloquent
 */
class TaxSystem extends Model
{
    use HasFactory;
    protected $hidden = [
        "id",
        "created_at",
        "updated_at"
    ];
}
