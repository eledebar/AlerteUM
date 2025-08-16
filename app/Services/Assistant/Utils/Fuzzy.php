<?php
namespace App\Services\Assistant\Utils;

class Fuzzy
{
    public static function jaroWinkler(string $s1, string $s2): float
    {
        $s1 = mb_strtolower($s1, 'UTF-8');
        $s2 = mb_strtolower($s2, 'UTF-8');
        $mt = self::matches($s1, $s2);
        if ($mt['m'] == 0) return 0.0;
        $m = $mt['m']; $t = $mt['t']; $l = $mt['l'];
        $j = (($m / mb_strlen($s1)) + ($m / mb_strlen($s2)) + (($m - $t) / $m)) / 3.0;
        $p = 0.1;
        return $j + ($l * $p * (1 - $j));
    }

    protected static function matches(string $s1, string $s2): array
    {
        $mrange = max(0, (int)floor(max(mb_strlen($s1), mb_strlen($s2)) / 2) - 1);
        $match1 = []; $match2 = [];
        $m = 0; $t = 0; $l = 0;
        for ($i=0; $i<mb_strlen($s1); $i++) {
            $start = max(0, $i - $mrange);
            $end = min($i + $mrange + 1, mb_strlen($s2));
            for ($j=$start; $j<$end; $j++) {
                if (isset($match2[$j])) continue;
                if (mb_substr($s1, $i, 1) === mb_substr($s2, $j, 1)) {
                    $match1[$i] = true; $match2[$j] = true; $m++; break;
                }
            }
        }
        if ($m == 0) return ['m'=>0,'t'=>0,'l'=>0];
        $k = 0;
        for ($i=0; $i<mb_strlen($s1); $i++) {
            if (!isset($match1[$i])) continue;
            while (!isset($match2[$k])) $k++;
            if (mb_substr($s1, $i, 1) !== mb_substr($s2, $k, 1)) $t++;
            $k++;
        }
        $t = $t / 2;
        for ($i=0; $i<min(4, min(mb_strlen($s1), mb_strlen($s2))); $i++) {
            if (mb_substr($s1, $i, 1) === mb_substr($s2, $i, 1)) $l++; else break;
        }
        return ['m'=>$m,'t'=>$t,'l'=>$l];
    }

    public static function tokenScore(string $a, string $b): float
    {
        $ta = preg_split('/\s+/', mb_strtolower($a, 'UTF-8'));
        $tb = preg_split('/\s+/', mb_strtolower($b, 'UTF-8'));
        $seta = array_unique($ta); $setb = array_unique($tb);
        $inter = array_intersect($seta, $setb);
        $union = array_unique(array_merge($seta, $setb));
        if (count($union) == 0) return 0.0;
        return count($inter) / count($union);
    }

    public static function best(string $needle, array $candidates): array
    {
        $best = ['', 0.0];
        foreach ($candidates as $c) {
            $score = max(self::jaroWinkler($needle, $c), self::tokenScore($needle, $c));
            if ($score > $best[1]) $best = [$c, $score];
        }
        return $best;
    }
}
