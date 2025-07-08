<?php

namespace App\Imports;

use Carbon\Carbon;
use Hyn\Tenancy\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Hyn\Tenancy\Queue\TenantAwareJob;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Digemid\Models\DigemidAyuda;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

/**
 * Class DigemidAyudaImport
 *
 * @package App\Imports
 */
class DigemidAyudaImport implements ToCollection, WithChunkReading, ShouldQueue
{
    use Importable;
    use Dispatchable, InteractsWithQueue, Queueable, TenantAwareJob;

    protected $data;

    public function collection(Collection $rows)
    {
        $env = app(Environment::class);

        if ($fqdn = optional($env->hostname())->fqdn) {
            config(['database.default' => 'tenant']);
        }

        // Eliminar todos los registros de la tabla antes de la importación
        DigemidAyuda::truncate();

        $total = count($rows);
        $registered = 0;
        for ($i = 0; $i < $total; $i++) {
            $row = $rows[$i];
            /*
             0 => 'Cod_Prod'
             1 => 'Nom_Prod'
             2 => 'Concent'
             3 => 'Nom_Form_Farm'
             4 => 'Presentac'
             5 => 'Fracción'
             6 => 'Num_RegSan'
             7 => 'Nom_Titular'
             8 => 'Nom_Fabricante'
             9 => 'Nom_IFA'
             10 => 'Nom_Rubro'
             11 => 'Situación'
            */
            $Cod_Prod = trim($row[0]);
            $Nom_Prod = $row[1];
            $Concent = $row[2];
            $Nom_Form_Farm = $row[3];
            $Presentac = $row[4];
            $Fraccion = $row[5];
            $Num_RegSan = $row[6];
            $Nom_Titular = $row[7];
            $Nom_Fabricante = $row[8];
            $Nom_IFA = $row[9];
            $Nom_Rubro = $row[10];
            $Situacion = $row[11];

            if (
                !empty($Cod_Prod) &&
                !empty($Nom_Prod) &&
                !empty($Concent) &&
                !empty($Nom_Form_Farm) &&
                !empty($Presentac) &&
                !empty($Fraccion) &&
                !empty($Num_RegSan) &&
                !empty($Nom_Titular) &&
                !empty($Nom_Fabricante) &&
                !empty($Situacion) &&
                $Cod_Prod !== 'Cod_Prod'
            ) {
                $digemidAyuda = DigemidAyuda::where('cod_prod', $Cod_Prod)->first();

                if (empty($digemidAyuda)) {
                    $digemidAyuda = new DigemidAyuda();
                    $digemidAyuda->cod_prod = $Cod_Prod;
                }

                $active = 1;
                if (strtolower(trim($Situacion)) !== 'act') {
                    $active = 0;
                }

                $digemidAyuda->fill([
                    'nom_prod'       => $Nom_Prod,
                    'concent'        => $Concent,
                    'nom_form_farm'  => $Nom_Form_Farm,
                    'presentac'      => $Presentac,
                    'fraccion'       => $Fraccion,
                    'num_reg_san'    => $Num_RegSan,
                    'nom_titular'    => $Nom_Titular,
                    'nom_fabricante' => $Nom_Fabricante,
                    'nom_ifa'        => $Nom_IFA,
                    'nom_rubro'      => $Nom_Rubro,
                    'situacion'      => $Situacion,
                    'active'         => $active,
                ]);

                $digemidAyuda->save();

                ++$registered;
            }
        }
        $this->data = compact('total', 'registered');
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Define el tamaño de los bloques de registros a procesar
     * 
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000; // Procesa 1000 registros por chunk
    }
}
