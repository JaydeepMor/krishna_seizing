<?php

namespace App\Http\Controllers;

use App\Vehicle;
use App\User;
use App\Imports\VehiclesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends BaseController
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

        $users     = User::where('id', '!=', User::ADMIN_ID)->where('status', User::STATUS_ACTIVE)->get();

        $todayDate = strtotime(date(DEFAULT_DATE_FORMAT));

        $vehicles  = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('vehicle.index', compact('vehicles', 'users', 'todayDate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nextLotNumber = Vehicle::getNextLotNumber();

        return view('vehicle.create', compact('nextLotNumber'));
    }

    public function importExcel(Request $request)
    {
        $data  = $request->all();

        $model = new Vehicle();

        $validator = $model->excelValidator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $excelVehicles = $data['excel_import'];
        $pathInfos     = pathinfo($excelVehicles->getClientOriginalName());

        if (!empty($pathInfos['extension'])) {
            $folder = $model->excelPath;

            if (!empty($folder)) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);
                $storeFile = $excelVehicles->storeAs($folder, $fileName, $model->fileSystem);

                if (!empty($storeFile)) {
                    $nextLotNumber = $model::getNextLotNumber();

                    Excel::import(new VehiclesImport($nextLotNumber), $excelVehicles);

                    return redirect()->route('vehicle.index')->with('success', __('Record added successfully!'));
                }
            }
        }

        return redirect(url()->previous())->with('danger', __('Something went wrong! Please upload again.'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data    = $request->all();

        $model   = new Vehicle();

        $allNull = true;

        foreach ($data as $key => $field) {
            if ($key != "_token" && !empty($field)) {
                $allNull = false;
            }
        }

        if ($allNull) {
            return redirect()->route('vehicle.index')->with('danger', __('All fields is null. Add some data to add vehicle.'));
        }

        $data["lot_number"] = $model::getNextLotNumber();

        $model::create($data);

        return redirect()->route('vehicle.index')->with('success', __('Record added successfully!'));
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
        $row = Vehicle::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        return view('vehicle.edit', compact('row'));
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
        $data    = $request->all();

        $model   = new Vehicle();

        $allNull = true;

        foreach ($data as $key => $field) {
            if ($key != "_token" && $key != "_method" && !empty($field)) {
                $allNull = false;
            }
        }

        if ($allNull) {
            return redirect()->route('vehicle.index')->with('danger', __('All fields is null. Add some data to update vehicle.'));
        }

        $row     = $model::find($id);

        if (empty($row)) {
            return redirect()->route('vehicle.index')->with('danger', __('No record found!'));
        }

        $row->update($data);

        return redirect()->route('vehicle.index')->with('success', __('Record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        Vehicle::where('id', $id)->delete();

        return redirect()->route('vehicle.index')->with('success', __('Record deleted successfully!'));
    }

    public function confirmVehicle(Request $request, $id)
    {
        $model     = new Vehicle();

        $isConfirm = $request->get('is_confirm', $model::NOT_CONFIRM);

        $userId    = $request->get('user_id', NULL);

        if ($isConfirm == 'on' && empty($userId)) {
            return redirect(url()->previous())->with('danger', __('No user found!'));
        }

        $row       = $model::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $row->update(['is_confirm' => ($isConfirm == 'on' ? $model::CONFIRM : $model::NOT_CONFIRM), 'user_id' => ($isConfirm == 'on' ? $userId : NULL)]);

        return redirect(url()->previous())->with('success', __('Record updated successfully!'));
    }

    public function cancelVehicle(Request $request, $id)
    {
        $model    = new Vehicle();

        $isCancel = $request->get('is_cancel', $model::NOT_CANCEL);

        $row      = $model::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $row->update(['is_cancel' => ($isCancel == 'on' ? $model::CANCEL : $model::NOT_CANCEL)]);

        return redirect(url()->previous())->with('success', __('Record updated successfully!'));
    }
}