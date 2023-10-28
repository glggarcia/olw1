<?php

namespace App\Http\Controllers;

use App\Models\Export;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exports = Export::paginate(15);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Export $export)
    {
        Storage::delete($export->filename);
        $export->delete();

        return 'Deletado';
    }
}
