<?php

namespace MityDigital\StatamicLogger\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;
use MityDigital\StatamicLogger\Support\Entry;
use Statamic\Auth\Eloquent\User as EloquentUser;
use Statamic\Auth\File\User as FileUser;
use Statamic\Facades\User;

class LogResource extends JsonResource
{
    protected static bool $includeRawMessage = false;

    protected string $pattern = "/^\[(?<datetime>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?<env>\w+)\.(?<level>\w+): (?<message>.*)/m";

    protected function parseLog(): ?array
    {
        // parse the line based on the monolog regex pattern
        preg_match($this->pattern, $this->resource, $matches);

        foreach ($matches as $key => $value) {
            if (is_int($key)) {
                unset($matches[$key]);
            }
        }

        return $matches;
    }

    public function toArray(Request $request): array
    {
        // parse the log
        $matches = $this->parseLog($this->resource);

        // decode the message
        $message = json_decode($matches['message']);

        // prepare the details for the payload
        $handler = null;
        $view = null;
        $type = null;
        $viewData = [];

        $debug = '';

        $user = [];
        if (! $message) {
            // there was an issue decoding the json message - no user or actual message is known
            $view = 'statamic-logger::error';
            $viewData = [
                'error' => __('statamic-logger::errors.invalid_json'),
                'json' => $matches['message'],
            ];
        } else {
            //
            // get the handler
            //
            $handler = app()->make($message->handler);

            //
            // set the type
            //
            $type = $handler->type();

            //
            // convert to a statamic logger entry
            //
            $entry = new Entry();
            foreach ($message->data as $key => $value) {
                $entry->$key = $value;
            }

            //
            // render the message
            //
            if (View::exists($handler->view())) {
                $view = $handler->view();

                // add the event to the handler (just makes it easier)
                $handler->setActionEvent($message->event);

                $viewData = [
                    'data' => $entry,
                    'event' => $message->event,
                    'handler' => $handler,
                ];
            } else {
                // no view
                $view = 'statamic-logger::error';
                $viewData = [
                    'error' => __('statamic-logger::errors.view_not_found', [
                        ':view' => $handler->view(),
                        ':handler' => get_class($handler),
                    ]),
                    'json' => $matches['message'],
                ];
            }

            if ($message->user) {
                //
                // prepare the user
                //
                $user = [
                    'id' => $message->user?->id,
                    'name' => $message->user->name ?? null,
                    'initials' => $message->user->name ? collect(explode(' ', $message->user->name))->map(fn (
                        string $name
                    ) => mb_substr($name, 0, 1))->join('') : null,
                    'avatar' => $this->getUserAvatar($message->user),
                ];

            }

            //
            // raw message
            //
            if (LogResource::$includeRawMessage) {
                $debug = view('statamic-logger::raw', ['data' => $entry])->render();
            }
        }

        //
        // render the view
        //
        try {
            $render = view($view, $viewData)->render();
        } catch (ViewException $e) {
            // error with the view render itself

            $render = view('statamic-logger::error', [
                'error' => __('statamic-logger::errors.render').(isset($handler) ? ' ('.get_class($handler).')' : null),
                'json' => $matches['message'],
                'message' => config('app.debug') ? $e->getMessage() : null,
            ])->render();
        }

        return [
            'date' => $matches['datetime'],
            'user' => $user,
            'type' => $type,
            'detail' => $render.$debug,
        ];
    }

    protected function getUserAvatar($user): ?string
    {
        // load avatar if we are a Statamic user
        if (property_exists($user, 'model')) {
            $userClass = $user->model;
            if ($userClass === FileUser::class || EloquentUser::class) {
                $u = User::find($user->id);

                // if we found the user
                if ($u) {
                    return $u->avatar(); // return the avatar
                }
            }
        }

        return null; // this far, we fail
    }

    public static function includeRawMessage(bool $include)
    {
        LogResource::$includeRawMessage = $include;
    }
}
