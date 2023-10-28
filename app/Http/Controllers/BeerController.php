<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeerRequest;
use App\Jobs\ExportJob;
use App\Jobs\SendExportMailJob;
use App\Jobs\StoreExportDataJob;
use App\Services\PunkapiService;
use Illuminate\Support\Facades\Auth;

class BeerController extends Controller
{
    public function index(BeerRequest $request, PunkapiService $service)
    {
        return $service->getBeers(... $request->validated());
    }

    public function export(BeerRequest $request, PunkapiService $service)
    {
        $filename = 'cervejas-encontradas-' . now()->format('Y-m-d - H_i') . '.xlsx';

        ExportJob::withChain([
            (new SendExportMailJob($filename))->delay(5),
            (new StoreExportDataJob(Auth::user(), $filename))->delay(10)
        ])->dispatch($request->validated(), $filename);

        return "Relatório ${filename} criado";
    }
}
