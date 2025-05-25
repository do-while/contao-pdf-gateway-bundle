<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */


use Contao\System;
use Contao\Backend;
use Contao\DC_Table;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\DataContainer;


// require_once( System::getContainer( )->getParameter( 'kernel.project_dir' ) . '/vendor/do-while/contao-pdf-gateway/contao/classes/pdfnc_helper.php' );

/**
 * Table tl_pdfnc_positions
 */
$GLOBALS['TL_DCA']['tl_pdfnc_positions'] = [
    // Config
    'config' => [
        'dataContainer'               => DC_Table::class,
        'enableVersioning'            => true,
        'ptable'                      => 'tl_nc_gateway',
        'sql' => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index'
            ]
        ]
    ],

    // List
    'list' => [
        'sorting' => [
            'mode'                    => DataContainer::MODE_PARENT,
            'fields'                  => ['sorting'],
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => ['title', 'tstamp', 'type', 'pdfnc_on', 'pdfnc_vorlage', 'pdfnc_savepath'],
            'child_record_callback'   => ['tl_pdfnc_positions', 'listPositions']
        ],
        'global_operations' => [
            'testpdf' => [
                'href'                => 'key=testpdf',
                'class'               => 'header_testpdf',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="t"'
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__'                => ['type','pictype'],
        'default'                     => '{type_legend},type;'
                                        .'{publish_legend},published',

        'text'                        => '{type_legend},type;'
                                        .'{pdfnc_legend},textitems,textcolor,noblanks,notice;'
                                        .'{attr_legend},page,posxy,borderright,align,fontsize,fontstyle,texttransform;'
                                        .'{publish_legend},published',

        'picfile'                     => '{type_legend},type,notice;'
                                        .'{attr_legend},page,posxy,bedingung,invert,textarea;'
                                        .'{img_legend},pictype,picture,size;'
                                        .'{publish_legend},published',

        'picupload'                   => '{type_legend},type,notice;'
                                        .'{attr_legend},page,posxy,bedingung,invert,textarea;'
                                        .'{img_legend},pictype,pictag,size;'
                                        .'{publish_legend},published',

        'picdata'                     => '{type_legend},type,notice;'
                                        .'{attr_legend},page,posxy,bedingung,invert,textarea;'
                                        .'{img_legend},pictype,pictag,size;'
                                        .'{publish_legend},published',

        'picuuid'                     => '{type_legend},type,notice;'
                                        .'{attr_legend},page,posxy,bedingung,invert,textarea;'
                                        .'{img_legend},pictype,pictag,size;'
                                        .'{publish_legend},published',

        'qrcode'                      => '{type_legend},type,bartype;'
                                        .'{pdfnc_legend},textitems,textcolor,noblanks,notice;'
                                        .'{attr_legend},page,posxy,bedingung,invert,qrsize;'
                                        .'{publish_legend},published',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql'                     => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'autoincrement' => true]
        ],
        'pid' => [
            'foreignKey'              => 'tl_nc_gateway.title',
            'sql'                     => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '0'],
            'relation'                => ['type' => 'belongsTo', 'load' => 'lazy']
        ],
        'sorting' => [
            'sql'                     => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '0']
        ],
        'tstamp' => [
            'sql'                     => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '0']
        ],
//-------
        'type' => [
            'exclude'                 => true,
            'default'                 => 'text',
            'inputType'               => 'select',
            'options'                 => ['text', 'pic', 'qrcode'],
            'reference'               => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['type_'],
            'eval'                    => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'                     => ['type' => 'string', 'length' => 8, 'default' => 'text']
        ],
        'bartype' => [
            'exclude'                 => true,
            'default'                 => 'QRCODE,Q',
            'filter'                  => true,
            'inputType'               => 'select',
            'options'                 => ['2d' => ['QRCODE,L', 'QRCODE,M', 'QRCODE,Q', 'QRCODE,H', 'PDF417', 'DATAMATRIX'], 
                                          '1d' => ['C39', 'C39+', 'C39E', 'C39E+', 'C93', 'S25', 'S25+', 'I25', 'I25+', 'C128',
                                          'C128A', 'C128B', 'C128C', 'EAN8', 'EAN13', 'UPCA', 'UPCE', 'EAN5', 'EAN2', 'MSI', 'MSI+', 
                                          'CODABAR', 'CODE11', 'PHARMA', 'PHARMA2T', 'IMB', 'POSTNET', 'PLANET', 'RMS4CC', 'KIX']
                                         ],
            'reference'               => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['bartype_'],
            'eval'                    => ['tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 12, 'default' => 'QRCODE,Q']
        ],
//-------
        'textitems' => [
            'exclude'                 => true,
            'inputType'               => 'multiColumnWizard',
            'search'                  => true,
            'eval'                    => [
                'columnFields' => [
                    'feld' => [
                        'label'             => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['textitem_feld'],
                        'default'           => '',
                        'exclude'           => true,
                        'inputType'         => 'text',
                        'eval'              => ['allowHtml' => true, 'style' => 'width:350px'],
                    ],
                    'bedingung' => [
                        'label'             => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['textitem_bedingung'],
                        'exclude'           => true,
                        'inputType'         => 'text',
                        'eval'              => ['style' => 'width:235px'],
                    ],
                ]
            ],
            'sql'                     => "mediumtext NULL"
        ],
        'notice' => [
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'textarea',
            'eval'                    => ['tl_class' => 'clr long'],
            'sql'                     => "text NULL"
        ],
        'noblanks' => [
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['tl_class' => 'm12 w50'],
            'sql'                     => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => '']
        ],
//-------
        'page' => [
            'exclude'                 => true,
            'default'                 => '1',
            'filter'                  => true,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '1']
        ],
        'posxy' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'maxlength' => 6, 'multiple' => true, 'size' => 2, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 64, 'default' => '']
        ],
        'textarea' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'maxlength' => 6, 'multiple' => true, 'size' => 2, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 64, 'default' => '']
        ],
//-------
        'borderright' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 16, 'default' => '']
        ],
        'align' => [
            'exclude'                 => true,
            'default'                 => 'left',
            'inputType'               => 'select',
            'options'                 => ['left', 'center', 'right'],
            'reference'               => &$GLOBALS['TL_LANG']['MSC'],
            'eval'                    => ['tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 32, 'default' => '']
        ],
        'fontsize' => [
            'exclude'                 => true,
            'default'                 => '11',
            'inputType'               => 'text',
            'eval'                    => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 16, 'default' => '11']
        ],
        'textcolor' => [
            'exclude'                 => true,
            'default'                 => '',
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 6, 'colorpicker' => true, 'isHexColor' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard', 'style' => 'width:138px'],
            'sql'                     => ['type' => 'string', 'length' => 8, 'default' => '']
        ],
        'fontstyle' => [
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'options'                 => ['bold', 'italic'],
            'reference'               => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['fontstyle_'],
            'eval'                    => ['multiple' => true, 'tl_class' => 'clr w50'],
            'sql'                     => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'texttransform' => [
            'exclude'                 => true,
            'inputType'               => 'select',
            'options'                 => ['uppercase', 'lowercase', 'capitalize', 'none'],
            'reference'               => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['texttransform_'],
            'eval'                    => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 32, 'default' => '']
        ],
//-------
        'pictype' => [
            'exclude'                 => true,
            'default'                 => 'file',
            'filter'                  => true,
            'inputType'               => 'select',
            'options'                 => ['file', 'upload', 'data', 'uuid'],
            'reference'               => &$GLOBALS['TL_LANG']['tl_pdfnc_positions']['pictype_'],
            'eval'                    => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'                     => ['type' => 'string', 'length' => 8, 'default' => 'file']
        ],
        'picture' => [
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => ['mandatory' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr', 'extensions' => '%contao.image.valid_extensions%'],
            'sql'                     => ['type' => 'binary', 'length' => 16, 'fixed' => true, 'notnull' => false],
        ],
        'pictag' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'clr w50'],
            'sql'                     => ['type' => 'string', 'length' => 64, 'default' => 'file']
        ],
        'qrsize' => [
            'exclude'                 => true,
            'default'                 => '2',
            'inputType'               => 'select',
            'options'                 => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
            'eval'                    => ['tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 2, 'default' => '2']
        ],
//-------
        'bedingung' => [
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'                     => ['type' => 'string', 'length' => 64, 'default' => '']
        ],
        'invert' => [
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['tl_class' => 'm12 w50'],
            'sql'                     => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => '']
        ],
//-------
        'published' => [
            'exclude'                 => true,
            'filter'                  => true,
            'toggle'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['doNotCopy' => true],
            'sql'                     => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => '']
        ],
    ]
];


/**
 * Class tl_pdfnc_positions
 */
class tl_pdfnc_positions extends Backend
{
    //-----------------------------------------------------------------
    //  Callback zum Anzeigen der Positionen im Backend
    //
    //  $arrRow - aktueller Datensatz
    //-----------------------------------------------------------------
    public function listPositions( $arrRow )
    {
        $pub   = $arrRow['published'] ? 'pdfcolor' : 'pdfcolor unpublished';
        $pos   = StringUtil::deserialize( $arrRow['posxy'], true );
        $area  = StringUtil::deserialize( $arrRow['textarea'], true );
        $items = StringUtil::deserialize( $arrRow['textitems'], true );

        switch( $arrRow['type'] ) {
            case 'pic':     if( $arrRow['pictype'] === 'file' ) {
                                $text = FilesModel::findByUuid( $arrRow['picture'] )->path ?? '';
                                $text = '<span title="' . $text . '">' . basename( $text ) . '</span>';
                            }
                            else {
                                $text = $arrRow['pictag'];
                            }
                            break;

            case 'qrcode':
            case 'text':
            default:        $style = StringUtil::deserialize( $arrRow['fontstyle'], true );
                            $text = ( in_array( 'bold', $style ) ? '<strong>' : '' ) . ( in_array( 'italic', $style ) ? '<em>' : '' );
                            foreach( $items as $item ) {
                                $text .= $item['feld'] . '<br>';
                            }
                            $text .= ( in_array( 'italic', $style ) ? '</em>' : '' ) . ( in_array( 'bold', $style ) ? '</strong>' : '' );
                            break;
        }

        $result = '<table class="pdfposition"><tr class="' . $pub . '">'
                 .'<td width="32"><img src="bundles/contaopdfgateway/pos_' . $arrRow['type'] . '.svg" width="16" height="16" alt=""></td>'
                 .'<td width="240" valign="top">' . $text . '</td>'
                 .'<td width="65" valign="top">' . $GLOBALS['TL_LANG']['tl_pdfnc_positions']['seite'] . ' ' . ( $arrRow['page'] ?? '' ) . '</td>'
                 .'<td width="65" valign="top">X = ' . ( $pos[0] ?? '' ) . '</td>'
                 .'<td width="65" valign="top">Y = ' . ( $pos[1] ?? '' ) . '</td>';

        if( ( $arrRow['type'] === 'pic' ) && !empty( $arrRow['textarea'] ) ) {
            $result .= '<td width="120" valign="top">(' . $area[0] . ' x ' . $area[1] . ' mm)</td>';
        }
        else {
            $result .= '<td width="120" valign="top">&nbsp;</td>';
        }

        $result .= '<td valign="top">' . ( $arrRow['notice'] ?? '' ) . '</td>'
                  .'</tr></table>';

        return $result;
    }


    //-----------------------------------------------------------------
    //  Erstellt eine Liste der Formularfelder
    //  $dc->currentRecord   ist die ID des tl_pdfnc_positions
    //-----------------------------------------------------------------
    public function getFelder( $dc )
    {
        $arrFields = [];                    //=== Aufbau eines Feldes mit Vergleichswerten ===
        $cm_text = '';
        $cm_nr = 0;

        // Formular ermitteln
        $objFormField = $this->Database->prepare( "SELECT * FROM tl_form_field WHERE invisible<>1 AND pid=(SELECT pid FROM tl_pdfnc_positions WHERE id=?) ORDER BY sorting" )
                                       ->execute( $dc->currentRecord );

        if( $objFormField->numRows < 1 ) return $arrFields;                     // keine Felder nicht gefunden: leeres Array zurück

        while( $objFormField->next( ) ) {
            $options = StringUtil::deserialize( $objFormField->options );       // Options auflösen

            switch( $objFormField->type ) {
                case 'submit':              break;                                                  // Kommt nicht in die Liste

                case 'select':
                case 'radio':
                case 'checkbox':
                                            if( empty( $options ) ) break;
                                            foreach( $options as $opt ) {                                   // Die Optionen einzeln
                                                $arrFields[$objFormField->name . '~'. $opt['value']] = $objFormField->name . '~'. $opt['value'];
                                            }
                                            break;

                case 'condition':
                case 'text':
                case 'textarea':
                case 'hidden':              $arrFields[$objFormField->name] = $objFormField->name;          // Feldname direkt
                                            break;

                case 'cm_alternative':      if( $objFormField->cm_alternativeType === 'cm_stop' ) break;
                                            if( $objFormField->cm_alternativeType === 'cm_start' ) {
                                                $cm_text = $objFormField->name;
                                                $cm_nr = 0;
                                            }
                                            else {
                                                $cm_nr++;
                                            }
                                            $arrFields[$cm_text . '~'. $cm_nr] = $cm_text . '~'. $cm_nr;    // Feldname mit Option
                                            break;
            }
        }

        asort( $arrFields, SORT_FLAG_CASE );    // Alphabetisch sortieren

        return $arrFields;
    }


    //-----------------------------------------------------------------
}
