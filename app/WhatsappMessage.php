<?php

namespace App;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use App\Vehicle;

class WhatsappMessage extends BaseModel
{
    use SoftDeletes;

    public $timestamps = true;

    const FROM_ADMIN = "Admin";

    protected $fillable = [
        'sid',
        'from',
        'to',
        'body',
        'user_id'
    ];

    public function validator(array $data, int $id = NULL)
    {
        return Validator::make($data, [
            'sid'     => ['nullable', 'string', 'max:255'],
            'from'    => ['nullable', 'string', 'max:255'],
            'to'      => ['nullable', 'string', 'max:255'],
            'body'    => ['nullable', 'string', 'max:255'],
            'user_id' => ['required', 'integer', 'exists:' . User::getTableName() . ',id']
        ]);
    }

    public static function messageFormatForCancelled(Vehicle $vehicle, User $user)
    {
        // Get permitted vehicle fields.
        $userFieldPermissions = $user->userVehicleFieldPermissions;

        if (!empty($userFieldPermissions)) {
            $userFieldPermissions = !empty($userFieldPermissions->vehicle_allowed_fields) ? json_decode($userFieldPermissions->vehicle_allowed_fields, true) : [];

            if (!empty($userFieldPermissions)) {
                $vehicleFields = $vehicle->getFillable();

                $message       = __("*CANCELLED!*") . "\n\n";

                foreach ($vehicleFields as $vehicleField) {
                    if ($vehicleField == "finance_company_id") {
                        $vehicleField = "finance_company";
                    }

                    if (in_array($vehicleField, $userFieldPermissions)) {
                        $field = implode(" ", explode("_", $vehicleField));
                        $field = ucwords($field);
                        $value = !empty($vehicle->{$vehicleField}) ? $vehicle->{$vehicleField} : "-";

                        if ($vehicleField == "finance_company") {
                            $financeCompany = $vehicle->financeCompany()->first();

                            $value          = "-";

                            if (!empty($financeCompany) && !empty($financeCompany->name)) {
                                $value = $financeCompany->name;
                            }

                            $message .= __($field . " : *" . $value) . "*\n";
                        } else {
                            $message .= __($field . " : *" . $value) . "*\n";
                        }
                    }
                }

                $message .= "\n" . __("Agency : *" . env('APP_NAME', 'V.R. Boricha Service') . "*\n" . 
                               "Agency Contact : *" . env('AGENCY_CONTACT', '')) . "*";

                return $message;
            }
        }

        return null;
    }

    public static function messageFormatForConfirmed(Vehicle $vehicle, User $user)
    {
        // Get permitted vehicle fields.
        $userFieldPermissions = $user->userVehicleFieldPermissions;

        if (!empty($userFieldPermissions)) {
            $userFieldPermissions = !empty($userFieldPermissions->vehicle_allowed_fields) ? json_decode($userFieldPermissions->vehicle_allowed_fields, true) : [];

            if (!empty($userFieldPermissions)) {
                $vehicleFields = $vehicle->getFillable();

                $message       = __("*CONFIRMED!*") . "\n\n";

                foreach ($vehicleFields as $vehicleField) {
                    if ($vehicleField == "finance_company_id") {
                        $vehicleField = "finance_company";
                    }

                    if (in_array($vehicleField, $userFieldPermissions)) {
                        $field = implode(" ", explode("_", $vehicleField));
                        $field = ucwords($field);
                        $value = !empty($vehicle->{$vehicleField}) ? $vehicle->{$vehicleField} : "-";

                        if ($vehicleField == "finance_company") {
                            $financeCompany = $vehicle->financeCompany()->first();

                            $value          = "-";

                            if (!empty($financeCompany) && !empty($financeCompany->name)) {
                                $value = $financeCompany->name;
                            }

                            $message .= __($field . " : *" . $value) . "*\n";
                        } else {
                            $message .= __($field . " : *" . $value) . "*\n";
                        }
                    }
                }

                $message .= "\n" . __("Agency : *" . env('APP_NAME', 'V.R. Boricha Service') . "*\n" . 
                               "Agency Contact : *" . env('AGENCY_CONTACT', '')) . "*";

                return $message;
            }
        }

        return null;
    }
}
