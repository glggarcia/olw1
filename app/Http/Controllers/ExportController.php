<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exports = Export::paginate(15);

        return Inertia::render('Reports', [
            'exports' => $exports
        ]);
    }

    public function show($export)
    {
        $export = Export::find($export);
        return Storage::download($export->file_name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($export)
    {
        $export = Export::find($export);
        if ($export) {
            Storage::disk('s3')->delete($export->file_name);
            $export->delete();

            return redirect()->back()
                ->with('success', 'Seu arquivo foi removido com sucesso');
        }

        return redirect()->back()
            ->with('success', 'Seu arquivo não foi removido. Id não encontrado');
    }
}
