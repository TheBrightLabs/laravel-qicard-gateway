<?php

namespace Thebrightlabs\IraqPayments\Traits;

trait withQiCardHelpers
{
    // Bismillah
    /**
     * Generate a human-readable description for a payment request.
     */
    protected function buildDescription(?string $description, array $context = []): string
    {
        $base = $description ?: 'No Description.';
        if (empty($context)) {
            return $base;
        }
        // Append simple key=value context pairs for debugging/logging.
        $pairs = [];
        foreach ($context as $k => $v) {
            if (is_scalar($v)) {
                $pairs[] = "$k=$v";
            }
        }
        return $pairs ? $base.' | '.implode(', ', $pairs) : $base;
    }
}
