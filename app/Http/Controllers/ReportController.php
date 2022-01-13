<?php

namespace App\Http\Controllers;

use App\Vehicle;
use App\FinanceCompany;
use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehiclesExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

        $financeCompanies = FinanceCompany::all();

        return view('report.index', compact('vehicles', 'queryStrings', 'financeCompanies'));
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
            $query->where($modal::getTableName() . '.registration_number', 'LIKE', '%' . $request->get('registration_number') . '%');
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
            $query->whereRaw('DATE(' . $modal::getTableName() .'.`created_at`) >= "' . date("Y-m-d", strtotime($request->get('from_date'))) . '"');
        }

        if ($request->has('to_date') && !empty($request->get('to_date'))) {
            $query->whereRaw('DATE(' . $modal::getTableName() .'.`created_at`) <= "' . date("Y-m-d", strtotime($request->get('to_date'))) . '"');
        }

        if ($request->has('finance_company_id') && !empty($request->get('finance_company_id'))) {
            $query->join(FinanceCompany::getTableName(), $modal::getTableName() . '.finance_company_id', '=', FinanceCompany::getTableName() . '.id')
                  ->where(FinanceCompany::getTableName() . '.id', '=', (int)$request->get('finance_company_id'));
        }

        return $query;
    }

    public function export(Request $request)
    {
        $now       = Carbon::now();

        $excelName = 'Exported-vehicles-' . $now->timestamp . '.xlsx';

        // (new VehiclesExport)->queue($excelName, 'vehicle_export');

        (new VehiclesExport($request->all()))->store($excelName, 'vehicle_export');

        $queryStrings = $request->getQueryString();

        return redirect(route('report.index', $queryStrings))->with('success', __('We have started vehicle exporting.  <br /> You will get download link to <a href="mailto:' . env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL', '') . '">' . env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL', '') . '</a> address.'));
    }

    public function download(Request $request, $fileName)
    {
        $exists = Storage::disk('vehicle_export')->exists($fileName);

        if ($exists) {
            $file = Storage::disk('vehicle_export')->path($fileName);

            return response()->download($file);
        }

        return redirect(route('report.index'))->with('warning', __('File does not exists now. Try to re-export it.'));
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
