<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Payment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'type' => ['required', 'string', Rule::in(Payment::getPaymentTypes())],
        ], $this->getPaymentTypeValidation($this->get('type', '')));
    }

    /**
     * @return array<string, array<string>>
     */
    protected function getPaymentTypeValidation(string $paymentType): array
    {
        return match ($paymentType) {
            Payment::TYPE_CREDIT_CARD => [
                'holder_name' => ['required', 'string'],
                // Credit card numbers typically use 12 - 19 digits
                'card_number' => ['required', 'min:12', 'max:19'],
                'ccv' => ['required', 'numeric', 'size:3'],
                'expire_date' => ['required', 'date', 'date_format:Y-m-d'],
            ],
            Payment::TYPE_BANK_TRANSFER => [
                // Swift code typically contains 8 - 11 characters
                'swift' => ['required', 'string', 'min:8', 'max:11'],
                // IBAN typically ranges between 16 - 34 characters
                'iban' => ['required', 'string', 'min:16', 'max:34'],
                'account_name' => ['required', 'string', 'min:2'],
            ],
            Payment::TYPE_CASH => [
                'first_name' => ['required', 'string', 'min:2'],
                'last_name' => ['required', 'string', 'min:2'],
                'address' => ['required', 'string'],
            ],
            default => [],
        };
    }
}
