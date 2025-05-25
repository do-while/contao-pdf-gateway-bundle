<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

namespace Softleister\ContaoPdfGatewayBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoPdfGatewayExtension extends Extension
{
    public function load( array $configs, ContainerBuilder $container ): void
    {
        ( new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../../config' ) ) )
            ->load( 'services.yaml' )
        ;
    }
}
