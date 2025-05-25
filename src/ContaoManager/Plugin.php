<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

namespace Softleister\ContaoPdfGatewayBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Softleister\ContaoPdfGatewayBundle\ContaoPdfGatewayBundle;
use Terminal42\NotificationCenterBundle\Terminal42NotificationCenterBundle;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    public function getBundles( ParserInterface $parser )
    {
        return [
            BundleConfig::create( ContaoPdfGatewayBundle::class )
                ->setLoadAfter( [ContaoCoreBundle::class, Terminal42NotificationCenterBundle::class] ),
        ];
    }
}
