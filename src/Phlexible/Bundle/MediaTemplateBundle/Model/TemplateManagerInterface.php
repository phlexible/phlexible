<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

use Phlexible\Bundle\MediaTemplateBundle\Exception\NotFoundException;

/**
 * Media template manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TemplateManagerInterface
{
    /**
     * @return TemplateCollection
     */
    public function getCollection();

    /**
     * Find template
     *
     * @param string $key
     *
     * @return TemplateInterface
     * @throws NotFoundException
     */
    public function find($key);

    /**
     * @param array $criteria
     *
     * @return TemplateInterface[]
     */
    public function findBy(array $criteria);

    /**
     * Return all templates
     *
     * @return TemplateInterface[]
     */
    public function findAll();

    /**
     * Update template
     *
     * @param TemplateInterface $template
     */
    public function updateTemplate(TemplateInterface $template);
}
