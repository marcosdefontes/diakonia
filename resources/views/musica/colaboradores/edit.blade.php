@extends( 'musica.template-musica')

@section('nivel2')
    <li class="active"><a href="{{route('musica.colaborador.index')}}">
    Equipe de Música</a></li>
@stop
@section('nivel3')<li class="active">Atualizar membro da equipe de música</li>@stop


    @section('content')

        <div class="container-fluid">
            {{ Form::model($colaborador, ['method' => 'PATCH',
                'route' => ['musica.colaborador.update',$colaborador->id],
                'class'=> 'form-horizontal',
                'name'=>'colaboradorMusicaForm']) }}
                {{ Form::hidden('id', $colaborador->id) }}
                <div class="form-group">
                    <div class="col-sm-2 text-right">
                    <img src="{{ URL($colaborador->user->avatarPathSmall()) }}" alt="" />
                    </div>
                  <div class="col-sm-4 name-title-2">
                      {{ $colaborador->user->name }}
                  </div>
                </div>

                @include('musica.colaboradores.form',[
                    'submitButton'=>'Atualizar'
                    ])


            {{ Form::close() }}
        </div>

        @component('layouts.geral.modal-exclusao')
            @slot('modalId')modalRemoverColaboradorMusica @endslot
            @slot('modalTitle')Remover Colaborador (Música) @endslot
            @slot('deleteRoute')
                musica.colaborador.destroy
            @endslot
            @slot('deleteId'){{$colaborador->id}} @endslot
            Remover o colaborador {{$colaborador->user->name}}?
        @endcomponent



    @endsection

    @section('scripts')

        <script type="text/javascript">
            $(document).ready(function() {
                $(".select-usuario-colaborador").select2({
                    placeholder: 'Escolha um dos usuários cadastrados',
                    allowClear: true,
                    width: '100%'
                });

                $(".image-picker").imagepicker({
                    show_label: true
                });
            });
        </script>

    @endsection
