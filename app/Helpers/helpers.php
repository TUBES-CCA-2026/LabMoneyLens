<?php

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi nominal Rupiah, contoh: "Rp 10.130.000".
     * Tidak pernah menambahkan prefix "+" atau "-".
     *
     * @param  int|float|string|null  $amount
     */
    function rupiah($amount): string
    {
        $value = (float) ($amount ?? 0);

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}