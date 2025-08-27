<?php

namespace MityDigital\StatamicLogger\Http\CP\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
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
            return view('statamic-logger::show', [
                'dates' => $reader->getDates(),
            ]);
        }

        if ($request->get('raw', false) === true || $request->get('raw', false) === 'true') {
            LogResource::includeRawMessage(true);
        }

        $collection = $reader->paginate(
            $request->get('date', null),
            max(1, $request->get('page', 1)),
            $request->get('perPage', config('statamic.cp.pagination_size'))
        );

        $totalItems = $reader->getTotal();
        $perPage = $reader->getPerPage();
        $page = $reader->getPage();

        return (new ResourceCollection(
            LogResource::collection($collection)
        ))->additional([
            'meta' => [
                'current_page' => $page,
                'from' => $totalItems > 0 ? ($page - 1) * $perPage + 1 : null,
                'last_page' => $totalItems > 0 ? max((int) ceil($totalItems / $perPage), 1) : null,
                'path' => Paginator::resolveCurrentPath(),
                'per_page' => $perPage,
                'to' => $totalItems > 0 ? $page * $perPage : null,
                'total' => $totalItems,
                'columns' => [
                    ['field' => 'date', 'label' => __('statamic-logger::utility.columns.date')],
                    ['field' => 'user', 'label' => __('statamic-logger::utility.columns.user')],
                    ['field' => 'type', 'label' => __('statamic-logger::utility.columns.type')],
                    ['field' => 'detail', 'label' => __('statamic-logger::utility.columns.detail')],
                ],
            ],
        ]);
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
