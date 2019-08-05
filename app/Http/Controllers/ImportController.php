<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ImportQueue;
use App\Imports\WorkImport;
use App\Imports\RemuneracionImport;
use App\Imports\DescuentoImport;
use App\Models\Cronograma;

class ImportController extends Controller
{

    public function __construct()
    {
        $this->middleware('validacion');
    }
    
    public function work(Request $request)
    {
        $this->validate(request(), [
            "import" => "required|file|max:1024"
        ]);
        
        try {
            // configurar archivo de excel
            $name = "work_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import'), $name);
            // Procesar importacion
            (new WorkImport)->import("/imports/{$name}", "public");
    
            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    public function remuneracion(Request $request, $slug)
    {
        $this->validate(request(), [
            "import_remuneracion" => "required|file|max:1024"
        ]);

        try {
            // recuperar id
            $id = \base64_decode($slug);
            // obtener cronograma
            $cronograma = Cronograma::findOrFail($id);
            // configurar archivo de excel
            $name = "remuneracion_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import_remuneracion'), $name);
            // Procesar importacion
            (new RemuneracionImport($cronograma, $name))->import("/imports/{$name}", "public");
    
            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    public function descuento(Request $request, $slug)
    {
        $this->validate(request(), [
            "import_descuento" => "required|file|max:1024"
        ]);

        try {
            // recuperar id
            $id = \base64_decode($slug);
            // obtener cronograma
            $cronograma = Cronograma::findOrFail($id);
            // configurar archivo de excel
            $name = "descuento_import_" . date('Y-m-d') . ".xlsx";
            $storage = Storage::disk("public")->putFileAs("/imports", $request->file('import_descuento'), $name);
            // Procesar importacion
            (new DescuentoImport($cronograma, $name))->import("/imports/{$name}", "public");
    
            return back()->with(["success" => "La importación ha sido exitosa"]);
        } catch (\Throwable $th) {
            return back()->with(["danger" => "La importación falló"]); 
        }
    }

}
