<?php

declare(strict_types=1);

namespace Toby\States\OvertimeRequest;

use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class OvertimeRequestState extends State
{
    /**
     * @throws InvalidConfig
     */
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class)
            ->allowTransition(Created::class, Approved::class)
            ->allowTransition(Created::class, WaitingForTechnical::class)
            ->allowTransition(WaitingForTechnical::class, Rejected::class)
            ->allowTransition(WaitingForTechnical::class, AcceptedByTechnical::class)
            ->allowTransition(AcceptedByTechnical::class, Approved::class)
            ->allowTransition([
                Created::class,
                WaitingForTechnical::class,
                AcceptedByTechnical::class,
                Approved::class,
            ], Cancelled::class)
            ->allowTransition(Approved::class, Settled::class);
    }

    public function label(): string
    {
        return __(static::$name);
    }
}
