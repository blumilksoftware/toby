<?php

declare(strict_types=1);

namespace Toby\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Toby\Helpers\YearPeriodRetriever;

class SelectedYearPeriodScope implements Scope
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {
    }

    public function apply(Builder $builder, Model $model): Builder
    {
        return $builder->where("year_period_id", $this->yearPeriodRetriever->selected()->id);
    }
}
