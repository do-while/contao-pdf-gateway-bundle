<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['notification_center']['nc_gateways']['tables'][]   = 'tl_pdfnc_positions';
$GLOBALS['BE_MOD']['notification_center']['nc_gateways']['stylesheet'] = 'bundles/contaopdfgateway/styles.css';
// $GLOBALS['BE_MOD']['notification_center']['nc_gateways']['testpdf']    = ['Softleister\PdfGateway\pdfnc_TestPdf', 'testpdf'];
