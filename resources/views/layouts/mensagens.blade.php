{{--
@if( $errors->any())
    <ul class="alert alert-danger">
      @foreach( $errors->all() as $error)
        <li> {{ $error }}
        </li>
      @endforeach
    </ul>
    <hr class="divider">
@endif--}}

@if( Session::has('message') )
  <div class="alert bg-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
    {{Session::get('message')}}
  </div>
  <hr class="divider mensagem">
@endif

@if( Session::has('erro') )
  <div class="alert bg-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
    {{Session::get('erro')}}
  </div>
@endif

@if (session()->has('flash_notification.message'))
  <div class="alert alert-{{ session('flash_notification.level') }}">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

      {!! session('flash_notification.message') !!}
  </div>
@endif
