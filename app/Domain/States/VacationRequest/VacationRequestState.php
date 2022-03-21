<?php

declare(strict_types=1);

namespace Toby\Domain\States\VacationRequest;

use Spatie\ModelStates\Exceptions\InvalidConfig;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class VacationRequestState extends State
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
            ->allowTransition(Created::class, WaitingForAdministrative::class)
            ->allowTransition(WaitingForTechnical::class, Rejected::class)
            ->allowTransition(WaitingForTechnical::class, AcceptedByTechnical::class)
            ->allowTransition(WaitingForAdministrative::class, Rejected::class)
            ->allowTransition(WaitingForAdministrative::class, AcceptedByAdministrative::class)
            ->allowTransition(AcceptedByTechnical::class, WaitingForAdministrative::class)
            ->allowTransition(AcceptedByTechnical::class, Approved::class)
            ->allowTransition(AcceptedByAdministrative::class, Approved::class)
            ->allowTransition([
                Created::class,
                WaitingForTechnical::class,
                WaitingForAdministrative::class,
                AcceptedByTechnical::class,
                AcceptedByAdministrative::class,
                Approved::class,
            ], Cancelled::class);
    }

    public function label(): string
    {
        return __(static::$name);
    }
}
