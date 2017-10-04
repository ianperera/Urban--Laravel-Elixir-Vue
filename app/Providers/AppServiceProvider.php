<?php

namespace App\Providers;

use App\Exceptions\GeneralException;
use App\Http\Requests\Request;

use App\Models\RtoCompany;
use App\Models\Dealer;
use App\Models\Order;
use App\Models\Building;
use App\Models\BuildingOption;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;
use App\Models\BuildingPackage;
use App\Models\BuildingPackageCategory;
use App\Models\Option;
use App\Models\OrderReference;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use ClassPreloader\Config;
use Faker\Provider\DateTime;

use App\Observers\BuildingObserver;
use App\Observers\BuildingOptionObserver;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * @return void
     * @throws GeneralException
     */
    public function boot()
    {
        Relation::morphMap([
            'user' => User::class,
            'dealer' => Dealer::class,
            'rto-company' => RtoCompany::class,
            'building' => Building::class,
            'building-package' => BuildingPackage::class,
            'building-package-category' => BuildingPackageCategory::class,
            'order'    => Order::class,
            'order-reference'    => OrderReference::class,
            'status'   => BuildingHistory::class,
            'location' => BuildingLocation::class,
            'option'   => Option::class,
        ]);

        // skip timezone if DB config is not specifed (on deployment)
        if (!env('DB_CONNECTION')) return;

        $timezone = Setting::where('id', 'time_zone')->value('value');
        if ($timezone) {

            $dt = new \DateTime('now', new \DateTimeZone($timezone));
            $abbreviation = $dt->format('T');

            // set default timezone for Cargon/DateTime ..
            date_default_timezone_set($timezone);

            config(['app.timezone' => $timezone]);
            view()->share('time_zone', [
                'name' => $timezone,
                'abbr' => $abbreviation
            ]);

            $offsetToFormat = new \DateTime('now', new \DateTimeZone($timezone));
            $timezoneOffset = $offsetToFormat->format('P');

            DB::statement("SET time_zone = '".$timezoneOffset."'");
        }


        Building::observe(BuildingObserver::class);
        BuildingOption::observe(BuildingOptionObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
