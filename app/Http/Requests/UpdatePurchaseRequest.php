<?php

namespace App\Http\Requests;
use App\Models\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update purchase', Purchase::class, $this->purchase);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Purchase
            'date' => ['required', 'max:50'],
            'purchase_no' => [
                'required',
                'max:150',
                'unique:purchases,purchase_no,'.$this->id
            ],
            'supplier_id' => ['nullable', 'max:50'],
            'depot_id' => ['nullable', 'max:50'],
            'account_id' => ['nullable', 'max:50'],
            'monthly_mindoro_transaction_id' => ['nullable', 'max:50'],
            'monthly_batangas_transaction_id' => ['nullable', 'max:50'],

            // PurchaseDetail
            'details.*.quantity' => ['nullable', 'max:50'],
            'details.*.unit_price' => ['nullable', 'max:50'],
            'details.*.remarks' => ['nullable', 'max:100'],
            'details.*.product.id' => ['required', 'max:50'],

            // TankerLoad - Batangas
            'batangasLoads.*.batangas_transaction_id' => ['nullable', 'max:50'],
            'batangasLoads.*.remarks' => ['nullable', 'max:200'],

            // TankerLoadDetail - Batangas
            'batangasLoads.*.details.*.product.id' => ['required', 'max:50'],
            'batangasLoads.*.details.*.quantity' => ['nullable', 'max:50'],
            'batangasLoads.*.details.*.unit_price' => ['nullable', 'max:50'],

            // TankerLoad - Mindoro
            'mindoroLoads.*.mindoro_transaction_id' => ['nullable', 'max:50'],
            'mindoroLoads.*.remarks' => ['nullable', 'max:200'],

            // TankerLoadDetail - Mindoro
            'mindoroLoads.*.details.*.product.id' => ['required', 'max:50'],
            'mindoroLoads.*.details.*.quantity' => ['nullable', 'max:50'],
            'mindoroLoads.*.details.*.unit_price' => ['nullable', 'max:50'],

        ];
    }

    public function messages()
    {
        return [
            // Purchase
            'date.required' => 'Date is required',
            'purchase_no.required' => 'Purchase no. is required',

            //Purchase Detail
            'details.*.product_id.required' => 'Product is required',

            // TankerLoad - Batangas
            'batangasLoads.*.batangas_transaction_id.required' => 'Batangas trip no. is required',
            // TankerLoadDetail - Batangas
            'batangasLoads.*.details.*.product.id.required' => 'Product is required',

            // TankerLoad - Mindoro
            'mindoroLoads.*.mindoro_transaction_id.required' => 'Mindoro trip no. is required',
            // TankerLoadDetail - Mindoro
            'mindoroLoads.*.details.*.product.id.required' => 'Product is required',
        ];
    }
}
