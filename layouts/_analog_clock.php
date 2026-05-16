<?php
/**
 * Reusable analog clock SVG (centered viewBox, hand rotation via group transform).
 * Caller can pass $clockSize (default w-40 h-40).
 *
 * Hidden if admin set show_analog = 0.
 */
if ((string)mc_setting('show_analog', '1') === '0') return;

$clockSize = $clockSize ?? 'w-40 h-40';
?>
<svg id="analog" viewBox="-100 -100 200 200" class="<?= $clockSize ?> drop-shadow-[0_10px_30px_rgba(0,0,0,0.7)]">
    <defs>
        <radialGradient id="face" cx="0" cy="-30" r="100" gradientUnits="userSpaceOnUse">
            <stop offset="0%"  stop-color="#ffffff"/>
            <stop offset="60%" stop-color="#f1f5f9"/>
            <stop offset="100%" stop-color="#cbd5e1"/>
        </radialGradient>
        <linearGradient id="bezel" x1="0" y1="-1" x2="0" y2="1">
            <stop offset="0%"  stop-color="var(--accent)"/>
            <stop offset="100%" stop-color="var(--primary)"/>
        </linearGradient>
        <filter id="handShadow" x="-50%" y="-50%" width="200%" height="200%">
            <feDropShadow dx="0" dy="2" stdDeviation="1.2" flood-opacity="0.4"/>
        </filter>
    </defs>

    <circle cx="0" cy="0" r="98" fill="url(#bezel)"/>
    <circle cx="0" cy="0" r="92" fill="var(--primary)"/>
    <circle cx="0" cy="0" r="86" fill="url(#face)"/>

    <!-- minute ticks -->
    <g stroke="#0f172a" stroke-linecap="round">
        <?php for ($i=0; $i<60; $i++):
            $angle = $i * 6;
            $isHour = $i % 5 === 0;
            $r1 = $isHour ? 75 : 80;
            $r2 = 84;
            $sw = $isHour ? 2.4 : 0.7;
            $rad = deg2rad($angle - 90);
            $x1 = $r1 * cos($rad);  $y1 = $r1 * sin($rad);
            $x2 = $r2 * cos($rad);  $y2 = $r2 * sin($rad);
        ?>
        <line x1="<?= number_format($x1,2) ?>" y1="<?= number_format($y1,2) ?>"
              x2="<?= number_format($x2,2) ?>" y2="<?= number_format($y2,2) ?>"
              stroke-width="<?= $sw ?>"/>
        <?php endfor; ?>
    </g>

    <!-- numerals -->
    <g fill="#0f172a" font-family="Inter, sans-serif" font-weight="800" font-size="13"
       text-anchor="middle" dominant-baseline="central">
        <?php for ($n=1; $n<=12; $n++):
            $a = $n * 30;
            $rad = deg2rad($a - 90);
            $x = 65 * cos($rad);  $y = 65 * sin($rad);
        ?>
        <text x="<?= number_format($x,2) ?>" y="<?= number_format($y,2) ?>"><?= $n ?></text>
        <?php endfor; ?>
    </g>

    <circle cx="0" cy="0" r="32" fill="none" stroke="var(--accent)" stroke-width="0.6" opacity="0.5"/>

    <!-- Hands (drawn pointing UP from origin; rotation via group transform) -->
    <g filter="url(#handShadow)">
        <g id="handH" transform="rotate(0)">
            <rect x="-2" y="-50" width="4" height="56" rx="2" fill="#0f172a"/>
        </g>
        <g id="handM" transform="rotate(0)">
            <rect x="-1.5" y="-72" width="3" height="78" rx="1.5" fill="#0f172a"/>
        </g>
        <g id="handS" transform="rotate(0)">
            <rect x="-0.6" y="-78" width="1.2" height="92" fill="#dc2626"/>
            <circle cx="0" cy="-50" r="3" fill="none" stroke="#dc2626" stroke-width="1.2"/>
        </g>
    </g>

    <circle cx="0" cy="0" r="4" fill="#0f172a"/>
    <circle cx="0" cy="0" r="1.6" fill="#dc2626"/>
</svg>
