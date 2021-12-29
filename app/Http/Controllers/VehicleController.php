<?php

namespace App\Http\Controllers;

use App\Vehicle;
use App\User;
use App\Imports\VehiclesImport;
use App\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\VehicleConfirmed;
use App\Notifications\VehicleCancelled;
use App\Channels\WhatsAppChannel;
use Illuminate\Http\Response;
use Maatwebsite\Excel\HeadingRowImport;

class VehicleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal               = new Vehicle();

        $modalFinanceCompany = new FinanceCompany();

        $query               = $modal::query();

        $query->select($modal::getTableName() . '.*');

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

        if ($request->has('finance_company_id') && !empty($request->get('finance_company_id'))) {
            $query->join($modalFinanceCompany::getTableName(), $modal::getTableName() . '.finance_company_id', '=', $modalFinanceCompany::getTableName() . '.id')
                  ->where($modalFinanceCompany::getTableName() . '.id', '=', (int)$request->get('finance_company_id'));
        }

        $users            = User::where('id', '!=', User::ADMIN_ID)->where('status', User::STATUS_ACTIVE)->get();

        $todayDate        = strtotime(date(DEFAULT_DATE_FORMAT));

        $vehicles         = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        $financeCompanies = FinanceCompany::all();

        return view('vehicle.index', compact('vehicles', 'users', 'todayDate', 'financeCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nextLotNumber    = Vehicle::getNextLotNumber();

        $financeCompanies = FinanceCompany::all();

        return view('vehicle.create', compact('nextLotNumber', 'financeCompanies'));
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
                    $nextLotNumber     = $model::getNextLotNumber();

                    $headings          = (new HeadingRowImport)->toArray($excelVehicles);

                    $validatorHeadings = $model->excelHeadingsValidator($headings);

                    if (!empty($validatorHeadings['code']) && $validatorHeadings['code'] == 401) {
                        return redirect()->route('vehicle.index')->with('danger', $validatorHeadings['msg']);
                    } 

                    try {
                        // Remove old finance company data.
                        $this->removeFinanceVehicles($request, $data['finance_company_id']);

                        Excel::import(new VehiclesImport($nextLotNumber, $data['finance_company_id']), "public/" . $storeFile);
                    } catch (\Exception $e) {
                        return redirect()->route('vehicle.index')->with('danger', __($e->getMessage()));
                    }

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
        $data      = $request->all();

        $model     = new Vehicle();

        $allNull   = true;

        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($data as $key => $field) {
            if ($key != "_token" && $key != "finance_company_id" && !empty($field)) {
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

        $financeCompanies = FinanceCompany::all();

        return view('vehicle.edit', compact('row', 'financeCompanies'));
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
        $data      = $request->all();

        $model     = new Vehicle();

        $allNull   = true;

        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($data as $key => $field) {
            if ($key != "_token" && $key != "_method" && $key != "finance_company_id" && !empty($field)) {
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

        $user      = User::find($userId);

        if ($isConfirm == 'on' && (empty($userId) || empty($user))) {
            return redirect(url()->previous())->with('danger', __('No user found!'));
        }

        $row       = $model::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $isUpdate = $row->update(['is_confirm' => ($isConfirm == 'on' ? $model::CONFIRM : $model::NOT_CONFIRM), 'user_id' => ($isConfirm == 'on' ? $userId : NULL)]);

        if ($isUpdate) {
            // Send WhatsApp message.
            if (!empty($user)) {
                $whatsappNotify = (new WhatsAppChannel())->send($user, new VehicleConfirmed($row));

                if (!empty($whatsappNotify['code'])) {
                    if (!empty($whatsappNotify['msg'])) {
                        if ($whatsappNotify['code'] == 401) {
                            return redirect(url()->previous())->with('danger', __('Record updated successfully!') . "<br /> But whatsapp notification not send." . "<br /> Issue is : " . $whatsappNotify['msg']);
                        }

                        return redirect(url()->previous())->with('success', __('Record updated successfully! <br /> And also sent whatsapp notification to the ') . '<a href="' . route('subseizer.index', ['user_id' => $user->id]) . '" target="_blank">' . $user->name . '</a>');
                    }
                } else {
                    return redirect(url()->previous())->with('danger', __('Record updated successfully!<br /> But whatsapp notification not send.<br /> Please check whatsapp number and try again after sometime.'));
                }
            }
        }

        return redirect(url()->previous())->with('success', __('Record updated successfully!'));
    }

    public function cancelVehicle(Request $request, $id)
    {
        $model    = new Vehicle();

        $isCancel = $request->get('is_cancel', $model::NOT_CANCEL);

        $userId    = $request->get('user_id', NULL);

        $user      = User::find($userId);

        if ($isCancel == 'on' && (empty($userId) || empty($user))) {
            return redirect(url()->previous())->with('danger', __('No user found!'));
        }

        $row      = $model::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $isUpdate = $row->update(['is_cancel' => ($isCancel == 'on' ? $model::CANCEL : $model::NOT_CANCEL), 'user_id' => ($isCancel == 'on' ? $userId : NULL)]);

        if ($isUpdate) {
            // Send WhatsApp message.
            if (!empty($user)) {
                $whatsappNotify = (new WhatsAppChannel())->send($user, new VehicleCancelled($row));

                if (!empty($whatsappNotify['code'])) {
                    if (!empty($whatsappNotify['msg'])) {
                        if ($whatsappNotify['code'] == 401) {
                            return redirect(url()->previous())->with('danger', __('Record updated successfully!') . "<br /> But whatsapp notification not send." . "<br /> Issue is : " . $whatsappNotify['msg']);
                        }

                        return redirect(url()->previous())->with('success', __('Record updated successfully! <br /> And also sent whatsapp notification to the ') . '<a href="' . route('subseizer.index', ['user_id' => $user->id]) . '" target="_blank">' . $user->name . '</a>');
                    }
                } else {
                    return redirect(url()->previous())->with('danger', __('Record updated successfully!<br /> But whatsapp notification not send.<br /> Please check whatsapp number and try again after sometime.'));
                }
            }
        }

        return redirect(url()->previous())->with('success', __('Record updated successfully!'));
    }

    public function downloadSampleExcel()
    {
        $model = new Vehicle();

        $file  = storage_path() . "/app/public/vehicle/" . $model->sampleExcelFileName;

        return response()->download($file, $model->sampleExcelFileName, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $model->sampleExcelFileName . '"'
        ]);
    }

    public function removeFinanceVehicles(Request $request, $financeCompanyId)
    {
        Vehicle::where('finance_company_id', $financeCompanyId)->delete();

        // Get finance company name.
        $financeCompany = FinanceCompany::find($financeCompanyId);

        $msg = 'Records deleted successfully!';

        if (!empty($financeCompany)) {
            $msg = '<a href="' . route('company.index', ['id' => $financeCompany->id]) . '" target="_blank">' . $financeCompany->name . '</a> records deleted successfully!';
        }

        return redirect()->route('vehicle.index')->with('success', __($msg));
    }
}
