<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\FinanceHo;
use App\FinanceCompany;
use App\Group;
use App\UserSubscription;

class DashboardController extends BaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = array();

        // Total count of sub-seizers.
        $data['users'] = User::where('id', '!=', User::ADMIN_ID)->count();

        // Total count of finance hos.
        $data['finance_hos'] = FinanceHo::count();

        // Total count of finance companies.
        $data['finance_companies'] = FinanceCompany::count();

        // Total count of groups.
        $data['groups'] = Group::count();

        // Get sub-seizers activation expiration in 3 days.
        $today                       = date("Y-m-d", strtotime(date("Y-m-d")));
        $afterThreeDay               = date("Y-m-d", strtotime('+2 days', strtotime($today)));
        $data['seizers_activations'] = UserSubscription::where(function($query) use($today, $afterThreeDay) {
            $query->whereDate('to', '>=', $today)
                  ->whereDate('to', '<=', $afterThreeDay);
        })->get();

        return view('index', compact('data'));
    }
}
