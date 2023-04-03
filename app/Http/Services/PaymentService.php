<?php

namespace App\Http\Services;

use App\Models\Payment;

class PaymentService
{
    /**
     * @param array<string, string> $data
     */
    public function create(array $data): Payment
    {
        $payment = new Payment([
            'type' => $data['type'],
        ]);

        $payment = $this->updatePaymentDetailsByType($payment, $data);
        $payment->save();

        return $payment->refresh();
    }

    /**
     * @param array<string, string> $data
     */
    protected function updatePaymentDetailsByType(Payment $payment, array $data): Payment
    {
        $paymentType = $data['type'];

        $payment->details = match ($paymentType) {
            Payment::TYPE_CASH => [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address' => $data['address'],
            ],
            Payment::TYPE_BANK_TRANSFER => [
                'swift' => $data['swift'],
                'iban' => $data['iban'],
                'name' => $data['account_name'],
            ],
            Payment::TYPE_CREDIT_CARD => [
                'number' => $data['card_number'],
                'cvv' => $data['cvv'],
                'expire_date' => $data['expire_date'],
                'holder_name' => $data['holder_name'],
            ],
        };

        return $payment;
    }
}
