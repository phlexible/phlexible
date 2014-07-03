<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Table field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TableField extends AbstractField
{
    protected $icon = 'p-elementtype-field_table-icon';

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $item['rawContent'] = $item['data_content'];

        $content = array();
        if (!empty($item['data_content'])) {
            try {
                $content = json_decode($item['data_content'], true);
            } catch (\Exception $e) {
            }
        }

        $item['content'] = array(
            'data'         => array(),
            'hasRowHeader' => false,
            'hasColHeader' => false,
        );

        if (!empty($content['hasRowHeader'])) {
            $item['content']['hasRowHeader'] = true;
        }

        if (!empty($content['hasColHeader'])) {
            $item['content']['hasColHeader'] = true;
        }

        if (!empty($content['data'])) {
            $item['content']['data'] = $content['data'];

            $item['empty'] = true;
            foreach ($content['data'] as $rows) {
                foreach ($rows as $col) {
                    if ($col) {
                        unset($item['empty']);
                        break 2;
                    }
                }
            }
        } else {
            $item['empty'] = true;
        }

        // legacy
        #print_r($content);
        if (!empty($content['rowHeaders']) || !empty($content['colHeaders'])) {
            foreach ($item['content']['data'] as $rowKey => $row) {
                if (empty($row[0]) && !empty($row[1])) {
                    unset($item['content']['data'][$rowKey][0]);
                    $item['content']['data'][$rowKey] = array_values($item['content']['data'][$rowKey]);
                }
            }

            if (!empty($content['rowHeaders'])) {
                $hasRowHeader = false;
                foreach ($content['rowHeaders'] as $rowHeader) {
                    if (!empty($rowHeader)) {
                        $hasRowHeader = true;
                        break;
                    }
                }

                if ($hasRowHeader) {
                    $item['content']['hasRowHeader'] = true;

                    foreach ($item['content']['data'] as $key => $row) {
                        array_unshift($item['content']['data'][$key], $content['rowHeaders'][$key]);
                    }
                }

                unset($content['rowHeaders']);
            }

            if (!empty($content['colHeaders'])) {
                $hasColHeader = false;
                foreach ($content['colHeaders'] as $colHeader) {
                    if (!empty($colHeader)) {
                        $hasColHeader = true;
                        break;
                    }
                }

                if ($hasColHeader) {
                    $item['content']['hasColHeader'] = true;

                    if ($hasRowHeader) {
                        array_unshift($content['colHeaders'], '');
                    }

                    if (!empty($item['content']['data'][0]) && count($item['content']['data'][0]) < count(
                            $content['colHeaders']
                        )
                    ) {
                        array_pop($content['colHeaders']);
                    }

                    array_unshift($item['content']['data'], $content['colHeaders']);
                }

                unset($content['colHeaders']);
            }
        }
        #print_r($item['content']);die;
        // end legacy

        $meta = array();
        foreach ($item['content']['data'] as $row => $rowData) {
            foreach ($rowData as $col => $cell) {
                $meta[$row][$col] = array(
                    'row'     => $row,
                    'col'     => $col,
                    'rowspan' => 0,
                    'colspan' => 0,
                );

                if ($cell === '#r' && $col) {
                    $meta[$row][$col - 1]['rowspan']++;
                } elseif ($cell === '#c' && $row) {
                    $meta[$row - 1][$col]['colspan']++;
                }
            }
        }

        return $item;
    }
}
