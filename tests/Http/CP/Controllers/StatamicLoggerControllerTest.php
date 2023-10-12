<?php

use Carbon\Carbon;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use Statamic\Facades\Collection;
use Statamic\Facades\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::make()
        ->makeSuper()
        ->set('name', 'Peter Parker')
        ->email('peter.parker@spiderman.com')
        ->set('password', 'secret')
        ->save();

    actingAs($this->user);
});

it('shows the correct view', function () {
    $this->get(route('statamic.cp.utilities.statamic-logger.show'))
        ->assertOk()
        ->assertViewIs('statamic-logger::show');
});

it('returns paginated json when requested', function () {
    $response = $this->get(route('statamic.cp.utilities.statamic-logger.show', [
        'date' => '2023-10-09',
    ]), [
        'Accept' => 'application/json',
    ])
        ->assertOk()
        ->assertHeader('Content-Type', 'application/json');

    expect($response->json())->toHaveKeys(['current_page', 'data', 'from', 'path', 'per_page', 'to', 'total']);
});

it('requires the download request to be in the correct format', function () {
    // test regex - must be yyyy-mm-dd
    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => 'abcd',
    ]))
        ->assertStatus(404);

    // fail all of these
    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => '23-10-09',
    ]))
        ->assertStatus(404);

    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => '20231009',
    ]))
        ->assertStatus(404);

    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => '2023-10-9',
    ]))
        ->assertStatus(404);

    // success
    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => Carbon::now()->format('Y-m-d'),
    ]))
        ->assertStatus(200);
});

it('can download a log file', function () {
    $today = Carbon::now()->format('Y-m-d');

    $path = StatamicLogger::getStoragePath();
    $filename = StatamicLogger::getStorageFilename();

    $fullPath = storage_path($path.DIRECTORY_SEPARATOR.$filename.'-'.$today.'.log');

    // force a log entry for today
    Collection::make('a')->save();

    // there should definitely be a log now
    expect(file_exists($fullPath))->toBeTrue();

    // test download
    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => $today,
    ]))
        ->assertDownload($filename.'-'.$today.'.log');
});

it('returns a 404 if log does not exist', function () {
    // test 404 if file not exists
    $today = Carbon::now()->format('Y-m-d');

    $path = StatamicLogger::getStoragePath();
    $filename = StatamicLogger::getStorageFilename();

    $fullPath = storage_path($path.DIRECTORY_SEPARATOR.$filename.'-'.$today.'.log');

    // delete the log
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }

    // there should not be a log file
    expect(file_exists($fullPath))->toBeFalse();

    // test download
    $this->get(route('statamic.cp.utilities.statamic-logger.download', [
        'date' => $today,
    ]))
        ->assertStatus(404);
});
