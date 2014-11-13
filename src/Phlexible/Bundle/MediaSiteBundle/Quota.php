<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle;

use Phlexible\Bundle\MediaSiteBundle\Folder\SizeCalculator;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Quota
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Quota
{
    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * @var float
     */
    private $softQuotaPercent = 0.7;

    /**
     * @var int
     */
    private $hardQuota = 107374182400;

    /**
     * @var float
     */
    private $usage;

    /**
     * @param SiteInterface $site
     */
    public function __construct(SiteInterface $site)
    {
        $this->site = $site;
        $this->hardQuota = $site->getQuota();
    }

    /**
     * Return string representation
     *
     * @return string
     */
    public function __toString()
    {
        return '<pre>' .
            'Hard Quota:     ' . $this->getHardQuota() . PHP_EOL .
            'Soft Quota:     ' . $this->getSoftQuota() . PHP_EOL .
            'Soft Quota %:   ' . $this->getSoftQuotaPercent() . PHP_EOL .
            'Usage:          ' . $this->getUsage() . PHP_EOL .
            'Usage %:        ' . $this->getUsagePercent() . PHP_EOL .
            'Remaining SQ:   ' . $this->getRemainingSoftQuota() . PHP_EOL .
            'Remaining SQ %: ' . $this->getRemainingSoftQuotaPercent() . PHP_EOL .
            'Remaining HQ:   ' . $this->getRemainingHardQuota() . PHP_EOL .
            'Remaining HQ %: ' . $this->getRemainingHardQuotaPercent() . PHP_EOL .
            '';
    }

    /**
     * Return array representation
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'hard_quota'                   => $this->getHardQuota(),
            'soft_quota'                   => $this->getSoftQuota(),
            'soft_quota_percent'           => $this->getSoftQuotaPercent(),
            'usage'                        => $this->getUsage(),
            'usage_percent'                => $this->getUsagePercent(),
            'remaining_soft_quota'         => $this->getRemainingSoftQuota(),
            'remaining_soft_quota_percent' => $this->getRemainingSoftQuotaPercent(),
            'remaining_hard_quota'         => $this->getRemainingHardQuota(),
            'remaining_hard_quota_percent' => $this->getRemainingHardQuotaPercent(),
        ];
    }

    /**
     * Return soft Quota in bytes for this Site
     *
     * @return int
     */
    public function getSoftQuota()
    {
        return $this->hardQuota * $this->softQuotaPercent;
    }

    /**
     * Return soft Quota in percent for this Site
     *
     * @return float
     */
    public function getSoftQuotaPercent()
    {
        return $this->softQuotaPercent;
    }

    /**
     * Return hard Quota in bytes for this Site
     *
     * @return int
     */
    public function getHardQuota()
    {
        return $this->hardQuota;
    }

    /**
     * Return Usage in bytes for this Site
     *
     * @return int
     */
    public function getUsage()
    {
        if ($this->usage === null) {
            $calculator = new SizeCalculator();
            $calculatedSize = $calculator->calculate($this->site, $this->site->findRootFolder());

            $this->usage = $calculatedSize->getSize();
        }

        return $this->usage;
    }

    /**
     * Return Usage in percent for this Site
     *
     * @return float
     */
    public function getUsagePercent()
    {
        $usagePercent = $this->getUsage() / $this->hardQuota;

        if ($usagePercent > 1) {
            return 1;
        }

        return $usagePercent;
    }

    /**
     * Return remaining soft quota in bytes for this Site
     *
     * @return int
     */
    public function getRemainingSoftQuota()
    {
        $remainingSoftQuota = $this->getSoftQuota() - $this->getUsage();

        if ($remainingSoftQuota < 0) {
            return 0;
        }

        return $remainingSoftQuota;
    }

    /**
     * Return remaining soft quota in percent for this Site
     *
     * @return float
     */
    public function getRemainingSoftQuotaPercent()
    {
        $remainingSoftQuota = $this->getRemainingSoftQuota();

        if (!$remainingSoftQuota) {
            return 0;
        }

        return $remainingSoftQuota / $this->getHardQuota();
    }

    /**
     * Return remaining hard quota in bytes for this Site
     *
     * @return int
     */
    public function getRemainingHardQuota()
    {
        $remainingHardQuota = $this->hardQuota - $this->getUsage();

        if ($remainingHardQuota < 0) {
            return 0;
        }

        return $remainingHardQuota;
    }

    /**
     * Return remaining hard quota in percent for this Site
     *
     * @return float
     */
    public function getRemainingHardQuotaPercent()
    {
        $remainingHardQuota = $this->getRemainingHardQuota();

        if (!$remainingHardQuota) {
            return 0;
        }

        return $remainingHardQuota / $this->getHardQuota();
    }
}
