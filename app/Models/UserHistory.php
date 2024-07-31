<?php

declare(strict_types=1);

namespace Toby\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Toby\Enums\EmploymentForm;
use Toby\Enums\UserHistoryType;

/**
 * @property int $id
 * @property int $user_id
 * @property string $comment
 * @property Carbon $from
 * @property ?Carbon $to
 * @property UserHistoryType $type
 * @property EmploymentForm $employment_form
 * @property bool $is_employed_at_current_company
 * @property User $user
 */
class UserHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        "from" => "date",
        "to" => "date",
        "type" => UserHistoryType::class,
        "employment_form" => EmploymentForm::class,
        "is_employed_at_current_company" => "boolean",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
