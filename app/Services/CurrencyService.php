<?php

namespace App\Services;

class CurrencyService
{
    // ─── English Words ────────────────────────────────────────────────────────
    private array $ones_en = [
        '',
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
        'Seven',
        'Eight',
        'Nine',
        'Ten',
        'Eleven',
        'Twelve',
        'Thirteen',
        'Fourteen',
        'Fifteen',
        'Sixteen',
        'Seventeen',
        'Eighteen',
        'Nineteen',
    ];
    private array $tens_en = [
        '',
        '',
        'Twenty',
        'Thirty',
        'Forty',
        'Fifty',
        'Sixty',
        'Seventy',
        'Eighty',
        'Ninety',
    ];

    // ─── Arabic Words ─────────────────────────────────────────────────────────
    private array $ones_ar = [
        '',
        'واحد',
        'اثنان',
        'ثلاثة',
        'أربعة',
        'خمسة',
        'ستة',
        'سبعة',
        'ثمانية',
        'تسعة',
        'عشرة',
        'أحد عشر',
        'اثنا عشر',
        'ثلاثة عشر',
        'أربعة عشر',
        'خمسة عشر',
        'ستة عشر',
        'سبعة عشر',
        'ثمانية عشر',
        'تسعة عشر',
    ];
    private array $tens_ar = [
        '',
        '',
        'عشرون',
        'ثلاثون',
        'أربعون',
        'خمسون',
        'ستون',
        'سبعون',
        'ثمانون',
        'تسعون',
    ];
    private array $hundreds_ar = [
        '',
        'مائة',
        'مئتان',
        'ثلاثمائة',
        'أربعمائة',
        'خمسمائة',
        'ستمائة',
        'سبعمائة',
        'ثمانمائة',
        'تسعمائة',
    ];
    private array $scales_ar = [
        '',
        'ألف',
        'مليون',
        'مليار',
        'تريليون',
    ];
    private array $scales_ar_pl = [
        '',
        'آلاف',
        'ملايين',
        'مليارات',
        'تريليونات',
    ];

    /**
     * Convert an amount to words in the given language.
     */
    public function amountToWords(float $amount, string $currency = 'SAR', string $lang = 'en'): string
    {
        $amount = round($amount, 2);
        $integer = (int) floor($amount);
        $decimal = (int) round(($amount - $integer) * 100);

        if ($lang === 'ar') {
            return $this->formatArabic($integer, $decimal, $currency);
        }

        return $this->formatEnglish($integer, $decimal, $currency);
    }

    // ─── English ──────────────────────────────────────────────────────────────

    private function formatEnglish(int $integer, int $decimal, string $currency): string
    {
        $currencyName = $this->currencyNameEn($currency);
        $subName = $this->subUnitNameEn($currency);

        if ($integer === 0 && $decimal === 0) {
            return "Zero {$currencyName}s Only";
        }

        $parts = [];
        if ($integer > 0) {
            $parts[] = $this->intToWordsEn($integer) . ' ' . $currencyName . ($integer > 1 ? 's' : '');
        }
        if ($decimal > 0) {
            $parts[] = $this->intToWordsEn($decimal) . ' ' . $subName . ($decimal > 1 ? 's' : '');
        }

        return implode(' and ', $parts) . ' Only';
    }

    private function intToWordsEn(int $n): string
    {
        if ($n === 0)
            return 'Zero';
        if ($n < 0)
            return 'Minus ' . $this->intToWordsEn(-$n);

        $chunks = [];
        $scales = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
        $i = 0;

        while ($n > 0) {
            $chunk = $n % 1000;
            if ($chunk !== 0) {
                $word = $this->chunkToWordsEn($chunk);
                $chunks[] = $scales[$i] ? $word . ' ' . $scales[$i] : $word;
            }
            $n = intdiv($n, 1000);
            $i++;
        }

        return implode(' ', array_reverse($chunks));
    }

    private function chunkToWordsEn(int $n): string
    {
        $parts = [];
        if ($n >= 100) {
            $parts[] = $this->ones_en[intdiv($n, 100)] . ' Hundred';
            $n %= 100;
        }
        if ($n >= 20) {
            $tens = $this->tens_en[intdiv($n, 10)];
            $one = $this->ones_en[$n % 10];
            $parts[] = $one ? $tens . '-' . $one : $tens;
        } elseif ($n > 0) {
            $parts[] = $this->ones_en[$n];
        }
        return implode(' and ', $parts);
    }

    // ─── Arabic ───────────────────────────────────────────────────────────────

    private function formatArabic(int $integer, int $decimal, string $currency): string
    {
        $currencyName = $this->currencyNameAr($currency);
        $subName = $this->subUnitNameAr($currency);

        if ($integer === 0 && $decimal === 0) {
            return "صفر {$currencyName} فقط";
        }

        $parts = [];
        if ($integer > 0) {
            $parts[] = $this->intToWordsAr($integer) . ' ' . $currencyName;
        }
        if ($decimal > 0) {
            $parts[] = $this->intToWordsAr($decimal) . ' ' . $subName;
        }

        return implode(' و', $parts) . ' فقط';
    }

    private function intToWordsAr(int $n): string
    {
        if ($n === 0)
            return 'صفر';
        if ($n < 0)
            return 'سالب ' . $this->intToWordsAr(-$n);

        $result = [];
        $scaleIdx = 0;

        // Split into groups of 1000
        $groups = [];
        $tmp = $n;
        while ($tmp > 0) {
            $groups[] = $tmp % 1000;
            $tmp = intdiv($tmp, 1000);
        }

        $groups = array_reverse($groups);
        $count = count($groups);

        foreach ($groups as $i => $chunk) {
            if ($chunk === 0)
                continue;

            $scaleIdx = $count - 1 - $i;
            $chunkWord = $this->chunkToWordsAr($chunk);

            if ($scaleIdx === 0) {
                $result[] = $chunkWord;
            } elseif ($scaleIdx === 1) {
                // Thousands
                if ($chunk === 1) {
                    $result[] = 'ألف';
                } elseif ($chunk === 2) {
                    $result[] = 'ألفان';
                } elseif ($chunk >= 3 && $chunk <= 10) {
                    $result[] = $chunkWord . ' ' . $this->scales_ar_pl[$scaleIdx];
                } else {
                    $result[] = $chunkWord . ' ' . $this->scales_ar[$scaleIdx];
                }
            } elseif ($scaleIdx === 2) {
                // Millions
                if ($chunk === 1) {
                    $result[] = 'مليون';
                } elseif ($chunk === 2) {
                    $result[] = 'مليونان';
                } elseif ($chunk >= 3 && $chunk <= 10) {
                    $result[] = $chunkWord . ' ' . $this->scales_ar_pl[$scaleIdx];
                } else {
                    $result[] = $chunkWord . ' ' . $this->scales_ar[$scaleIdx];
                }
            } elseif ($scaleIdx === 3) {
                // Billions
                if ($chunk === 1) {
                    $result[] = 'مليار';
                } elseif ($chunk === 2) {
                    $result[] = 'ملياران';
                } elseif ($chunk >= 3 && $chunk <= 10) {
                    $result[] = $chunkWord . ' ' . $this->scales_ar_pl[$scaleIdx];
                } else {
                    $result[] = $chunkWord . ' ' . $this->scales_ar[$scaleIdx];
                }
            }
        }

        return implode(' و', $result);
    }

    private function chunkToWordsAr(int $n): string
    {
        $parts = [];

        if ($n >= 100) {
            $parts[] = $this->hundreds_ar[intdiv($n, 100)];
            $n %= 100;
        }

        if ($n >= 20) {
            $tens = $this->tens_ar[intdiv($n, 10)];
            $one = $n % 10;
            if ($one > 0) {
                $parts[] = $this->ones_ar[$one] . ' و' . $tens;
            } else {
                $parts[] = $tens;
            }
        } elseif ($n > 0) {
            $parts[] = $this->ones_ar[$n];
        }

        return implode(' و', $parts);
    }

    // ─── Currency Names ───────────────────────────────────────────────────────

    private function currencyNameEn(string $code): string
    {
        return match (strtoupper($code)) {
            'SAR' => 'Riyal',
            'USD' => 'Dollar',
            'EUR' => 'Euro',
            'GBP' => 'Pound',
            'AED' => 'Dirham',
            'KWD' => 'Dinar',
            default => 'Riyal',
        };
    }

    private function subUnitNameEn(string $code): string
    {
        return match (strtoupper($code)) {
            'SAR' => 'Halala',
            'USD' => 'Cent',
            'EUR' => 'Cent',
            'GBP' => 'Penny',
            'AED' => 'Fils',
            'KWD' => 'Fils',
            default => 'Halala',
        };
    }

    private function currencyNameAr(string $code): string
    {
        return match (strtoupper($code)) {
            'SAR' => 'ريال سعودي',
            'USD' => 'دولار أمريكي',
            'EUR' => 'يورو',
            'GBP' => 'جنيه إسترليني',
            'AED' => 'درهم إماراتي',
            'KWD' => 'دينار كويتي',
            default => 'ريال سعودي',
        };
    }

    private function subUnitNameAr(string $code): string
    {
        return match (strtoupper($code)) {
            'SAR' => 'هللة',
            'USD' => 'سنت',
            'EUR' => 'سنت',
            'GBP' => 'بنس',
            'AED' => 'فلس',
            'KWD' => 'فلس',
            default => 'هللة',
        };
    }
}
