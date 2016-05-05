<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      /**
       * Does not support nesting
       */
      Blade::directive('index', function($expression) {
          return '<?php echo $index; ?>';
      });
      Blade::directive('foreachIndexed', function($expression) {
          return '<?php $index = 1; foreach' . $expression . ': ?>';
      });
      Blade::directive('endforeachIndexed', function($expression) {
          return '<?php $index++; endforeach; ?>';
      });

      Carbon::setLocale('pt_BR');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
