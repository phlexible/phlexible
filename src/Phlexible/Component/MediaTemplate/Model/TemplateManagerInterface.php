<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Model;

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
