<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\ResumeFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property ?User $user
 * @property string $name
 * @property Collection $education
 * @property Collection $languages
 * @property Collection $technologies
 * @property Collection $projects
 */
class Resume extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "education" => AsCollection::class,
        "languages" => AsCollection::class,
        "technologies" => AsCollection::class,
        "projects" => AsCollection::class,
    ];
    protected $perPage = 50;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    protected static function newFactory(): ResumeFactory
    {
        return ResumeFactory::new();
    }
}
