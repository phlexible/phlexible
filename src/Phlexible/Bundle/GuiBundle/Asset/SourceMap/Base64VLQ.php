<?php

namespace Phlexible\Bundle\GuiBundle\Asset\SourceMap;

/**
 * Encode / Decode Base64 VLQ.
 *
 * @author bspot
 */
class Base64VLQ {

    const SHIFT = 5;
    const MASK = 0x1F; // == (1 << SHIFT) == 0b00011111
    const CONTINUATION_BIT = 0x20; // == (MASK - 1 ) == 0b00100000

    private $charToInt = array();
    private $intToChar = array();

    public function __construct()
    {
        foreach (str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/') as $i => $char) {
            $this->charToInt[$char] = $i;
            $this->intToChar[$i] = $char;
        }
    }

    /**
     * Return the base 64 VLQ encoded value.
     *
     * @param int $aValue
     *
     * @return string
     */
    public function encode($aValue)
    {
        $encoded = "";

        $vlq = $this->toVLQSigned($aValue);

        do {
            $digit = $vlq & self::MASK;
            $vlq = $this->zeroFill($vlq, self::SHIFT);
            if ($vlq > 0) {
                $digit |= self::CONTINUATION_BIT;
            }
            $encoded .= $this->base64Encode($digit);
        } while ($vlq > 0);

        return $encoded;
    }

    /**
     * Return the value decoded from base 64 VLQ.
     *
     * @param string $encoded
     *
     * @return int
     */
    public function decode($encoded)
    {
        $vlq = 0;

        $i = 0;
        do {
            $digit = $this->base64Decode($encoded[$i]);
            $vlq |= ($digit & self::MASK) << ($i*self::SHIFT);
            $i++;
        } while ($digit & self::CONTINUATION_BIT);

        return $this->fromVLQSigned($vlq);
    }

    /**
     * Convert from a two-complement value to a value where the sign bit is
     * is placed in the least significant bit.  For example, as decimals:
     *   1 becomes 2 (10 binary), -1 becomes 3 (11 binary)
     *   2 becomes 4 (100 binary), -2 becomes 5 (101 binary)
     * We generate the value for 32 bit machines, hence
     *   -2147483648 becomes 1, not 4294967297,
     * even on a 64 bit machine.
     *
     * @param int $aValue
     *
     * @return int
     */
    private function toVLQSigned($aValue)
    {
        return 0xffffffff & ($aValue < 0 ? ((-$aValue) << 1) + 1 : ($aValue << 1) + 0);
    }

    /**
     * Convert to a two-complement value from a value where the sign bit is
     * is placed in the least significant bit. For example, as decimals:
     *   2 (10 binary) becomes 1, 3 (11 binary) becomes -1
     *   4 (100 binary) becomes 2, 5 (101 binary) becomes -2
     * We assume that the value was generated with a 32 bit machine in mind.
     * Hence
     *   1 becomes -2147483648
     * even on a 64 bit machine.
     *
     * @param int $aValue
     *
     * @return int
     */
    private function fromVLQSigned($aValue)
    {
        return $aValue & 1 ? $this->zeroFill(~$aValue+2, 1) | (-1 - 0x7fffffff) : $this->zeroFill($aValue, 1);
    }

    /**
     * Right shift with zero fill.
     *
     * @param int $a number to shift
     * @param int $b number of bits to shift
     *
     * @return int
     */
    private function zeroFill($a, $b)
    {
        return ($a >= 0) ? ($a >> $b) : ($a >> $b) & (PHP_INT_MAX >> ($b-1));
    }

    /**
     * Encode single 6-bit digit as base64.
     *
     * @param int $number
     *
     * @return string
     * @throws \Exception
     */
    private function base64Encode($number)
    {
        if ($number < 0 || $number > 63) {
            throw new \Exception("Must be between 0 and 63: " . $number);
        }
        return $this->intToChar[$number];
    }

    /**
     * Decode single 6-bit digit from base64
     *
     * @param string $char
     *
     * @return int
     * @throws \Exception
     */
    private function base64Decode($char)
    {
        if (!array_key_exists($char, $this->charToInt)) {
            throw new \Exception("Not a valid base 64 digit: " . $char);
        }
        return $this->charToInt[$char];
    }
}
