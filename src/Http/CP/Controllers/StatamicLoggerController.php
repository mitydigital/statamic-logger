<?php

namespace MityDigital\StatamicLogger\Http\CP\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use MityDigital\StatamicLogger\Facades\StatamicLogger;
use MityDigital\StatamicLogger\Http\Resources\LogResource;
use MityDigital\StatamicLogger\Support\StatamicLoggerReader;
use Statamic\Http\Controllers\Controller;

class StatamicLoggerController extends Controller
{
    public function show(Request $request, StatamicLoggerReader $reader)
    {
        if (! $request->expectsJson()) {
            // return the html view
            return view('statamic-logger::show', [
                'dates' => $reader->getDates(),
            ]);
        }

        // return json
        return new LengthAwarePaginator(
            LogResource::collection($reader->paginate(
                $request->get('date', null),
                max(1, $request->get('page', 1)),
                $request->get('perPage', config('statamic.cp.pagination_size'))
            )),
            $reader->getTotal(),
            $reader->getPerPage(),
            $reader->getPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    public function download(string $date, Request $request)
    {
        // does the log file exist?
        $filename = StatamicLogger::getStorageFilename().'-'.$date.'.log';
        $path = storage_path(StatamicLogger::getStoragePath().DIRECTORY_SEPARATOR.$filename);

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}
