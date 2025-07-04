<?php

namespace Contao;

$rootDir = System::getContainer( )->getParameter('kernel.project_dir' );

\define( 'K_TCPDF_EXTERNAL_CONFIG', true );
\define( 'K_PATH_MAIN', $rootDir . '/vendor/tecnickcom/tcpdf/' );
\define( 'K_PATH_URL', Environment::get('base') . 'vendor/tecnickcom/tcpdf/' );
\define( 'K_PATH_FONTS', K_PATH_MAIN . 'fonts/' );
\define( 'K_PATH_CACHE', $rootDir . '/system/tmp/' );
\define( 'K_PATH_URL_CACHE', $rootDir . '/system/tmp/' );
\define( 'K_PATH_IMAGES', K_PATH_MAIN . 'images/' );
\define( 'K_BLANK_IMAGE', K_PATH_IMAGES . '_blank.png' );
\define( 'PDF_PAGE_FORMAT', 'A4' );
\define( 'PDF_PAGE_ORIENTATION', 'P' );
\define( 'PDF_CREATOR', 'Contao Open Source CMS' );
\define( 'PDF_AUTHOR', Environment::get('url') );
\define( 'PDF_HEADER_TITLE', 'websiteTitle' );
\define( 'PDF_HEADER_STRING', '' );
\define( 'PDF_HEADER_LOGO', '' );
\define( 'PDF_HEADER_LOGO_WIDTH', 30 );
\define( 'PDF_UNIT', 'mm' );
\define( 'PDF_MARGIN_HEADER', 0 );
\define( 'PDF_MARGIN_FOOTER', 0 );
\define( 'PDF_MARGIN_TOP', 10 );
\define( 'PDF_MARGIN_BOTTOM', 10 );
\define( 'PDF_MARGIN_LEFT', 15 );
\define( 'PDF_MARGIN_RIGHT', 15 );
\define( 'PDF_FONT_NAME_MAIN', 'freeserif' );
\define( 'PDF_FONT_SIZE_MAIN', 12 );
\define( 'PDF_FONT_NAME_DATA', 'freeserif' );
\define( 'PDF_FONT_SIZE_DATA', 12 );
\define( 'PDF_FONT_MONOSPACED', 'freemono' );
\define( 'PDF_FONT_SIZE_MONOSPACED', 10);
\define( 'PDF_IMAGE_SCALE_RATIO', 1.25 );
\define( 'HEAD_MAGNIFICATION', 1.1 );
\define( 'K_CELL_HEIGHT_RATIO', 1.25 );
\define( 'K_TITLE_MAGNIFICATION', 1.3 );
\define( 'K_SMALL_RATIO', 2/3 );
\define( 'K_THAI_TOPCHARS', false );
\define( 'K_TCPDF_CALLS_IN_HTML', false );
