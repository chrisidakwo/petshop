<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $paymentType = $this->faker->randomElement(Payment::getPaymentTypes());

        return [
            'type' => $paymentType,
            'details' => $this->getDetailsForPaymentType($paymentType),
        ];
    }

    public function getDetailsForPaymentType(string $type): array
    {
        return match($type) {
            Payment::TYPE_CREDIT_CARD => [
                'holder_name' => $this->faker->name,
                'number' => $this->faker->creditCardNumber(),
                'ccv' => $this->faker->randomNumber(3),
                'expire_date' => $this->faker->creditCardExpirationDateString(),
            ],

            Payment::TYPE_CASH => [
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'address' => $this->faker->streetAddress(),
            ],

            Payment::TYPE_BANK_TRANSFER => [
                'swift' => $this->faker->swiftBicNumber(),
                'iban' => $this->faker->iban('NGA'),
                'name' => $this->faker->name(),
            ],
        };
    }
}
