<?php

declare(strict_types=1);

namespace Toby\Eloquent\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Rackbeat\UIAvatars\HasAvatar;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Eloquent\Helpers\ColorGenerator;

/**
 * @property string $first_name
 * @property string $last_name
 * @property-read string $full_name
 * @property string $position
 * @property EmploymentForm $employment_form
 * @property Carbon $employment_date
 * @property Carbon $birthday
 * @property Carbon $last_medical_exam_date
 * @property Carbon $next_medical_exam_date
 * @property Carbon $last_ohs_training_date
 * @property Carbon $next_ohs_training_date
 */
class Profile extends Model
{
    use HasFactory;
    use HasAvatar;

    protected $primaryKey = "user_id";
    protected $guarded = [];
    protected $casts = [
        "employment_form" => EmploymentForm::class,
        "employment_date" => "date",
        "birthday" => "date",
        "last_medical_exam_date" => "date",
        "next_medical_exam_date" => "date",
        "last_ohs_training_date" => "date",
        "next_ohs_training_date" => "date",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withTrashed();
    }

    public function getAvatar(): string
    {
        return $this->getAvatarGenerator()
            ->backgroundColor(ColorGenerator::generate($this->full_name))
            ->image();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected function getAvatarName(): string
    {
        return mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1);
    }

    protected static function newFactory(): ProfileFactory
    {
        return ProfileFactory::new();
    }
}
