<?php

namespace Newnet\Setting;

use Illuminate\Support\Facades\Blade;
use Newnet\Module\Support\BaseModuleServiceProvider;
use Newnet\Setting\Contracts\SettingInterface;
use Newnet\Setting\Models\Setting as SettingModel;
use Newnet\Setting\Repositories\SettingRepositoryInterface;
use Newnet\Setting\Repositories\SettingRepository;

class SettingServiceProvider extends BaseModuleServiceProvider
{
    public function register()
    {
        $this->app->singleton(SettingRepositoryInterface::class, function () {
            return new SettingRepository(new SettingModel);
        });

        $this->app->singleton(SettingInterface::class, Setting::class);
    }

    public function boot()
    {
        parent::boot();

        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('setting', function ($expression) {
            return "<?php echo setting($expression); ?>";
        });
    }
}
