<?php

declare(strict_types=1);

namespace Toby\Models;

use Database\Factories\BenefitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property boolean $companion
 */
class Benefit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): BenefitFactory
    {
        return BenefitFactory::new();
    }
}
