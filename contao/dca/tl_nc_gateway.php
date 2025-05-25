<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

// $GLOBALS['TL_DCA']['tl_nc_gateway']['palettes']['pdfgateway'] = '{type_legend},type;'
//                                                                .'{options_legend},filename,gateway;';


// Positions-Icon hinzufügen
$GLOBALS['TL_DCA']['tl_nc_gateway']['list']['operations']['positions'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['positions'],
    'href'      => 'table=tl_pdfnc_positions',
    'icon'      => 'bundles/contaopdfgateway/pdf_positions.svg'
];

// PALETTES
$GLOBALS['TL_DCA']['tl_nc_gateway']['palettes']['pdfgateway'] = 
        '{title_legend},title,type,pdfnc_on;'
       .'{datei_legend},pdfnc_vorlage,pdfnc_savepath,pdfnc_overwrite,pdfnc_useHomeDir,pdfnc_fileext,pdfnc_title,pdfnc_author;'
       .'{vorlage_legend},pdfnc_multiform,pdfnc_allpages,pdfnc_tokens,pdfnc_offset,pdfnc_textcolor;'
       .'{font_legend:hide},pdfnc_font,pdfnc_fontb,pdfnc_fonti,pdfnc_fontbi;'
       .'{protect_legend:hide},pdfnc_protect';

// Subpalette hinzufügen
$GLOBALS['TL_DCA']['tl_nc_gateway']['subpalettes']['pdfnc_protect'] = 'pdfnc_password,pdfnc_openpassword,pdfnc_protectflags';


// Selector hinzufügen
$GLOBALS['TL_DCA']['tl_nc_gateway']['palettes']['__selector__'][] = 'pdfnc_protect'; 


// Kopplung mit weiterer Child-Tabelle aufbauen
$GLOBALS['TL_DCA']['tl_nc_gateway']['config']['ctable'][] = 'tl_pdfnc_positions';


// Neue Felder hinzufügen
$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_on'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_on'],
    'exclude'       => true,
    'filter'        => true,
    'inputType'     => 'checkbox',
    'eval'          => ['tl_class'=>'m12 w50'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_vorlage'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_vorlage'],
    'exclude'       => true,
    'inputType'     => 'fileTree',
    'eval'          => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr', 'extensions'=>'pdf'],
    'sql'           => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_savepath'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_savepath'],
    'exclude'       => true,
    'inputType'     => 'fileTree',
    'eval'          => ['mandatory'=>true, 'files'=>false, 'fieldType'=>'radio', 'tl_class'=>'clr w50'],
    'sql'           => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_overwrite'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_overwrite'],
    'default'       => '1',
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => ['tl_class'=>'m12 w50'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_useHomeDir'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_useHomeDir'],
    'default'       => '1',
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => ['tl_class'=>'w50'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_fileext'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_fileext'],
    'default'       => '_{{date::ymd_His}}',
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => ['maxlength'=>255, 'tl_class'=>'clr w50'],
    'sql'           => "varchar(255) NOT NULL default '_{{date::ymd_His}}'"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_allpages'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_allpages'],
    'default'       => '1',
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => ['tl_class'=>'clr m12'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_offset'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_offset'],
    'default'       => \serialize( ['0', '0'] ),
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => ['multiple'=>true, 'size'=>2, 'rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50'],
    'sql'           => "varchar(64) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_textcolor'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_textcolor'],
    'default'       => '000ac0',
    'inputType'     => 'text',
    'eval'          => ['maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w50 wizard', 'style'=>'width:138px'],
    'sql'           => "varchar(8) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_title'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_title'],
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => ['maxlength'=>255, 'tl_class'=>'clr w50'],
    'sql'           => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_author'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_author'],
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => ['maxlength'=>255, 'tl_class'=>'w50'],
    'sql'           => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_protect'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_protect'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => ['submitOnChange'=>true, 'tl_class'=>'clr w50'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_openpassword'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_openpassword'],
    'exclude'       => true,
    'inputType'     => 'text',
    // 'load_callback' => [ ['Softleister\PdfGateway\pdfnc_helper', 'decrypt'] ],
    // 'save_callback' => [ ['Softleister\PdfGateway\pdfnc_helper', 'encrypt'] ],
    'eval'          => ['preserveTags'=>true, 'tl_class'=>'w50'],
    'sql'           => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_password'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_password'],
    'exclude'       => true,
    'inputType'     => 'text',
    // 'load_callback' => [ ['Softleister\PdfGateway\pdfnc_helper', 'decrypt'] ],
    // 'save_callback' => [ ['Softleister\PdfGateway\pdfnc_helper', 'encrypt'] ],
    'eval'          => ['preserveTags'=>true, 'tl_class'=>'w50'],
    'sql'           => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_protectflags'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_protectflags'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'options'       => ['print', 'print-high', 'modify', 'assemble', 'extract', 'copy', 'annot-forms', 'fill-forms'],
    'reference'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_protectflag'],
    'eval'          => ['multiple'=>true, 'tl_class'=>'clr w50'],
    'sql'           => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_tokens'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_tokens'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => ['tl_class'=>'clr m12'],
    'sql'           => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_multiform'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_multiform'],
    'exclude'                 => true,
    'inputType'               => 'multiColumnWizard',
    'eval'                    => [
        'tl_class'            => 'clr',
        'columnFields' => [
            'bedingung' => [
                'label'       => &$GLOBALS['TL_LANG']['tl_nc_gateway']['multiform_bedingung'],
                'default'     => '',
                'exclude'     => true,
                'inputType'   => 'text',
            ],
            'seiten' => [
                'label'       => &$GLOBALS['TL_LANG']['tl_nc_gateway']['multiform_seiten'],
                'default'     => '',
                'exclude'     => true,
                'inputType'   => 'text',
            ],
        ]
    ],
    'sql'                     => "mediumtext NULL"
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_font'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_font'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr w50', 'extensions'=>'ttf,otf'],
    'sql'       => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_fontb'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_fontb'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'w50', 'extensions'=>'ttf,otf'],
    'sql'       => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_fonti'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_fonti'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'w50', 'extensions'=>'ttf,otf'],
    'sql'       => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['pdfnc_fontbi'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_gateway']['pdfnc_fontbi'],
    'exclude'   => true,
    'inputType' => 'fileTree',
    'eval'      => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'w50', 'extensions'=>'ttf,otf'],
    'sql'       => "binary(16) NULL",
];
