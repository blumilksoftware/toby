<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Toby\Models\User;

class BulkCreateAction
{
    public function __construct(
        protected CreateAction $createAction,
    ) {}

    public function execute(Collection $users, array $data, User $creator): Collection
    {
        $requests = collect();
        $errors = collect();

        /** @var User $user */
        foreach ($users as $user) {
            $data["user_id"] = $user->id;

            try {
                $request = $this->createAction->execute($data, $creator);

                $requests->push($request);
            } catch (ValidationException $e) {
                $errors->push($user->profile->full_name . " - " . $e->getMessage());
            }
        }

        return collect([
            "requests" => $requests,
            "errors" => $errors,
        ]);
    }
}
