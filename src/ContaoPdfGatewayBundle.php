<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

namespace Softleister\ContaoPdfGatewayBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoPdfGatewayBundle extends Bundle
{
    public function getPath( ): string
    {
        return \dirname( __DIR__ );
    }
}
