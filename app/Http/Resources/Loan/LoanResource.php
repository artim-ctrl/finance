<?php

namespace App\Http\Resources\Loan;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Loan
 */
class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // TODO: refactor
        $monthsLeft = $this->term - $this->first_payment->diff(now())->m - 1;

        $left = $this->amount / $this->term * $monthsLeft;

        $nextPayment = now()->day($this->first_payment->day);
        if (now()->day >= $this->first_payment->day) {
            $nextPayment->addMonth();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'left' => round($left, 2),
            'months_left' => $monthsLeft,
            'currency' => $this->currency->code,
            'term' => $this->term,
            'first_payment' => $this->first_payment->format('d.m.Y'),
            'next_payment' => $nextPayment->format('d.m.Y'),
            'last_payment' => $this->first_payment->addMonths($this->term - 1)->format('d.m.Y'),
        ];
    }
}
