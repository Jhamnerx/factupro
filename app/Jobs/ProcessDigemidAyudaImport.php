<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Imports\DigemidAyudaImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Hyn\Tenancy\Queue\TenantAwareJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDigemidAyudaImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, TenantAwareJob;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $import = new DigemidAyudaImport();
            Excel::import($import, storage_path('app/' . $this->filePath));

            // Registrar los resultados de la importación
            Log::info('Importación DigemidAyuda completada', [
                'registros_procesados' => $import->getData()['total'] ?? 0,
                'registros_importados' => $import->getData()['registered'] ?? 0
            ]);

            // Eliminar el archivo temporal después de procesarlo
            Storage::delete($this->filePath);
        } catch (\Exception $e) {
            Log::error('Error en la importación DigemidAyuda: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Eliminar el archivo temporal en caso de error
            Storage::delete($this->filePath);

            // Relanzar la excepción para que el sistema de colas pueda manejarla
            throw $e;
        }
    }
}
