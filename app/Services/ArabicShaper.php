<?php

namespace App\Services;

class ArabicShaper
{
    /**
     * Arabic Character forms (Isolated, End, Middle, Beginning)
     */
    private $arabicChars = [
        // Char => [Iso, End, Mid, Beg, canJoinLeft, canJoinRight]
        0x0621 => [0xFE80, 0xFE80, 0xFE80, 0xFE80, false, false], // Hamza
        0x0622 => [0xFE81, 0xFE82, 0xFE82, 0xFE81, false, true],  // Alef with Madda
        0x0623 => [0xFE83, 0xFE84, 0xFE84, 0xFE83, false, true],  // Alef with Hamza Above
        0x0624 => [0xFE85, 0xFE86, 0xFE86, 0xFE85, false, true],  // Waw with Hamza Above
        0x0625 => [0xFE87, 0xFE88, 0xFE88, 0xFE87, false, true],  // Alef with Hamza Below
        0x0626 => [0xFE89, 0xFE8A, 0xFE8B, 0xFE8C, true, true],   // Yeh with Hamza Above
        0x0627 => [0xFE8D, 0xFE8E, 0xFE8E, 0xFE8D, false, true],  // Alef
        0x0628 => [0xFE8F, 0xFE90, 0xFE92, 0xFE91, true, true],   // Beh
        0x0629 => [0xFE93, 0xFE94, 0xFE94, 0xFE93, false, true],  // Teh Marbuta
        0x062A => [0xFE95, 0xFE96, 0xFE98, 0xFE97, true, true],   // Teh
        0x062B => [0xFE99, 0xFE9A, 0xFE9C, 0xFE9B, true, true],   // Theh
        0x062C => [0xFE9D, 0xFE9E, 0xFEA0, 0xFE9F, true, true],   // Jeem
        0x062D => [0xFEA1, 0xFEA2, 0xFEA4, 0xFEA3, true, true],   // Hah
        0x062E => [0xFEA5, 0xFEA6, 0xFEA8, 0xFEA7, true, true],   // Khah
        0x062F => [0xFEA9, 0xFEAA, 0xFEAA, 0xFEA9, false, true],  // Dal
        0x0630 => [0xFEAB, 0xFEAC, 0xFEAC, 0xFEAB, false, true],  // Thal
        0x0631 => [0xFEAD, 0xFEAE, 0xFEAE, 0xFEAD, false, true],  // Reh
        0x0632 => [0xFEAF, 0xFEB0, 0xFEB0, 0xFEAF, false, true],  // Zain
        0x0633 => [0xFEB1, 0xFEB2, 0xFEB4, 0xFEB3, true, true],   // Seen
        0x0634 => [0xFEB5, 0xFEB6, 0xFEB8, 0xFEB7, true, true],   // Sheen
        0x0635 => [0xFEB9, 0xFEBA, 0xFEBC, 0xFEBB, true, true],   // Sad
        0x0636 => [0xFEBD, 0xFEBE, 0xFEC0, 0xFEBF, true, true],   // Dad
        0x0637 => [0xFEC1, 0xFEC2, 0xFEC4, 0xFEC3, true, true],   // Tah
        0x0638 => [0xFEC5, 0xFEC6, 0xFEC8, 0xFEC7, true, true],   // Zah
        0x0639 => [0xFEC9, 0xFECA, 0xFECC, 0xFECB, true, true],   // Ain
        0x063A => [0xFECD, 0xFECE, 0xFED0, 0xFECF, true, true],   // Ghain
        0x0641 => [0xFED1, 0xFED2, 0xFED4, 0xFED3, true, true],   // Feh
        0x0642 => [0xFED5, 0xFED6, 0xFED8, 0xFED7, true, true],   // Qaf
        0x0643 => [0xFED9, 0xFEDA, 0xFEDC, 0xFEDB, true, true],   // Kaf
        0x0644 => [0xFEDD, 0xFEDE, 0xFEE0, 0xFEDF, true, true],   // Lam
        0x0645 => [0xFEE1, 0xFEE2, 0xFEE4, 0xFEE3, true, true],   // Meem
        0x0646 => [0xFEE5, 0xFEE6, 0xFEE8, 0xFEE7, true, true],   // Noon
        0x0647 => [0xFEE9, 0xFEEA, 0xFEEC, 0xFEEB, true, true],   // Heh
        0x0648 => [0xFEED, 0xFEEE, 0xFEEE, 0xFEED, false, true],  // Waw
        0x0649 => [0xFEEF, 0xFEF0, 0xFEF0, 0xFEEF, false, true],  // Alef Maksura
        0x064A => [0xFEF1, 0xFEF2, 0xFEF4, 0xFEF3, true, true],   // Yeh
    ];

    /**
     * Shape Arabic text for PDF rendering.
     * This handles joining and right-to-left reversal.
     */
    public function shape($text)
    {
        if (empty($text))
            return $text;

        $chars = $this->utf8ToUnicodes($text);
        $shapedChars = [];
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $current = $chars[$i];

            if (!isset($this->arabicChars[$current])) {
                $shapedChars[] = $current;
                continue;
            }

            $prev = ($i > 0) ? $chars[$i - 1] : null;
            $next = ($i < $count - 1) ? $chars[$i + 1] : null;

            $canJoinLeft = $this->arabicChars[$current][4];
            $canJoinRight = $this->arabicChars[$current][5];

            $joinPrev = $prev && isset($this->arabicChars[$prev]) && $this->arabicChars[$prev][4] && $canJoinRight;
            $joinNext = $next && isset($this->arabicChars[$next]) && $this->arabicChars[$next][5] && $canJoinLeft;

            if ($joinPrev && $joinNext) {
                $shapedChars[] = $this->arabicChars[$current][2]; // Mid
            } elseif ($joinPrev) {
                $shapedChars[] = $this->arabicChars[$current][1]; // End
            } elseif ($joinNext) {
                $shapedChars[] = $this->arabicChars[$current][3]; // Beg
            } else {
                $shapedChars[] = $this->arabicChars[$current][0]; // Iso
            }
        }

        // Handle Lam-Alef ligatures
        $ligatured = [];
        for ($i = 0; $i < count($shapedChars); $i++) {
            if ($i < count($shapedChars) - 1 && $chars[$i] == 0x0644) {
                $nextChar = $chars[$i + 1];
                $ligature = null;

                if ($nextChar == 0x0622)
                    $ligature = [0xFEF5, 0xFEF6]; // Madda
                elseif ($nextChar == 0x0623)
                    $ligature = [0xFEF7, 0xFEF8]; // Hamza Above
                elseif ($nextChar == 0x0625)
                    $ligature = [0xFEF9, 0xFEFA]; // Hamza Below
                elseif ($nextChar == 0x0627)
                    $ligature = [0xFEFB, 0xFEFC]; // Plain

                if ($ligature) {
                    $joinPrev = ($i > 0) && isset($this->arabicChars[$chars[$i - 1]]) && $this->arabicChars[$chars[$i - 1]][4] && $this->arabicChars[0x0644][5];
                    $ligatured[] = $joinPrev ? $ligature[1] : $ligature[0];
                    $i++;
                    continue;
                }
            }
            $ligatured[] = $shapedChars[$i];
        }

        return $this->unicodesToUtf8($this->reverseArabicChunks($ligatured));
    }

    private function utf8ToUnicodes($str)
    {
        $unicodes = [];
        $values = [];
        $lookingFor = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);
            if ($thisValue < 128)
                $unicodes[] = $thisValue;
            else {
                if (count($values) == 0)
                    $lookingFor = ($thisValue < 224) ? 2 : 3;
                $values[] = $thisValue;
                if (count($values) == $lookingFor) {
                    $number = ($lookingFor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
                        (($values[0] % 32) * 64) + ($values[1] % 64);
                    $unicodes[] = $number;
                    $values = [];
                    $lookingFor = 0;
                }
            }
        }
        return $unicodes;
    }

    private function unicodesToUtf8($unicodes)
    {
        $utf8 = '';
        foreach ($unicodes as $unicode) {
            if ($unicode < 128)
                $utf8 .= chr($unicode);
            elseif ($unicode < 2048) {
                $utf8 .= chr(192 + (($unicode - ($unicode % 64)) / 64));
                $utf8 .= chr(128 + ($unicode % 64));
            } else {
                $utf8 .= chr(224 + (($unicode - ($unicode % 4096)) / 4096));
                $utf8 .= chr(128 + ((($unicode % 4096) - ($unicode % 64)) / 64));
                $utf8 .= chr(128 + ($unicode % 64));
            }
        }
        return $utf8;
    }

    private function reverseArabicChunks($unicodes)
    {
        $result = [];
        $chunk = [];
        foreach ($unicodes as $u) {
            if (($u >= 0x0600 && $u <= 0x06FF) || ($u >= 0xFB50 && $u <= 0xFEFF)) {
                $chunk[] = $u;
            } else {
                if (!empty($chunk)) {
                    $result = array_merge($result, array_reverse($chunk));
                    $chunk = [];
                }
                $result[] = $u;
            }
        }
        if (!empty($chunk)) {
            $result = array_merge($result, array_reverse($chunk));
        }
        return $result;
    }
}
