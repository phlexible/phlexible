<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Phlexible\Bundle\MediaSiteBundle\Driver\DriverInterface;

/**
 * Media site interface
 * Represents a complete set of classes used to get a virtual set of folders and files
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SiteInterface extends DriverInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getRootDir();

    /**
     * @return int
     */
    public function getQuota();

    /**
     * @return DriverInterface
     */
    public function getDriver();}
