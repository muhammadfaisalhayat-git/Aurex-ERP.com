<?php
require 'vendor/autoload.php';
require 'app/Services/ArabicShaper.php';

$shaper = new App\Services\ArabicShaper();
$word = "الزراعية";
$shaped = $shaper->shape($word);

echo "Word: $word\n";
echo "Shaped (hex): " . bin2hex($shaped) . "\n";

// Expected hex for "الزراعية":
// Characters: ا(0x0627), ل(0x0644), ز(0x0632), ر(0x0631), ا(0x0627), ع(0x0639), ي(0x064A), ة(0x0629)
// Joining:
// ا: Iso (FE8D)
// ل: Beg (FEDF)
// ز: End (FEB0) - Wait, Lam(Beg) can join to Zain(End). Correct.
// ر: Iso (FEAD) - Zain doesn't join to the left. Correct.
// ا: End (FE8E) - Reh(End) can join? No, Reh doesn't join to the left. So Alif is Iso (FE8D).
// ع: Beg (FECB)
// ي: Mid (FEF4)
// ة: End (FE94)
// Reversed: ة ي ع ا ر ز ل ا
// Glyphs: FE94, FEF4, FECB, FE8D, FEAD, FEB0, FEDF, FE8D
