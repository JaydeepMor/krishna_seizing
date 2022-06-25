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
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubseizersExport;
use Illuminate\Http\UploadedFile;
use App\Notifications\CommonException;
use Notification;

class UserController extends BaseController
{
    public function filter(Request $request, $modal, $query)
    {
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

        if ($request->has('user_id') && !empty($request->get('user_id'))) {
            $query->where($modal::getTableName() . '.id', '=', $request->get('user_id'));
        }

        if ($request->has('subscription_month') && !empty($request->get('subscription_month'))) {
            $query->whereHas('userSubscriptionsWithTrashed', function($query) use($request) {
                $query->whereRaw('DATE_FORMAT(`from`, "%Y") <= "' . date("Y", strtotime($request->get('subscription_month'))) . '" AND DATE_FORMAT(`from`, "%m") <= "' . date("m", strtotime($request->get('subscription_month'))) . '"')
                      ->orWhereRaw('DATE_FORMAT(`to`, "%Y") <= "' . date("Y", strtotime($request->get('subscription_month'))) . '" AND DATE_FORMAT(`to`, "%m") <= "' . date("m", strtotime($request->get('subscription_month'))) . '"');
            });
        }

        return $query;
    }

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

        $query->whereNotIn('id', explode(",", env('TEST_USER_ID', 0)));

        $query->select($modal::getTableName() . '.*');

        $query        = $this->filter($request, $modal, $query);

        $queryStrings = $request->getQueryString();

        $users        = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        $date         = Carbon::now()->addMonth();

        for ($month = 0; $month <= 12; $month++) {
            $date->subMonth();

            $pastOneYearMonths[$date->format('Y-m')] = $date->format('F - Y');
        }

        return view('subseizer.index', compact('users', 'queryStrings', 'pastOneYearMonths'));
    }

    public function export(Request $request)
    {
        $modal        = new User();

        $query        = $modal::query();

        $query->where('id', '!=', User::ADMIN_ID)->whereNotIn('id', explode(",", env('TEST_USER_ID', 0)));

        $query->select('id', 'name', 'address', 'email', 'contact_number', 'team_leader', 'reference_name', 'imei_number', 'status', 'group_id', 'created_at');

        $query        = $this->filter($request, $modal, $query);

        $users        = $query->with(['userSubscriptionsWithTrashed' => function($query) use($request) {
                            if ($request->has('subscription_month') && !empty($request->get('subscription_month'))) {
                                $query->whereRaw('DATE_FORMAT(`from`, "%Y") = "' . date("Y", strtotime($request->get('subscription_month'))) . '" AND DATE_FORMAT(`from`, "%m") = "' . date("m", strtotime($request->get('subscription_month'))) . '"')
                                      ->orWhereRaw('DATE_FORMAT(`to`, "%Y") = "' . date("Y", strtotime($request->get('subscription_month'))) . '" AND DATE_FORMAT(`to`, "%m") = "' . date("m", strtotime($request->get('subscription_month'))) . '"');
                            }
                        }])->get();

        $userArray    = collect([]);

        if (!empty($users) && !$users->isEmpty()) {
            $users->map(function(&$row) use($modal) {
                $row->status  = $modal->statuses[(string)$row->status];
                $row->created = date(DEFAULT_DATE_FORMAT, strtotime($row->created_at));
                $row->group   = !empty($row->group) ? $row->group->name : "";
            });

            $users = $users->toArray();

            // Set dash "-" for null fields.
            foreach($users as $index => $user) {
                foreach ($user as $field => $row) {
                    if (empty($row)) {
                        if (is_array($row)) {
                            $users[$index][$field] = [];
                        } else {
                            $users[$index][$field] = "-";
                        }
                    }
                }
            }

            // Remove unnecessary fields.
            foreach($users as $field => $user) {
                unset($user['current_subscription']);
                unset($user['is_subscribed']);
                unset($user['group_id']);
                unset($user['api_key']);

                $userSubscriptions = $user['user_subscriptions_with_trashed'];

                unset($user['user_subscriptions_with_trashed']);

                $user['temp_created'] = $user['created'];

                unset($user['created']);

                $user['group']   = !empty($user['group']['name']) ? $user['group']['name'] : "";

                $user['created'] = $user['temp_created'];

                unset($user['temp_created']);

                $userArray->push($user);

                if (!empty($userSubscriptions)) {
                    $tempSubscription = [];

                    // Add heading row.
                    for ($space = 0; $space < 8; $space++) {
                        $tempSubscription[] = "";
                    }
                    $tempSubscription['group']   = "Subscription From";
                    $tempSubscription['created'] = "Subscription To";

                    $userArray->push($tempSubscription);

                    foreach ($userSubscriptions as $userSubscription) {
                        $tempSubscription = [];

                        for ($space = 0; $space < 8; $space++) {
                            $tempSubscription[] = "";
                        }

                        $tempSubscription['group']   = date(DEFAULT_DATE_FORMAT, strtotime($userSubscription['from']));
                        $tempSubscription['created'] = date(DEFAULT_DATE_FORMAT, strtotime($userSubscription['to']));

                        $userArray->push($tempSubscription);
                    }
                }
            }
        }

        return Excel::download(new SubseizersExport($userArray), 'Exported-Subseizers-' . $request->get('subscription_month', date('Ymd')) . '.xlsx');
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

        $idProofUpload = !empty($data['id_proof']) ? $data['id_proof'] : null;
        $selfieUpload  = !empty($data['selfie']) ? $data['selfie'] : null;

        unset($data['id_proof']);
        unset($data['selfie']);

        if (!empty($idProofUpload) && $idProofUpload instanceof UploadedFile) {
            $idProof   = $idProofUpload;

            $pathInfos = pathinfo($idProof->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $idProof->storeAs($model->idProofPath, $fileName, $model->fileSystem);

                if ($storeFile) {
                    $data['id_proof'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading. Store File : " . $storeFile)));

                    return redirect()->route('subseizer.index')->with('danger', __('We aren\'t able to store id proof. Please contact administrator'));
                }
            }
        }

        if (!empty($selfieUpload) && $selfieUpload instanceof UploadedFile) {
            $selfie    = $selfieUpload;

            $pathInfos = pathinfo($selfie->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $selfie->storeAs($model->selfiePath, $fileName, $model->fileSystem);

                if ($storeFile) {
                    $data['selfie'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading. Store File : " . $storeFile)));

                    return redirect()->route('subseizer.index')->with('danger', __('We aren\'t able to store selfie. Please contact administrator'));
                }
            }
        }

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

        ApiKey::generateKey($create->id, '0');

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

        $idProofUpload = !empty($data['id_proof']) ? $data['id_proof'] : null;
        $selfieUpload  = !empty($data['selfie']) ? $data['selfie'] : null;

        unset($data['id_proof']);
        unset($data['selfie']);

        if (!empty($idProofUpload) && $idProofUpload instanceof UploadedFile) {
            $idProof   = $idProofUpload;

            $pathInfos = pathinfo($idProof->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $idProof->storeAs($model->idProofPath, $fileName, $model->fileSystem);

                if ($storeFile) {
                    $data['id_proof'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading. Store File : " . $storeFile)));

                    return redirect()->route('subseizer.index')->with('danger', __('We aren\'t able to store id proof. Please contact administrator'));
                }
            }
        }

        if (!empty($selfieUpload) && $selfieUpload instanceof UploadedFile) {
            $selfie    = $selfieUpload;

            $pathInfos = pathinfo($selfie->getClientOriginalName());

            if (!empty($pathInfos['extension'])) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);

                $storeFile = $selfie->storeAs($model->selfiePath, $fileName, $model->fileSystem);

                if ($storeFile) {
                    $data['selfie'] = $fileName;
                } else {
                    Notification::route('mail', config('mail.mine.email', 'it.jaydeep.mor@gmail.com'))->notify(new CommonException(__("User id proof not uploading. Store File : " . $storeFile)));

                    return redirect()->route('subseizer.index')->with('danger', __('We aren\'t able to store selfie. Please contact administrator'));
                }
            }
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
        $user = User::find($id);

        if (!empty($user)) {
            $email = emailPlusAddressing($user->email);

            $imei  = imeiPlusAddressing($user->imei_number);

            $user->email       = $email;

            $user->imei_number = $imei;

            $user->save();

            User::where('id', $id)->delete();

            UserVehicleFieldPermission::where('user_id', $id)->delete();

            UserActivity::where('user_id', $id)->delete();

            UserSubscription::where('user_id', $id)->delete();

            ApiKey::where('user_id', $id)->delete();

            return redirect()->route('subseizer.index')->with('success', __('Record deleted successfully!'));
        }

        return redirect()->route('subseizer.index')->with('danger', __('Something went wrong! Please try again.'));
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

        $query->select($modal::getTableName() . '.*', User::getTableName() . '.name', Vehicle::getTableName() . '.registration_number');

        $query->where(User::getTableName() . '.is_admin', User::IS_USER);

        $query->whereNotIn(User::getTableName() . '.id', explode(",", env('TEST_USER_ID', 0)));

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

        $vehicles = $query->orderBy($modal::getTableName() . '.id', 'DESC')->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('subseizer.activity.index', compact('vehicles'));
    }
}
