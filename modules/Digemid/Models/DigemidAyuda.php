<?php

namespace Modules\Digemid\Models;

use App\Models\Tenant\ModelTenant;

/**
 * Class DigemidAyuda
 *
 * @package Modules\Digemid\Models
 * @mixin ModelTenant
 */
class DigemidAyuda extends ModelTenant
{
    protected $table = 'digemid_ayuda';
    protected $fillable = [
        'cod_prod',
        'nom_prod',
        'concent',
        'nom_form_farm',
        'presentac',
        'fraccion',
        'num_reg_san',
        'nom_titular',
        'nom_fabricante',
        'nom_ifa',
        'nom_rubro',
        'situacion',
        'active'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Retorna el nombre del producto
     *
     * @return string
     */
    public function getNomProd()
    {
        return $this->nom_prod;
    }

    /**
     * Retorna el titular
     *
     * @return string
     */
    public function getNomTitular()
    {
        return $this->nom_titular;
    }

    /**
     * Retorna el fabricante
     *
     * @return string
     */
    public function getNomFabricante()
    {
        return $this->nom_fabricante;
    }
}
