<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeerRequest;
use App\Jobs\ExportJob;
use App\Jobs\SendExportMailJob;
use App\Jobs\StoreExportDataJob;
use App\Models\Meal;
use App\Services\PunkapiService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BeerController extends Controller
{
    public function index(BeerRequest $request, PunkapiService $service)
    {
        $filters = $request->validated();
        $beers = $service->getBeers(... $filters);
        $meals = Meal::all();

        return Inertia::render('Beers', [
            'beers' => $beers,
            'meals' => $meals,
            'filters' => $filters
        ]);
    }

    public function export(BeerRequest $request, PunkapiService $service)
    {
        $filename = 'cervejas-encontradas-' . now()->format('Y-m-d - H_i') . '.xlsx';

        ExportJob::withChain([
            (new SendExportMailJob($filename))->delay(5),
            (new StoreExportDataJob(Auth::user(), $filename))->delay(10)
        ])->dispatch($request->validated(), $filename);

        return redirect()->back()
            ->with('success', 'Seu arquivo foi enviado para processamento e em breve estará em seu e-mail');
    }
}
