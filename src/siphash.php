<?php
namespace duzun;

class SipHash {
    const VERSION = '0.0.1';

    const UINT32_MASK = 0xFFFFFFFF;

    public static function hash_2_4($key, $str, $raw=false) {
        $key = str_pad($key, 16, "\x0", STR_PAD_RIGHT);
        $k = unpack('V4', $key);
        $v2 = [ 'h' => $k[2], 'l' => $k[1] ];
        $v3 = [ 'h' => $k[4], 'l' => $k[3] ];
        $v0 = [ 'h' => $v2['h'], 'l' => $v2['l'] ];
        $v1 = [ 'h' => $v3['h'], 'l' => $v3['l'] ];
        $ml = strlen($str); $ml7 = $ml - 7;
        $buf = [];

        self::_xor($v0, [ 'h' => 0x736f6d65, 'l' => 0x70736575 ]);
        self::_xor($v1, [ 'h' => 0x646f7261, 'l' => 0x6e646f6d ]);
        self::_xor($v2, [ 'h' => 0x6c796765, 'l' => 0x6e657261 ]);
        self::_xor($v3, [ 'h' => 0x74656462, 'l' => 0x79746573 ]);

        $mp = 0;
        while ($mp < $ml7) {
            $mi = [ 'h' => self::_get_int($str, $mp + 4), 'l' => self::_get_int($str, $mp) ];
            self::_xor($v3, $mi);
            self::_compress($v0, $v1, $v2, $v3);
            self::_compress($v0, $v1, $v2, $v3);
            self::_xor($v0, $mi);
            $mp += 8;
        }
        $buf[7] = $ml;
        $ic = 0;
        while ($mp < $ml) {
            $buf[$ic++] = ord(substr($str, $mp++));
        }
        while ($ic < 7) {
            $buf[$ic++] = 0;
        }
        $mi = [ 'h' => $buf[7] << 24 | $buf[6] << 16 | $buf[5] << 8 | $buf[4],
                'l' => $buf[3] << 24 | $buf[2] << 16 | $buf[1] << 8 | $buf[0] ];
        self::_xor($v3, $mi);
        self::_compress($v0, $v1, $v2, $v3);
        self::_compress($v0, $v1, $v2, $v3);
        self::_xor($v0, $mi);
        self::_xor($v2, [ 'h' => 0, 'l' => 0xff ]);
        self::_compress($v0, $v1, $v2, $v3);
        self::_compress($v0, $v1, $v2, $v3);
        self::_compress($v0, $v1, $v2, $v3);
        self::_compress($v0, $v1, $v2, $v3);

        $h = $v0;
        self::_xor($h, $v1);
        self::_xor($h, $v2);
        self::_xor($h, $v3);


        $h = pack('N', $h['h']) . pack('N', $h['l']);
        return $raw ? $h : bin2hex($h);
    }


    protected static function _add(&$a, $b) {
        $rl = $a['l'] + $b['l'];
        $a2 = [
            'h' => $a['h'] + $b['h'] + ($rl / 2 >> 31) & self::UINT32_MASK,
            'l' => $rl & self::UINT32_MASK
        ];
        $a['h'] = $a2['h'];
        $a['l'] = $a2['l'];
    }

    protected static function _xor(&$a, $b) {
        $a['h'] ^= $b['h']; $a['h'] &= self::UINT32_MASK;
        $a['l'] ^= $b['l']; $a['l'] &= self::UINT32_MASK;
    }

    protected static function _rotl(&$a, $n) {
        $a2 = [
            'h' => ($a['h'] << $n | $a['l'] >> (32 - $n)) & self::UINT32_MASK,
            'l' => ($a['l'] << $n | $a['h'] >> (32 - $n)) & self::UINT32_MASK
        ];
        $a['h'] = $a2['h']; $a['l'] = $a2['l'];
    }

    protected static function _rotl32(&$a) {
        $al = $a['l'];
        $a['l'] = $a['h']; $a['h'] = $al;
    }

    protected static function _compress(&$v0, &$v1, &$v2, &$v3) {
        self::_add($v0, $v1);
        self::_add($v2, $v3);

        self::_rotl($v1, 13);
        self::_rotl($v3, 16);
        self::_xor($v1, $v0);
        self::_xor($v3, $v2);
        self::_rotl32($v0);

        self::_add($v2, $v1);
        self::_add($v0, $v3);
        self::_rotl($v1, 17);
        self::_rotl($v3, 21);
        self::_xor($v1, $v2);
        self::_xor($v3, $v0);
        self::_rotl32($v2);
    }

    protected static function _get_int($a, $offset) {
        $r = unpack('V', substr($a, $offset, 4));
        return reset($r);
    }
}

function siphash_2_4($key, $str, $raw=false) {
    SipHash::hash($key, $str, $raw);
}

?>
