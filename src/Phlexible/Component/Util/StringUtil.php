<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Util;

/**
 * String utils.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 * @author Phillip Look <plook@brainbits.net>
 */
class StringUtil
{
    /**
     * Function truncates a HTML string, preserving and closing all tags.
     *
     * @param string $str
     * @param int    $textMaxLength
     * @param string $postString
     * @param string $encoding
     *
     * @return string
     */
    public function truncatePreservingTags($str, $textMaxLength, $postString = '...', $encoding = 'UTF-8')
    {
        // complete string length with tags
        $htmlLength = mb_strlen($str, $encoding);
        // cursor position
        $htmlPos = 0;
        // extracted text length without tags
        $textLength = 0;
        // tags that need to closed
        $closeTag = array();

        if ($htmlLength <= $textMaxLength) {
            return $str;
        }

        $inEntity = false;

        // loop through str, start at cursor position
        for ($i = $htmlPos; $i < $htmlLength; ++$i) {
            // extract multibyte encoded char on cursor position
            $char = mb_substr($str, $i, 1, $encoding);

            if ($char === '&') {
                if (!$inEntity) {
                    $inEntity = true;
                }
            } elseif ($char === ';') {
                if ($inEntity) {
                    $inEntity = false;
                }
            } // check on an angle bracket assuming text following is a tag
            elseif ($char === '<') {
                // sub cursor position inside the tag (after the first angle bracket)
                $tagPos = $i + 1;
                // remember first position inside the tag
                $tagFirst = $tagPos;
                // size of tag (including angle brackets <1234>)
                $tagSize = 1;
                // tag name
                $tagName = '';

                // tag is valid ( <tag> OR </tag> )
                $isTag = true;
                // tag is a closing tag </tag>
                $isClosingTag = false;

                // loop through text inside the tag until it is closed
                for ($j = $tagPos; $j < $htmlLength; ++$j) {
                    // extract multibyte encoded char on sub cursor position
                    $charTag = mb_substr($str, $j, 1, $encoding);

                    // another opening angle bracket = first angle bracket serves as "smaller as"
                    // not a valid tag, set mark and break
                    if ($charTag === '<') {
                        $isTag = false;
                        break;
                    }
                    // seems to be an '<>' entity
                    // not a valid tag, set mark and break
                    elseif ($tagFirst === $j
                        && $charTag === '>'
                    ) {
                        $isTag = false;
                        break;
                    } // closing tag, set mark
                    elseif ($tagFirst === $j
                        && $charTag === '/'
                    ) {
                        $isClosingTag = true;
                    } // tag has ended, break
                    elseif ($charTag === '>') {
                        break;
                    } // remember char as tag name
                    else {
                        $tagName .= $charTag;
                    }

                    ++$tagSize;
                }

                // valid tag
                if (!empty($isTag)) {
                    // closing tag </tag>
                    if (!empty($isClosingTag)) {
                        // remove it from closing array
                        array_pop($closeTag);
                    } // not a closing tag
                    else {
                        // remember to close it
                        $closeTag[] = $tagName;
                    }

                    // set cursor position, continue with next char after the tag
                    $i = $i + $tagSize;
                } // not a valid tag
                else {
                    // set assumed tag size as additional textLength, because it is text
                    $textLength = $textLength + $tagSize;
                    // set cursor position and reset the last < or > char to start a new check
                    $i = $i + $tagSize - 1;
                }
            } // normal char, increase textLength
            else {
                ++$textLength;
            }

            // if maximum length reached break
            if (!$inEntity && $textLength >= $textMaxLength) {
                break;
            }
        }

        // set cursor to new position
        $htmlPos = $i + 1;

        // extract preview text from 0 to current cursor position
        $ret = mb_substr($str, 0, $htmlPos, $encoding);

        // if necessary set placeholder string (before tags get closed)
        if ($textLength >= $textMaxLength) {
            $ret .= $postString;
        }

        // if we are between tags, close them correctly
        if (count($closeTag)) {
            foreach (array_reverse($closeTag) as $tag) {
                $tokens = explode(' ', $tag);
                $ret .= '</'.$tokens[0].'>';
            }
        }

        return $ret;
    }

    /**
     * Remove broken HTML entities from input string.
     * (e.g. 'Welcome in K&ouml' -> 'Welcome in K').
     *
     * @param string $html
     *
     * @return string
     */
    public function stripBrokenHtmlEntities($html)
    {
        return preg_replace(array('/^[^&]{0,7};/u', '/&[^;]{0,7}$/u'), '', $html);
    }

    /**
     * Wrapper for preg_quote supprting strings and array of strings.
     *
     * @param array|string $values
     * @param string       $delimiter
     *
     * @return string
     */
    public function pregQuote($values, $delimiter = null)
    {
        if (!is_array($values)) {
            return preg_quote($values, $delimiter);
        }

        // case: needle is array
        foreach ($values as $key => $value) {
            $values[$key] = preg_quote($value, $delimiter);
        }

        return implode('|', $values);
    }

    /**
     * Converts line breaks into <br /> tags
     * and double <br /> to </p><p> tags.
     *
     * @param string $string
     *
     * @return string
     */
    public function nl2p($string)
    {
        // replace lineFeed with <br /> tag
        $lineFeed = chr(10);
        $value = preg_replace('#'.$lineFeed.'#', '<br />', $string);

        // replace double <br /> tags with </p><p> tags
        $pattern = '#<br />([ ]*)<br />#';
        $replacement = '</p><p>';
        $result = preg_replace($pattern, $replacement, $value);

        return $result;
    }
}
