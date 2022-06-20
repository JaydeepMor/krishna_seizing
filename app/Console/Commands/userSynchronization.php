<?php

namespace App\Console\Commands;

use App\UserSynchronization as UserSynchronizationModel;
use App\User;
use App\FinanceCompany;
use App\Vehicle;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class userSynchronization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:synchronization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User synchronization with vehicles.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Finance companies.
        $financeCompanies = FinanceCompany::all();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserSynchronizationModel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Users.
        $users = User::all();

        $now = Carbon::now();

        if (!empty($financeCompanies)) {
            $financeCompanyIds = $financeCompanies->pluck('id');

            // Get vehicle count finance company wise.
            $vehicles = Vehicle::selectRaw('count(`id`) as count, `finance_company_id`')->whereIn('finance_company_id', $financeCompanyIds)->groupBy('finance_company_id')->whereNotNull('registration_number')->where('registration_number', '!=', '')->get()->pluck('count', 'finance_company_id')->toArray();

            foreach ($financeCompanies as $financeCompany) {
                foreach ($users as $user) {
                    $insert[] = [
                        'user_id' => $user->id,
                        'finance_company_id' => $financeCompany->id,
                        'vehicle_count' => (!empty($vehicles[$financeCompany->id])) ? $vehicles[$financeCompany->id] : 0,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            UserSynchronizationModel::insert($insert);
        }
    }
}
