<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Model;

use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSetField;

/**
 * Option resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OptionResolver
{
    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $type = $field->getType();

        if ($type === 'select') {
            $options = array();
            foreach (explode(',', $field->getOptions()) as $value) {
                $options[] = array($value, $value);
            }

            return $options;
        } elseif ($type === 'suggest') {
            $dataSourceId = $field->getOptions();
            $options = array(
                'source_id' => $dataSourceId,
            );

            return $options;
        }

        return null;
    }

}
