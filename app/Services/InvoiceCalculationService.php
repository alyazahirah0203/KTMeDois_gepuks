<?php

namespace App\Services;

class InvoiceCalculationService
{
    public function calculate($lineTotal, $creditNote = 0, $isLate = false)
    {
        $penalty = 0;
        if ($isLate) {
            $penalty = $lineTotal * 0.01;
        }

        $afterPenalty = $lineTotal - $penalty;
        $afterDiscount = $afterPenalty - $creditNote;
        $tax = $afterDiscount * 0.06;
        $total = $afterDiscount + $tax;

        return [
            'line_total' => round($lineTotal, 2),
            'penalty' => round($penalty, 2),
            'discount' => round($creditNote, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
            'balance_due' => round($total, 2)
        ];
    }

    public function isLateDelivery($deliveryDate, $expectedDate = null)
    {
        if (!$expectedDate) {
            $expectedDate = now()->subDays(30);
        }
        return $deliveryDate > $expectedDate;
    }
}