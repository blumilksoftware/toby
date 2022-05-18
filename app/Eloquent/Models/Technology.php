<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class Technology extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): TechnologyFactory
    {
        return TechnologyFactory::new();
    }
}
