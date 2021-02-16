<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => ['nullable', 'max:50'],
            'shift' => ['nullable', 'max:50'],
            'station_id' => ['nullable', 'max:50'],
            'cashier_id' => ['nullable', 'max:50'],
            'pump_attendant_id' => ['nullable', 'max:50'],
            'office_staff_id' => ['nullable', 'max:50'],
            //
            'pump_readings.*.pump' => ['nullable', 'max:50'],
            'pump_readings.*.opening' => ['nullable', 'max:250'],
            'pump_readings.*.closing' => ['nullable', 'max:250'],
            'pump_readings.*.unit_price' => ['nullable', 'max:50'],
            //
            'sales.*.pump_no' => ['nullable', 'max:50'],
            'sales.*.dr_no' => ['nullable', 'max:100'],
            'sales.*.client_id' => ['nullable', 'max:50'],
            'sales.*.product_id' => ['required', 'max:50'],
            'sales.*.quantity' => ['required', 'max:50'],
            'sales.*.rs_no' => ['required', 'max:100'],
            //
            'company_vales.*.pump_no' => ['nullable', 'max:50'],
            'company_vales.*.voucher_no' => ['nullable', 'max:100'],
            'company_vales.*.client_id' => ['nullable', 'max:50'],
            'company_vales.*.product_id' => ['required', 'max:50'],
            'company_vales.*.quantity' => ['required', 'max:50'],
            //
            'calibrations.*.pump' => ['nullable', 'max:50'],
            'calibrations.*.quantity' => ['nullable', 'max:100'],
            'calibrations.*.pump_no' => ['nullable', 'max:50'],
            'calibrations.*.voucher_no' => ['required', 'max:100'],
            //
            'discounts.*.voucher_no' => ['nullable', 'max:100'],
            'discounts.*.cash' => ['nullable', 'max:100'],
            'discounts.*.client_id' => ['nullable', 'max:50'],
            'discounts.*.quantity' => ['required', 'max:50'],
            'discounts.*.disc_amount' => ['required', 'max:50'],
        ];
    }
}
