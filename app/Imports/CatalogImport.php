<?php


namespace App\Imports;

use App\Models\Tenant\Item;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Digemid\Models\CatDigemid;


/**
 * Class CatalogImport
 *
 * @package App\Imports
 *
 */
class CatalogImport implements ToCollection
{
    use Importable;

    protected $data;
    /** @var array */
    protected $items;
    /** @var array */
    protected $updated;
    /** @var array */
    protected $news;

    /**
     * @return array
     */
    public function getUpdated(): array
    {
        if (empty($this->updated)) return [];
        return $this->updated;
    }

    /**
     * @param array $updated
     *
     * @return CatalogImport
     */
    public function setUpdated(array $updated): CatalogImport
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return array
     */
    public function getNews(): array
    {
        if (empty($this->news)) return [];
        return $this->news;
    }

    /**
     * @param array $news
     *
     * @return CatalogImport
     */
    public function setNews(array $news): CatalogImport
    {
        $this->news = $news;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return CatalogImport
     */
    public function setItems(array $items): CatalogImport
    {
        $this->items = $items;
        return $this;
    }
    public function addItem(Item $item)
    {
        $this->items[] = $item;
        return $this;
    }
    public function addUpdated(Item $item)
    {
        $this->updated[] = $item;
        return $this;
    }
    public function addNew(Item $item)
    {
        $this->news[] = $item;
        return $this;
    }


    public function collection(Collection $rows)
    {
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
  5 => 'Fracciones'
  6 => 'Num_RegSan'
  7 => 'Nom_Titular'
  8 => 'Nom_Fabricante'
  9 => 'Nom_IFA'
  10 => 'Nom_Rubro'
  11 => 'Situacion'
*/
            $Cod_Prod = trim($row[0]);
            $Nom_Prod = $row[1];
            $Concent = $row[2];
            $Nom_Form_Farm = $row[3];
            $Nom_Form_Farm_Simplif = $row[3];
            $Presentac = $row[4];
            $Fracciones = $row[5];
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
                !empty($Nom_Form_Farm_Simplif) &&
                !empty($Presentac) &&
                !empty($Fracciones) &&
                !empty($Num_RegSan) &&
                !empty($Nom_Titular) &&
                !empty($Nom_Fabricante) &&
                !empty($Situacion) &&
                $Cod_Prod !== 'Cod_Prod'
            ) {


                /*
                    $item = Item::orWhere(function (Builder $q) use ($Cod_Prod) {
                        $q->Where('internal_id', $Cod_Prod);
                        $q->WhereNotNull('internal_id');
                    })->orWhere(function (Builder $q) use ($Cod_Prod) {
                        $q->Where('cod_digemid', $Cod_Prod);
                        $q->WhereNotNull('cod_digemid');
                    })->first();
                    */
                $item = Item::FindByCodDigemid($Cod_Prod);

                if (!empty($item) && !empty($item->id)) {
                    if (empty($item->sanitary)) {
                        $item->setSanitary($Num_RegSan)->push();
                    }
                    $this->addUpdated($item);
                    $cat = CatDigemid::WhereItem($item)->first();
                    if (empty($cat)) {
                        $cat = new CatDigemid(['item_id' => $item->id, 'cod_digemid' => $item->cod_digemid]);
                    }
                    $active = 1;
                    if (strtolower(trim($Situacion)) !== 'act') {
                        $active = 0;
                    }
                    $cat->fill([
                        'nom_prod'               => $Nom_Prod,
                        'concent'                => $Concent,
                        'nom_form_farm'          => $Nom_Form_Farm,
                        'nom_form_farm_simplif'  => $Nom_Form_Farm_Simplif,
                        'presentac'              => $Presentac,
                        'fracciones'             => $Fracciones,
                        'num_reg_san'            => $Num_RegSan,
                        'nom_titular'            => $Nom_Titular,
                        'nom_fabricante'         => $Nom_Fabricante,
                        'nom_ifa'                => $Nom_IFA,
                        'nom_rubro'              => $Nom_Rubro,
                        'active'                 => $active,
                        'last_update'            => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                    $cat->updatePrices();
                    $cat->push();

                    ++$registered;
                }
            }
        }
        $this->data = compact('total', 'registered');
    }

    public function getData()
    {
        return $this->data;
    }
}
