<?php

namespace MityDigital\StatamicLogger\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SplFileObject;

class StatamicLoggerReader
{
    protected SplFileObject $file;

    protected int $page = 1;

    protected int $perPage = 25;

    protected int $total = 0;

    public function getDates(): Collection
    {
        $logs = collect();

        $path = storage_path(\MityDigital\StatamicLogger\Facades\StatamicLogger::getStoragePath());
        $filename = \MityDigital\StatamicLogger\Facades\StatamicLogger::getStorageFilename();

        // get items
        $items = glob($path.DIRECTORY_SEPARATOR.$filename.'-*.log');
        foreach ($items as $log) {
            // get the base file
            $file = basename($log);

            // extract the date
            preg_match('/'.$filename."-(?<date>\d{4}-\d{2}-\d{2}).log/", $file, $matches);

            if (array_key_exists('date', $matches)) {
                $date = $matches['date'];

                // add to the collection, with a formatted label
                $logs->put($date, Carbon::parse($date)->format('d M Y'));
            }
        }

        // sort descending, and return
        return $logs->sortKeysDesc();
    }

    public function paginate(string $date, int $page, int $perPage): array|Collection
    {
        // set the page and per page values
        $this->page = $page;
        $this->perPage = $perPage;

        // try to read the file
        if (! $this->read($date)) {
            // nothing for the requested day
            return [];
        }

        // offset - how many less than the $total do we need to go to?
        $offset = $this->total - (($this->page - 1) * $this->perPage);

        if ($offset <= 0) {
            // go to start
            $offset = $this->total;
            $this->page = 1; // force page 1
        }

        // loop through the lines, pulling each one on reverse (if it exists, until the start of the file)
        $lines = collect();
        for ($i = 0; $i < $this->perPage; $i++) {
            $seek = ($offset - 1) - $i;
            // if greater than zero, or less than the total (i.e. it exists)
            if ($seek >= 0 && $seek < $this->total) {
                $this->file->seek($seek); // go to line
                $lines->add(trim($this->file->current())); // read the line
            }
        }

        return $lines;
    }

    protected function read(string $date): bool
    {
        // get the configured path and filename
        $path = \MityDigital\StatamicLogger\Facades\StatamicLogger::getStoragePath();
        $filename = \MityDigital\StatamicLogger\Facades\StatamicLogger::getStorageFilename();

        // build the path
        $fullPath = storage_path($path.DIRECTORY_SEPARATOR.$filename.'-'.$date.'.log');

        // if the file doesn't exist, return false
        if (! file_exists($fullPath)) {
            return false;
        }

        // load the file
        $this->file = new SplFileObject($fullPath);
        $this->file->seek($this->file->getSize());

        // set the total number of records
        $this->total = $this->file->key();

        // loaded and total found, return true
        return true;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
