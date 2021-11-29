<?php

namespace App\Http\Controllers;

use App\Vehicle;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehiclesExport;

class ReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new Vehicle();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*');

        $query        = $this->filter($request, $modal, $query);

        $vehicles     = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        $queryStrings = $request->getQueryString();

        return view('report.index', compact('vehicles', 'queryStrings'));
    }

    public function filter(Request $request, $modal, $query)
    {
        if ($request->has('customer_name') && !empty($request->get('customer_name'))) {
            $query->where($modal::getTableName() . '.customer_name', 'LIKE', '%' . $request->get('customer_name') . '%');
        }

        if ($request->has('model') && !empty($request->get('model'))) {
            $query->where($modal::getTableName() . '.model', 'LIKE', '%' . $request->get('model') . '%');
        }

        if ($request->has('registration_number') && !empty($request->get('registration_number'))) {
            $query->where($modal::getTableName() . '.registration_number', '=', $request->get('registration_number'));
        }

        if ($request->has('mobile_number') && !empty($request->get('mobile_number'))) {
            $query->where($modal::getTableName() . '.mobile_number', 'LIKE', '%' . $request->get('mobile_number') . '%');
        }

        if ($request->has('address') && !empty($request->get('address'))) {
            $query->where($modal::getTableName() . '.address', 'LIKE', '%' . $request->get('address') . '%');
        }

        if ($request->has('branch') && !empty($request->get('branch'))) {
            $query->where($modal::getTableName() . '.branch', 'LIKE', '%' . $request->get('branch') . '%');
        }

        if ($request->has('area') && !empty($request->get('area'))) {
            $query->where($modal::getTableName() . '.area', 'LIKE', '%' . $request->get('area') . '%');
        }

        if ($request->has('region') && !empty($request->get('region'))) {
            $query->where($modal::getTableName() . '.region', 'LIKE', '%' . $request->get('region') . '%');
        }

        if ($request->has('from_date') && !empty($request->get('from_date'))) {
            $query->whereRaw('DATE(`created_at`) >= "' . date("Y-m-d", strtotime($request->get('from_date'))) . '"');
        }

        if ($request->has('to_date') && !empty($request->get('to_date'))) {
            $query->whereRaw('DATE(`created_at`) <= "' . date("Y-m-d", strtotime($request->get('to_date'))) . '"');
        }

        return $query;
    }

    public function export(Request $request)
    {
        $modal    = new Vehicle();

        $query    = $modal::query();

        $query->select('loan_number', 'customer_name', 'model', 'registration_number', 'chassis_number', 'engine_number', 'arm_rrm', 'mobile_number', 'brm', 'final_confirmation', 'final_manager_name', 'final_manager_mobile_number', 'address', 'branch', 'bkt', 'area', 'region', 'is_confirm', 'is_cancel', 'created_at');

        $query    = $this->filter($request, $modal, $query);

        $vehicles = $query->get();

        $vehicles->map(function(&$row) use($modal) {
            $row->is_confirm = $modal->isConfirm[$row->is_confirm];
            $row->is_cancel  = $modal->isCancel[$row->is_cancel];
            $row->created    = date(DEFAULT_DATE_FORMAT, strtotime($row->created_at));
        });

        return Excel::download(new VehiclesExport($vehicles), 'Exported-vehicles.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        
    }
}
