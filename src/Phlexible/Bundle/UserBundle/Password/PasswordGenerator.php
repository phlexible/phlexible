<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Password;

/**
 * Password generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PasswordGenerator
{
    const TYPE_PRONOUNCABLE   = 'pronouncable';
    const TYPE_UNPRONOUNCABLE = 'unpronouncable';

    /**
     * Create a single password
     *
     * @param  integer Length of the password.
     * @param  string  Type of password (pronounceable, unpronounceable)
     * @param  string  Character which could be use in the
     *                 unpronounceable password ex : 'A,B,C,D,E,F,G'
     *                 or numeric, alphabetical or alphanumeric.
     * @return string  Returns the generated password
     */
    public function create($length = 10, $type = self::TYPE_PRONOUNCABLE, $chars = null)
    {
        switch ($type)
        {
            case self::TYPE_UNPRONOUNCABLE:
                return $this->createUnpronounceable($length, $chars);
                break;

            case self::TYPE_PRONOUNCABLE:
            default:
                return $this->createPronounceable($length);
                break;
        }
    }

    /**
     * Create pronounceable password
     *
     * This method creates a string that consists of
     * vowels and consonats.
     *
     * @param  integer $length Length of the password
     *
     * @return string  Returns the password
     */
    private function createPronounceable($length)
    {
        $retVal = '';

        /**
         * List of vowels and vowel sounds
         */
        $v = array(
            'a',  'e',  'i',  'o', 'u', 'ae', 'ou', 'io',
            'ea', 'ou', 'ia', 'ai'
        );

        /**
         * List of consonants and consonant sounds
         */
        $c = array(
            'b',  'c',  'd',  'g',  'h',  'j',  'k', 'l', 'm',
            'n',  'p',  'r',  's',  't',  'u',  'v', 'w',
            'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th',
            'ch', 'ph', 'st', 'sl', 'cl'
        );

        $vCount = 12;
        $cCount = 29;

        for ($i = 0; $i < $length; $i++)
        {
            $retVal .= $c[mt_rand(0, $cCount-1)] . $v[mt_rand(0, $vCount-1)];
        }

        return substr($retVal, 0, $length);
    }

    /**
     * Create unpronounceable password
     *
     * This method creates a random unpronounceable password
     *
     * @param int     $length Length of the password
     * @param string  $chars  Character which could be use in the
     *                        unpronounceable password ex : 'ABCDEFG'
     *                        or numeric, alphabetical or alphanumeric.
     *
     * @return string Returns the password
     */
    private function createUnpronounceable($length, $chars)
    {
        $password = '';

        /**
         * List of character which could be use in the password
         */
         switch($chars)
         {
             case 'alphanumeric':
                 $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                 $numberOfPossibleCharacters = 62;
                 break;

             case 'alphabetical':
                 $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                 $numberOfPossibleCharacters = 52;
                 break;

             case 'numeric':
                 $chars = '0123456789';
                 $numberOfPossibleCharacters = 10;
                 break;

             case null:
                 $chars = '_#@%&ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                 $numberOfPossibleCharacters = 67;
                 break;

             default:
                 /**
                  * Some characters shouldn't be used
                  */
                 $chars = trim($chars);
                 $chars = str_replace(array('+', '|', '$', '^', '/', '\\', ','), '', $chars);

                 $numberOfPossibleCharacters = strlen($chars);
                 break;
         }

         /**
          * Generate password
          */
         for ($i = 0; $i < $length; $i++)
         {
             $num = mt_rand(0, $numberOfPossibleCharacters - 1);
             $password .= $chars{$num};
         }

         /**
          * Return password
          */
         return $password;
    }
}
