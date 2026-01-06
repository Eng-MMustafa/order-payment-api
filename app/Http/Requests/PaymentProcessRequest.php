<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,stripe',
            'card_number' => 'required_if:payment_method,credit_card',
            'card_expiry_month' => 'required_if:payment_method,credit_card',
            'card_expiry_year' => 'required_if:payment_method,credit_card',
            'card_cvv' => 'required_if:payment_method,credit_card',
            'paypal_email' => 'required_if:payment_method,paypal',
            'account_number' => 'required_if:payment_method,bank_transfer',
            'stripe_token' => 'required_if:payment_method,stripe',
        ];
    }
}

