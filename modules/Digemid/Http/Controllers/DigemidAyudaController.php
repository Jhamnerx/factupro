<?php

namespace Modules\Digemid\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\DigemidAyudaImport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Digemid\Models\DigemidAyuda;

class DigemidAyudaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('digemid::ayuda.index');
    }

    /**
     * Muestra la página de importación
     *
     * @return Response
     */
    public function import()
    {
        return view('digemid::ayuda.import');
    }

    /**
     * Procesa el archivo de importación
     *
     * @param Request $request
     * @return Response
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            $import = new DigemidAyudaImport();
            $import->import($request->file('file'));

            $data = $import->getData();

            return response()->json([
                'success' => true,
                'message' => 'Importación realizada con éxito',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista los registros de digemid_ayuda
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 10);

        $digemidAyuda = DigemidAyuda::where(function ($query) use ($search) {
            if (!empty($search)) {
                $query->where('cod_prod', 'like', "%{$search}%")
                    ->orWhere('nom_prod', 'like', "%{$search}%")
                    ->orWhere('nom_titular', 'like', "%{$search}%")
                    ->orWhere('nom_fabricante', 'like', "%{$search}%");
            }
        })->orderBy('id', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $digemidAyuda
        ]);
    }

    /**
     * Busca registros por número de registro sanitario
     *
     * @param string $regSan
     * @return Response
     */
    public function searchByRegSan($regSan)
    {
        try {
            // Buscamos coincidencias exactas o parciales del registro sanitario
            $records = DigemidAyuda::where('num_reg_san', 'like', "%{$regSan}%")
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $records,
                'total' => count($records)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar registros: ' . $e->getMessage()
            ], 500);
        }
    }
}
