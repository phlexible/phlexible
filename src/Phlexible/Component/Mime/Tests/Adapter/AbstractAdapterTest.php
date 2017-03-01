<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

/**
 * Abstract adapter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractAdapterTest extends TestCase
{
    protected $fileMap = [
        'file.csv' => ['text/csv', 'text/plain', 'text/plain; charset=us-ascii'],
        'file.txt' => ['text/plain', 'text/plain; charset=us-ascii'],
        'file.gif' => ['image/gif', 'image/gif; charset=binary'],
        /*
        'test.doc' => ['application/msword'],
        'test.docm' => ['application/msword', 'application/zip', 'application/vnd.ms-word.document.macroEnabled.12'],
        'test.docx' => [
            'application/msword',
            'application/zip',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ],
        'test.dot' => ['application/msword'],
        'test.dotx' => [
            'application/msword',
            'application/zip',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template'
        ],
        'test.gif' => ['image/gif'],
        'test.html' => ['text/html'],
        'test.jpg' => ['image/jpeg'],
        'test.mp3' => ['audio/mpeg'],
        'test.pdf' => ['application/pdf'],
        'test.png' => ['image/png'],
        'test.pot' => ['application/msword', 'application/vnd.ms-powerpoint'],
        'test.ppt' => ['application/msword', 'application/vnd.ms-powerpoint'],
        //'test.psd'      => array('image/x-photoshop'),
        'test.tif' => ['image/tiff'],
        'test.wav' => ['audio/x-wav'],
        'test.xlsm' => ['application/msword', 'application/zip', 'application/vnd.ms-excel.sheet.macroEnabled.12'],
        'test.xlsx' => [
            'application/msword',
            'application/zip',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
        'test.xltx' => [
            'application/msword',
            'application/zip',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
        ],
        */
    ];

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getFile($name = '')
    {
        return dirname(__DIR__).'/fixture/'.$name;
    }

    /**
     * @return AdapterInterface
     */
    abstract protected function createAdapter();
}
