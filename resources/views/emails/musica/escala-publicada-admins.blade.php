@component('mail::message')
Escala de Música publicada
========
**{{$admin->name}}**, uma nova escala de música foi publidada.

## Evento
**{{$escala->evento->titulo}}**, dia **
  {{$escala->evento->data_hora_inicio->format('d/m/Y')}}** às **
  {{$escala->evento->data_hora_inicio->format('G\hi')}}**.

## Escala
|  |  |
| ------ | ------ |
| **Líder** | {{$escala->lider->user->name}} |
@foreach ($escala->tarefas->sortBy('servico_id')->groupBy('servico_id') as $tarefas)
| {{$tarefas->first()->servico->descricao}} | {{$tarefas->map(function($item){return $item->colaborador->user->name;})->implode(', ')}} |
@endforeach

{{--  Ver escala --}}
@component('mail::button',['url'=>config('app.url') . '/musica/escala/' . $escala->id ])
Ver escala
@endcomponent


Você está recebendo este email porque está cadastrado como Administrador da Música no [Diakonia]({{config('app.url')}}).

@endcomponent
