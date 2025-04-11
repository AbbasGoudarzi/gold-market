<?php

namespace App\Services;

class CommissionService
{
    protected float $rateUnder1 = 0.02;
    protected float $rate1to10 = 0.015;
    protected float $rateAbove10 = 0.01;

    protected int $minCommission = 500000;
    protected int $maxCommission = 50000000;

    public function calculate(float $quantity, int $totalAmount): array
    {
        $commissionPercent = match (true) {
            $quantity < 1 => $this->rateUnder1,
            ($quantity >= 1 && $quantity < 10) => $this->rate1to10,
            $quantity >= 10 => $this->rateAbove10
        };

        $commissionValue = round($totalAmount * $commissionPercent / 100);
        $commissionValue = min(max($commissionValue, $this->minCommission), $this->maxCommission);
        return [
            'percent' => $commissionPercent,
            'value' => $commissionValue,
        ];
    }
}
