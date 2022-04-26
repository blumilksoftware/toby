<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack;

use Exception;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Controller as SlackController;
use Spatie\SlashCommand\Exceptions\InvalidRequest;
use Spatie\SlashCommand\Exceptions\RequestCouldNotBeHandled;
use Spatie\SlashCommand\Exceptions\SlackSlashCommandException;
use Spatie\SlashCommand\Response;

class Controller extends SlackController
{
    /**
     * @throws InvalidRequest|RequestCouldNotBeHandled
     */
    public function getResponse(IlluminateRequest $request): IlluminateResponse
    {
        $this->verifyWithSigning($request);

        $handler = $this->determineHandler();

        try {
            $response = $handler->handle($this->request);
        } catch (SlackSlashCommandException $exception) {
            $response = $exception->getResponse($this->request);
        } catch (ValidationException $exception) {
            $response = $this->prepareValidationResponse($exception);
        } catch (Exception $exception) {
            $response = $this->convertToResponse($exception);
        }

        return $response->getIlluminateResponse();
    }

    protected function prepareValidationResponse(ValidationException $exception): Response
    {
        $errors = (new Collection($exception->errors()))
            ->map(
                fn(array $message): Attachment => Attachment::create()
                    ->setColor("danger")
                    ->setText($message[0]),
            );

        return Response::create($this->request)
            ->withText(":x: Polecenie `/{$this->request->command} {$this->request->text}` jest niepoprawna:")
            ->withAttachments($errors->all());
    }
}
