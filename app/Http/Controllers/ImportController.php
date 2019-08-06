<?php
/**
 * Http/Controllers/ImportController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ImportQueue;
use App\Imports\WorkImport;
use App\Imports\RemuneracionImport;
use App\Imports\DescuentoImport;
use App\Models\Cronograma;

/**
 * Class ImportController
 * 
 * @category Controllers
 */
class ImportController extends Controller
{

    public function __construct()
    {
        $this->middleware('validacion');
    }
    
    
    /**
     * Realizar la importacion de trabajadores desde un archivo excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }


    /**
     * Realizar la importación de las remuneraciones con respecto al cronograma, desde un archivo de excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
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
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }

    /**
     * Realizar la importación de los descuentos con respecto al cronograma, desde un archivo de excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
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
            \Log::info($th);
            return back()->with(["danger" => "La importación falló"]); 
        }
    }

}
