<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\Support\StatamicLoggerReader;

it('stores a log entry with line breaks in a single line', function () {

    // set today
    $today = Carbon::now()->format('Y-m-d');

    // remove the log for today
    unlink(storage_path(StatamicLogger::getStoragePath().DIRECTORY_SEPARATOR.StatamicLogger::getStorageFilename().'-'.$today.'.log'));

    // log a simple string
    Log::channel('statamic-logger')
        ->info(
            json_encode([
                'lines' => 'no lines here',
            ])
        );

    // log a string with line breaks
    Log::channel('statamic-logger')
        ->info(
            json_encode([
                'lines' => "a string with\nline breaks",
            ])
        );

    // read the last line of the log
    $reader = app(StatamicLoggerReader::class);
    $data = $reader->paginate($today, 1, 10);

    expect($data)
        ->toHaveCount(2)
        ->and($data[0])->toContain('{"lines":"a string with\nline breaks"}')
        ->and($data[1])->toContain('{"lines":"no lines here"}');
});
