<?php

namespace App\Model\musica;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class ColaboradorMusica extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'colaboradores_musica';
    protected $softDelete = true;
    protected $with = ['user'];

    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function servicos(){
        return $this->belongsToMany('App\Model\musica\ServicoMusica'
            ,'colaborador_servicos_musica','colaborador_musica_id','servico_musica_id');
    }

    /**
     * Escopo consultar apenas os líderes da equipe de música
     * @param  Consulta
     * @return Líderes
     */
    public function scopeLideres($query, $order = 'asc'){
        return $query->where('lider', '=', true );
    }

    public function scopeToken($query,$token){
        $query->where('token','=', $token);
    }
}
