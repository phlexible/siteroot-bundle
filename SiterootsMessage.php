<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Siteroots message
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class SiterootsMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'channel' => 'siteroot',
        );
    }
}