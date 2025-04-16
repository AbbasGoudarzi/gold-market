<?php

namespace App\Services;

class FeeService
{
    protected float $rateUnder1 = 2;
    protected float $rate1to10 = 1.5;
    protected float $rateAbove10 = 1;

    protected int $minFee = 50000; // In Tomans
    protected int $maxFee = 5000000;

    public function calculateFeePercent(float $quantity): float
    {
        return match (true) {
            $quantity < 1 => $this->rateUnder1,
            ($quantity >= 1 && $quantity < 10) => $this->rate1to10,
            $quantity >= 10 => $this->rateAbove10
        };
    }

    public function calculateFeeValue(float $feePercent, int $totalAmount): int
    {
        $feeValue = round($totalAmount * $feePercent / 100);
        return floor(min(max($feeValue, $this->minFee), $this->maxFee));
    }
}
