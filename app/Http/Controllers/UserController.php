<?php

namespace App\Http\Controllers;

use App\User;
use App\UserSubscription;
use App\UserActivity;
use App\Group;
use App\UserVehicleFieldPermission;
use App\Vehicle;
use App\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new User();

        $query = $modal::query();

        $query->where('is_admin', $modal::IS_USER);

        $query->select($modal::getTableName() . '.*');

        if ($request->has('name') && !empty($request->get('name'))) {
            $query->where($modal::getTableName() . '.name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if ($request->has('imei_number') && !empty($request->get('imei_number'))) {
            $query->where($modal::getTableName() . '.imei_number', '=', $request->get('imei_number'));
        }

        if ($request->has('address') && !empty($request->get('address'))) {
            $query->where($modal::getTableName() . '.address', 'LIKE', '%' . $request->get('address') . '%');
        }

        if ($request->has('contact_number') && !empty($request->get('contact_number'))) {
            $query->where($modal::getTableName() . '.contact_number', 'LIKE', '%' . $request->get('contact_number') . '%');
        }

        $users = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('subseizer.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modal                = new User();

        $groups               = Group::all();

        $vehicleFields        = (defined("EXCEL_DATABASE_FIELDS") && !empty(EXCEL_DATABASE_FIELDS)) ? json_decode(EXCEL_DATABASE_FIELDS, true) : [];

        $vehicleDefaultFields = (defined("EXCEL_DATABASE_DEFAULT_FIELDS") && !empty(EXCEL_DATABASE_DEFAULT_FIELDS)) ? json_decode(EXCEL_DATABASE_DEFAULT_FIELDS, true) : [];

        return view('subseizer.create', compact('modal', 'groups', 'vehicleFields', 'vehicleDefaultFields'));
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

        $model     = new User();

        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data['password'] = Hash::make($data['password']);

        $create = $model::create($data);

        $userVehicleFieldPermissionModal = new UserVehicleFieldPermission();

        $userVehicleFieldPermissionData = [
            "vehicle_allowed_fields" => json_encode($data["vehicle_allowed_fields"]),
            "user_id"                => $create->id
        ];

        $validator = $userVehicleFieldPermissionModal->validator($userVehicleFieldPermissionData);

        if (!$validator->fails()) {
            $userVehicleFieldPermissionModal::create($userVehicleFieldPermissionData);
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return redirect()->route('subseizer.index')->with('success', __('Record added successfully!'));
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
        $modal                 = new User();

        $row                   = $modal::find($id);

        $vehicleFields         = (defined("EXCEL_DATABASE_FIELDS") && !empty(EXCEL_DATABASE_FIELDS)) ? json_decode(EXCEL_DATABASE_FIELDS, true) : [];

        $vehicleSelectedFields = UserVehicleFieldPermission::where('user_id', $id)->first();

        $vehicleSelectedFields = (!empty($vehicleSelectedFields)) ? json_decode($vehicleSelectedFields->vehicle_allowed_fields, true) : [];

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $groups = Group::all();

        return view('subseizer.edit', compact('modal', 'row', 'groups', 'vehicleFields', 'vehicleSelectedFields'));
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

        $model     = new User();

        $validator = $model->validator($data, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $row       = $model::find($id);

        if (empty($row)) {
            return redirect()->route('subseizer.index')->with('danger', __('No record found!'));
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $row->update($data);

        $userVehicleFieldPermissionModal = new UserVehicleFieldPermission();

        $userVehicleFieldPermissionData = [
            "vehicle_allowed_fields" => json_encode($data["vehicle_allowed_fields"]),
            "user_id"                => $id
        ];

        $validator = $userVehicleFieldPermissionModal->validator($userVehicleFieldPermissionData);

        if (!$validator->fails()) {
            $userVehicleFieldRow = $userVehicleFieldPermissionModal::where('user_id', $id)->first();

            if (!empty($userVehicleFieldRow)) {
                $userVehicleFieldPermissionModal::where('user_id', $id)->update($userVehicleFieldPermissionData);
            } else {
                $userVehicleFieldPermissionModal::create($userVehicleFieldPermissionData);
            }
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return redirect()->route('subseizer.index')->with('success', __('Record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        User::where('id', $id)->delete();

        UserVehicleFieldPermission::where('user_id', $id)->delete();

        return redirect()->route('subseizer.index')->with('success', __('Record deleted successfully!'));
    }

    public function subscription(Request $request, $id)
    {
        $isSubscribed = $request->get("is_subscribed", "");

        $row          = User::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        if ($isSubscribed == "on") {
            $this->setSubscribed($id);

            return redirect(url()->previous())->with('success', __('Record updated successfully!'));
        } elseif ($isSubscribed == "off") {
            $this->setUnsubscribed($id);

            return redirect(url()->previous())->with('success', __('Record updated successfully!'));
        }

        return redirect(url()->previous())->with('danger', __('Something went wrong! Please try again.'));
    }

    public function setSubscribed(int $userId)
    {
        $model = new UserSubscription();
        $from  = date(DEFAULT_DATE_FORMAT, strtotime(date(DEFAULT_DATE_FORMAT)));
        $to    = date(DEFAULT_DATE_FORMAT, strtotime('+' . (int)USER_APP_RESTORE_CYCLE_DAYS . ' days', strtotime($from)));

        // Remove old subscriptions.
        $model::where("user_id", $userId)->delete();

        // Set new api-key.
        ApiKey::appendKey($userId);

        // Add new subscriptions.
        return $model::create(["user_id" => $userId, "from" => $from, "to" => $to]);
    }

    public function setUnsubscribed(int $userId)
    {
        $model = new UserSubscription();

        // Check available subscriptions.
        $check = $model::where("user_id", $userId)->first();

        if (empty($check)) {
            return $this->setSubscribed($userId);
        } else {
            // Set api-key disabled.
            ApiKey::removeKey($userId);

            $to = date(DEFAULT_DATE_FORMAT, strtotime(date(DEFAULT_DATE_FORMAT)));

            return $model::where("user_id", $userId)->update(["to" => $to]);
        }
    }

    public function getActivity(Request $request)
    {
        $modal = new UserActivity();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*', User::getTableName() . '.name', Vehicle::getTableName() . '.model');

        $query->join(User::getTableName(), $modal::getTableName() . '.user_id', '=', User::getTableName() . '.id');

        $query->join(Vehicle::getTableName(), $modal::getTableName() . '.vehicle_id', '=', Vehicle::getTableName() . '.id');

        if ($request->has('name') && !empty($request->get('name'))) {
            $query->where(User::getTableName() . '.name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if ($request->has('vehicle') && !empty($request->get('vehicle'))) {
            $query->where(Vehicle::getTableName() . '.model', 'LIKE', '%' . $request->get('vehicle') . '%');
        }

        if ($request->has('latitude') && !empty($request->get('latitude'))) {
            $query->where($modal::getTableName() . '.latitude', 'LIKE', '%' . $request->get('latitude') . '%');
        }

        if ($request->has('longitude') && !empty($request->get('longitude'))) {
            $query->where($modal::getTableName() . '.longitude', 'LIKE', '%' . $request->get('longitude') . '%');
        }

        $vehicles = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('subseizer.activity.index', compact('vehicles'));
    }
}
