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

    public static function messageFormatForCancelled(Vehicle $vehicle)
    {
        return  __("CANCELLED!" . "\n\n" . 
                    "Vehicle Number : " . $vehicle->registration_number . "\n" . 
                    "Chassis Number : " . $vehicle->chassis_number . "\n" . 
                    "Vehicle Maker : " . $vehicle->model . "\n" . 
                    "Engine Number : " . $vehicle->engine_number . "\n" . 
                    "Customer Name : " . $vehicle->customer_name . "\n" . 
                    "Agency : " . env('APP_NAME', 'V.R. Boricha Service') . "\n" . 
                    "Agency Contact : " . env('AGENCY_CONTACT', ''));
    }

    public static function messageFormatForConfirmed(Vehicle $vehicle)
    {
        return  __("CONFIRMED!" . "\n\n" . 
                    "Vehicle Number : " . $vehicle->registration_number . "\n" . 
                    "Chassis Number : " . $vehicle->chassis_number . "\n" . 
                    "Vehicle Maker : " . $vehicle->model . "\n" . 
                    "Engine Number : " . $vehicle->engine_number . "\n" . 
                    "Customer Name : " . $vehicle->customer_name . "\n" . 
                    "Agency : " . env('APP_NAME', 'V.R. Boricha Service') . "\n" . 
                    "Agency Contact : " . env('AGENCY_CONTACT', ''));
    }
}
