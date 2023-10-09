<?php

use Carbon\Carbon;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\Support\StatamicLoggerReader;
use Statamic\Facades\Collection;

beforeEach(function () {
    $this->reader = app()->make(StatamicLoggerReader::class);
    $this->today = Carbon::now()->format('Y-m-d');

    $this->path = StatamicLogger::getStoragePath();
    $this->filename = StatamicLogger::getStorageFilename();

    $fullPath = storage_path($this->path.DIRECTORY_SEPARATOR.$this->filename.'-*.log');

    // delete all log files that are for statamic
    $files = glob($fullPath);
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            unlink($file); // delete file
        }
    }

    // log some data for "today"
    Collection::make('a')->save();
    Collection::make('b')->save();
    Collection::make('c')->save();
    Collection::make('d')->save();
    Collection::make('e')->save();
    $f = Collection::make('f');
    $f->save();

    // so far that is 12 entries (one created, one saved, per entry)

    // add one more save (13 total now)
    $f->title('f new');
    $f->save();

    // 13 entries
});

it('can paginate a log', function () {
    $day = Carbon::now()->setTime(9, 0)->subDay()->format('Y-m-d');
    $log = storage_path($this->path.DIRECTORY_SEPARATOR.$this->filename.'-'.$day.'.log');

    // if it exists, delete it
    if (file_exists($log)) {
        unlink($log);
    }

    // paginate
    $data = $this->reader->paginate($day, 1, 10);

    // should be empty
    expect($data)->toHaveCount(0);

    // write 23 lines to the file
    $fp = fopen($log, 'w');
    for ($i = 1; $i <= 23; $i++) {
        fwrite($fp, 'Line '.$i.PHP_EOL);
    }
    fclose($fp);

    // paginate page 1 with 10 per page
    $data = $this->reader->paginate($day, 1, 10);

    // confirm we have 10 returned, and 23 total
    expect($data)->toHaveCount(10)
        ->and($this->reader->getPage())->toBe(1)
        ->and($this->reader->getPerPage())->toBe(10)
        ->and($this->reader->getTotal())->toBe(23)
        // confirm we expect 23 to 14 (the LAST 10 entries)
        ->and($data)->toMatchArray([
            'Line 23',
            'Line 22',
            'Line 21',
            'Line 20',
            'Line 19',
            'Line 18',
            'Line 17',
            'Line 16',
            'Line 15',
            'Line 14',
        ]);

    // get page 2
    $data = $this->reader->paginate($day, 2, 10);

    // confirm we have 10 returned, and 23 total
    expect($data)->toHaveCount(10)
        ->and($this->reader->getPage())->toBe(2)
        ->and($this->reader->getPerPage())->toBe(10)
        ->and($this->reader->getTotal())->toBe(23)
        // confirm we expect 13 to 4 (the MIDDLE 10 entries)
        ->and($data)->toMatchArray([
            'Line 13',
            'Line 12',
            'Line 11',
            'Line 10',
            'Line 9',
            'Line 8',
            'Line 7',
            'Line 6',
            'Line 5',
            'Line 4',
        ]);

    // get page 3
    $data = $this->reader->paginate($day, 3, 10);

    // confirm we have 3 returned, and 23 total
    expect($data)->toHaveCount(3)
        ->and($this->reader->getPage())->toBe(3)
        ->and($this->reader->getPerPage())->toBe(10)
        ->and($this->reader->getTotal())->toBe(23)
        // confirm we expect 13 to 4 (the MIDDLE 10 entries)
        ->and($data)->toMatchArray([
            'Line 3',
            'Line 2',
            'Line 1',
        ]);

    // paginate 5 per page, get page 4
    $data = $this->reader->paginate($day, 4, 5);

    // confirm we have 5 returned, and 23 total
    expect($data)->toHaveCount(5)
        ->and($this->reader->getPage())->toBe(4)
        ->and($this->reader->getPerPage())->toBe(5)
        ->and($this->reader->getTotal())->toBe(23)
        // confirm we expect 13 to 4 (the MIDDLE 10 entries)
        ->and($data)->toMatchArray([
            'Line 8',
            'Line 7',
            'Line 6',
            'Line 5',
            'Line 4',
        ]);
});

it('returns the correct page after pagination', function () {
    // paginate, then getPage
    $this->reader->paginate($this->today, 2, 10);
    expect($this->reader->getPage())->toBe(2);

    $this->reader->paginate($this->today, 4, 10);
    expect($this->reader->getPage())->toBe(1); // page 1, as it exceeds the pagination

    $this->reader->paginate($this->today, 3, 2);
    expect($this->reader->getPage())->toBe(3);
});

it('returns the correct per page after pagination', function () {
    // paginate, then getPerPage
    $this->reader->paginate($this->today, 1, 10);
    expect($this->reader->getPerPage())->toBe(10);

    $this->reader->paginate($this->today, 1, 2);
    expect($this->reader->getPerPage())->toBe(2);

    $this->reader->paginate($this->today, 2, 5);
    expect($this->reader->getPerPage())->toBe(5);
});

it('returns the correct total after pagination', function () {
    // paginate, then getTotal
    $this->reader->paginate($this->today, 1, 10);
    expect($this->reader->getTotal())->toBe(13);

    // add a new one
    Collection::make('g')->save();

    // should be two more (one created, one saved)
    $this->reader->paginate($this->today, 1, 10);
    expect($this->reader->getTotal())->toBe(15);
});

it('can produce a list of statamic-logger log dates', function () {
    // beforeEach creates logs for "today"

    // prepare keys
    $keys = [
        Carbon::now()->format('Y-m-d'),
    ];

    // expect one
    expect($this->reader->getDates())
        ->toHaveCount(1)
        ->toHaveKeys($keys);

    // jump back a day, create a file
    $day = Carbon::now()->setTime(9, 0)->subDay();
    $keys[] = $day->format('Y-m-d');
    touch(storage_path($this->path.DIRECTORY_SEPARATOR.$this->filename.'-'.$day->format('Y-m-d').'.log'));

    // should have 2 log files
    expect($this->reader->getDates())
        ->toHaveCount(2)
        ->toHaveKeys($keys);

    // jump back another day, create a file
    $day->subDay();
    $keys[] = $day->format('Y-m-d');
    touch(storage_path($this->path.DIRECTORY_SEPARATOR.$this->filename.'-'.$day->format('Y-m-d').'.log'));

    // should have 3 log files
    expect($this->reader->getDates())
        ->toHaveCount(3)
        ->toHaveKeys($keys);
});
