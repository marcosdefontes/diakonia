<?php

namespace App\Http\Controllers\musica;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Model\evento\Evento;
use App\Model\musica\ColaboradorMusica;
use App\Model\musica\EscalaMusica;
use App\Model\musica\ServicoMusica;
use App\Model\musica\Tarefa;
use App\Services\Validation\EscalaMusicaValidator;
use Bouncer;
use Auth;
use App\Events\musica\EscalaPublicada;
use App\Events\musica\TarefaEscalaAdicionada;
use App\Events\musica\TarefaEscalaRemovida;
use App\Events\musica\EscalaLiderTrocado;
use App\Events\musica\EscalaRemovida;


class EscalaMusicaController extends Controller
{
    public function eventos($colaborador = null){
        if(Bouncer::denies('musica-escala-view')){
            abort(403);
        }
        $admin = Bouncer::allows('musica-escala-edit');
        if( $colaborador ){

            $eventosEm30Dias = Evento::proximos30Dias()
                ->whereIn('escala_musica_id', function( $query) use ($colaborador){
                    $query->select('escala_id')
                        ->from('tarefas_escala_musica')
                        ->where('colaborador_id','=',$colaborador);
                })->get()->sortBy('data_hora_inicio');

            $eventosDepois30Dias = Evento::apos30Dias()
                ->whereIn('escala_musica_id', function( $query) use ($colaborador){
                    $query->select('escala_id')
                        ->from('tarefas_escala_musica')
                        ->where('colaborador_id','=',$colaborador);
                })->get()->sortBy('data_hora_inicio');
            $colaborador = ColaboradorMusica::findOrFail($colaborador);
        } else{
            $eventosEm30Dias = Evento::proximos30Dias()
                ->when(!$admin, function( $query ) {
                    return $query->whereNotNull('escala_musica_id');
                })->get()->sortBy('data_hora_inicio');
            $eventosDepois30Dias = Evento::apos30Dias()
                ->when(!$admin, function( $query ) {
                    return $query->whereNotNull('escala_musica_id');
                })->get()->sortBy('data_hora_inicio');
        }

        return view('musica.escala.eventos')
            ->with('colaborador', $colaborador)
            ->with('eventos30Dias', $eventosEm30Dias)
            ->with('eventosApos30Dias', $eventosDepois30Dias);
    }

    public function addTarefa($escala_id, $servico_id){
        $escala = EscalaMusica::findOrFail($escala_id);
        $servico = ServicoMusica::findOrFail($servico_id);
        $colaboradoresServico = $escala->tarefas->where('servico_id',$servico_id)
          ->pluck('colaborador_id')->toArray();

        return view('musica.escala.add-tarefa')
            ->with('escala', $escala)
            ->with('colaboradoresServico', $colaboradoresServico)
            ->with('servico', $servico);
    }

    public function addTarefaAction(Request $request, $escala_id){
        $escala = EscalaMusica::findOrFail($escala_id);

        if( !(Bouncer::allows('musica-escala-edit') ||
         $escala->lider_id == Auth::user()->id) ){
             abort(403);
         }

        $col = ColaboradorMusica::findOrFail($request["colaborador_id"]);
        $servico = ServicoMusica::findOrFail($request["servico_id"]);

        $tarefa = new Tarefa;
        $tarefa->escala_id = $escala->id;
        $tarefa->colaborador_id = $col->id;
        $tarefa->servico_id = $servico->id;

        $tarefa->save();

        if( $escala->publicada){
            event(new TarefaEscalaAdicionada($tarefa));
        }

        return Redirect::route('musica.escala.edit', $escala->id)
            ->with('message', $col->user->name . ' escalado(a) para o serviço de '
                . $servico->descricao);

    }

    public function deleteTarefaAction($tarefa_id){
        $tarefa = Tarefa::findOrFail($tarefa_id);

        if( !(Bouncer::allows('musica-escala-edit') ||
         $tarefa->escala->lider_id == Auth::user()->id) ){
             abort(403);
        }
        
        $tarefa->delete();

        if( $tarefa->escala->publicada){
            event(new TarefaEscalaRemovida($tarefa));
        }


        return Redirect::route('musica.escala.edit', $tarefa->escala->id)
            ->with('message', $tarefa->colaborador->user->name .
                ' removido(a) do serviço ' . $tarefa->servico->descricao);
    }

    public function publish($escala_id){
        $escala = EscalaMusica::findOrFail($escala_id);

        if(Bouncer::denies('musica-escala-edit') &&
            Auth::user()->id != $escala->lider_id){
            abort(403);
        }
        if( $escala->publicada ){
            abort(403);
        }
        $validacao = new EscalaMusicaValidator($escala);
        $servicos = ServicoMusica::all();

        return view('musica.escala.publicar')
            ->with('escala', $escala)
            ->with('servicos', $servicos)
            ->with('validacao', $validacao);
    }

    public function publishAction($escala_id){
        if(Bouncer::denies('musica-escala-edit')){
            abort(403);
        }
        $escala = EscalaMusica::findOrFail($escala_id);
        $escala->publicado_em = \Carbon\Carbon::now();
        $escala->save();

        event( new EscalaPublicada($escala));
        return Redirect::route('musica.eventos')
            ->with('message', 'Escala publicada');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($evento_id){
        if(Bouncer::denies('musica-escala-edit')){
            abort(403);
        }
        $evento = Evento::findOrFail($evento_id);
        $lideres = ColaboradorMusica::lideres()->get();
        return view('musica.escala.create')
            ->with('evento', $evento)
            ->with('lideres', $lideres);
    }

    public function updateLider(Request $request, $id){
        if(Bouncer::denies('musica-escala-edit')){
            abort(403);
        }
        $lider = ColaboradorMusica::findOrFail($request["lider_id"]);
        $evento = Evento::findOrFail($id);
        if(isset($request["escala_id"])){
            // Já existe a escala
            $escala = EscalaMusica::findOrFail($request["escala_id"]);
        } else{
            $escala = new EscalaMusica;
        }

        $antigoLider = $escala->lider;

        $escala->lider_id = $lider->id;
        $escala->evento_id = $evento->id;
        $escala->save();
        $escala->load('lider');
        $evento->escala_musica_id = $escala->id;
        $evento->save();

        if( $escala->publicada ){
            event(new EscalaLiderTrocado($escala, $antigoLider));
        }

        // Salvar e redirecionar para edição
        return Redirect::route('musica.escala.edit', $escala->id)
            ->with('message', 'Líder atualizado!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($escala_id)
    {
        if(Bouncer::denies('musica-escala-view')){
            abort(403);
        }
        $escala = EscalaMusica::findOrFail($escala_id);
        if( !$escala->publicada ){
            abort(403);
        }
        $servicos = ServicoMusica::all();
        return view('musica.escala.show')
            ->with('evento', $escala->evento)
            ->with('servicos', $servicos)
            ->with('escala', $escala);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($escala_id)
    {
        $escala = EscalaMusica::findOrFail($escala_id);
        if( !(Bouncer::allows('musica-escala-edit') ||
         $escala->lider_id == Auth::user()->id) ){
             abort(403);
         }
        $lideres = ColaboradorMusica::lideres()->get()->sortBy(
            function($item){
                return str_slug($item->user->name);
            });
        $servicos = ServicoMusica::all();

        return view('musica.escala.edit')
            ->with('evento', $escala->evento)
            ->with('servicos', $servicos)
            ->with('escala', $escala)
            ->with('lideres', $lideres);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Bouncer::denies('musica-escala-remove')){
            abort(403);
        }
        $escala = EscalaMusica::findOrFail($id);
        // Replicação para manter informações após a deleção do objeto
        $escala_cp = $escala->replicate();
        $ts = $escala->tarefas;
        $evento = $escala->evento;
        $evento->escala_musica_id = null;
        $evento->save();
        $escala->delete();

        $colaboradores = $ts->map( function($item){
            return $item->colaborador->user;})
            ->push($escala_cp->lider->user)->unique();

        event(new EscalaRemovida( $escala_cp, $colaboradores));

        return Redirect::route('musica.eventos')
            ->with('message', 'Escala removida');
    }

    public function testing(){
        // $escala = EscalaMusica::findOrFail($id);
        // $user = \App\User::find(18);
        // return view('emails.musica.escala-publicada-colaboradores')
        //     ->with('escala', $escala)
        //     ->with('user', $user);

        return view('musica.escala.guest-impedimento-registrado');

    }
}
