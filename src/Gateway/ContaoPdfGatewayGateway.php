<?php

declare( strict_types=1 );

/**
 * @copyright  Softleister 2025
 * @package    contao-pdf-gateway-bundle
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-pdf-gateway-bundle
 *
 */

namespace Softleister\ContaoPdfGatewayBundle\Gateway;

use Contao\System;
use Contao\FilesModel;
use Contao\StringUtil;

use Contao\FrontendUser;
use Psr\Log\LoggerInterface;
use Codefog\HasteBundle\StringParser;
use Symfony\Component\VarDumper\VarDumper;
use Contao\CoreBundle\Framework\ContaoFramework;
use Terminal42\NotificationCenterBundle\Parcel\Parcel;
use Terminal42\NotificationCenterBundle\Receipt\Receipt;
use Terminal42\NotificationCenterBundle\BulkyItem\FileItem;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Terminal42\NotificationCenterBundle\Parcel\ParcelCollection;
use Terminal42\NotificationCenterBundle\Gateway\GatewayInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\BulkyItemsStamp;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\GatewayConfigStamp;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\TokenCollectionStamp;

// \file_put_contents( '../var/logs/sl-debug.log', __METHOD__ . ': parcel = ' . \print_r($parcel, true). "\n", FILE_APPEND );

/**
 * Gateway that generates a file from SimpleTokens and attaches it to the notification.
 * Gateway, das eine Datei aus SimpleTokens erzeugt und an die Benachrichtigung anhängt.
 */
class ContaoPdfGatewayGateway implements GatewayInterface
{
    public const NAME = 'pdfgateway';

    public function __construct(
        private readonly ContaoFramework $contaoFramework,
        // private NotificationCenter $notificationCenter,
        private readonly StringParser $stringParser, )
    {
    }

    public function getName( ): string
    {
        return self::NAME;
    }


    public static function getAvailableGateways( ): array
    {
        // Dynamisch verfügbare Gateways via Container-Parameter laden
        $container = System::getContainer( );
        if( !$container->hasParameter( 'terminal42.notification_center.gateways' ) ) {
            return [];
        }

        $map = $container->getParameter( 'terminal42.notification_center.gateways' );
        $gateways = [];

        foreach( $map as $alias => $serviceId ) {
            if( self::NAME === $alias ) {
                continue;
            }
            try {
                $service = $container->get( $serviceId );
                $gateways[$alias] = $service->getLabel( );
            }
            catch( \Exception $e ) {
                // ignore
            }
        }
        return $gateways;
    }


    public function sealParcel( Parcel $parcel ): Parcel
    {
        return $parcel;             // Keine Änderungen vor dem Versand nötig
    }

    
    public function sendParcel( Parcel $parcel ): Receipt
    {
        $arrTokens = $parcel->getStamp( TokenCollectionStamp::class )->tokenCollection->forSimpleTokenParser( );    // SimpleTokens
        $gatewayConfig = $parcel->getStamp( GatewayConfigStamp::class )->gatewayConfig;                             // Gateway-Parameter
// \file_put_contents( '../var/logs/sl-debug.log', __METHOD__ . ': parcel = ' . \print_r($parcel, true). "\n", FILE_APPEND );

        if( $gatewayConfig->getString( 'pdfnc_on' ) ) {                                                                                 // IF( PDF-Erstellung aktiv )
            $rootDir = System::getContainer( )->getParameter( 'kernel.project_dir' );                                                   //   TL_ROOT

            // Dateinamen zusammenbauen
            $filename = $this->stringParser->recursiveReplaceTokensAndTags( $gatewayConfig->getString( 'pdfnc_fileext' ), $arrTokens ); //   Filename-Erweiterung aus den Eigenschaften
            if( empty( $filename ) || in_array( substr( $filename, 0, 1 ), ['-', '_'] ) ) {
                $filename = $gatewayConfig->getString( 'title' ) . $filename;            
            }
            $filename = StringUtil::standardize( StringUtil::restoreBasicEntities( $filename ) );                                       //   PDF-Dateiname
            $savepath = FilesModel::findByUuid( $gatewayConfig->getString( 'pdfnc_savepath' ) )->path ?? '';                            //   Speicherpfad

            // Speichern im Ausgabeverzeichnis ODER im Home-Verzeichnis des eingeloggten Benutzers
            $savepath = FilesModel::findByUuid( $gatewayConfig->getString( 'pdfnc_savepath' ) )->path ?? '';                            //   Speicherpfad
            if( $gatewayConfig->getString( 'pdfnc_useHomeDir' ) && System::getContainer( )->get( 'contao.security.token_checker' )->hasFrontendUser( ) ) {
                                                                                                                                        //   IF( User eingeloggt )

                $user = FrontendUser::getInstance( );
                if( $user->assignDir && $user->homeDir ) {                                                                              //     IF( User hat HomeDir )
                    $dir = FilesModel::findByUuid( $user->homeDir )->path ?? '';                                                        //       HomeDir ermitteln
                    if( is_dir( $rootDir . '/' . $dir ) ) {                                                                             //       IF( HomeDir ist Verzeichnis )
                        $savepath = $dir;                                                                                               //         HomeDir verwenden
                    }                                                                                                                   //       ENDIF
                }                                                                                                                       //     ENDIF
            }                                                                                                                           //   ENDIF

            if( file_exists( $rootDir . '/' . $savepath . '/' . $filename . '.pdf' ) ) {                                                
                $i = 2;
                while( file_exists( $rootDir . '/' . $savepath . '/' . $filename . '-' . $i . '.pdf' ) ) $i++;
                $filename = $filename . '-' . $i;
            }
            $pdfdatei = $savepath . '/' . $filename . '.pdf';
            file_put_contents( $rootDir . '/' . $pdfdatei, '' );                // leere Datei erzeugen um Dateinamen zu sichern

            // PDF-Daten aufbereiten
            $arrPDF = [
                'gateid'        => $gatewayConfig->getString( 'id' ),
                'gatetitle'     => $gatewayConfig->getString( 'title' ),
                'filename'      => $filename,
                'vorlage'       => FilesModel::findByUuid( $gatewayConfig->getString( 'pdff_vorlage' ) )->path ?? '',
                'savepath'      => $savepath,
                'protect'       => $gatewayConfig->getString( 'pdff_protect' ),
                // 'openpassword'  => System::getContainer()->get('contao.insert_tag.parser')->replaceInline( PdfformsHelper::decrypt( $gatewayConfig->getString( 'pdff_openpassword' ) ) ),
                'protectflags'  => StringUtil::deserialize( $gatewayConfig->getString( 'pdff_protectflags' ), true ),
                // 'password'      => System::getContainer()->get('contao.insert_tag.parser')->replaceInline( PdfformsHelper::decrypt( $gatewayConfig->getString( 'pdff_password' ) ) ),
                'multiform'     => StringUtil::deserialize( $gatewayConfig->getString( 'pdff_multiform' ), true ),
                'allpages'      => $gatewayConfig->getString( 'pdff_allpages' ),
                'offset'        => [0, 0],
                'textcolor'     => $gatewayConfig->getString( 'pdff_textcolor' ),
                'title'         => $gatewayConfig->getString( 'pdff_title' ),
                'author'        => $gatewayConfig->getString( 'pdff_author' ),
                'tokenlist'     => $gatewayConfig->getString( 'pdfnc_tokens' ),
                'arrTokens'     => $arrTokens,
                'R'             => FilesModel::findByUuid( $gatewayConfig->getString( 'pdff_font' ) )->path ?? '',
                'B'             => FilesModel::findByUuid( $gatewayConfig->getString( 'pdff_fontb' ) )->path ?? '',
                'I'             => FilesModel::findByUuid( $gatewayConfig->getString( 'pdff_fonti' ) )->path ?? '',
                'IB'            => FilesModel::findByUuid( $gatewayConfig->getString( 'pdff_fontbi' ) )->path ?? '',
            ];
            if( !is_array( $arrPDF['protectflags'] ) ) $arrPDF['protectflags'] = [$arrPDF['protectflags']];

            // Enter offsets if specified
            $ofs = StringUtil::deserialize( $gatewayConfig->getString( 'pdfnc_offset' ) );
            if( isset( $ofs[0] ) && is_numeric( $ofs[0] ) ) $arrPDF['offset'][0] = $ofs[0];
            if( isset( $ofs[1] ) && is_numeric( $ofs[1] ) ) $arrPDF['offset'][1] = $ofs[1];

            // HOOK: before pdf generation
            if( isset( $GLOBALS['TL_HOOKS']['pdfnc_BeforePdf'] ) && \is_array( $GLOBALS['TL_HOOKS']['pdfnc_BeforePdf'] ) ) {
                foreach( $GLOBALS['TL_HOOKS']['pdfnc_BeforePdf'] as $callback ) {
                    $arrPDF = System::importStatic( $callback[0] )->{$callback[1]}( $arrPDF );
                }
            }
// VarDumper::dump( $arrPDF );

            // Own tcpdf.php from files directory
            if( file_exists( $rootDir . '/files/tcpdf.php' ) ) {
                require_once( $rootDir . '/files/tcpdf.php' );
            }
            // ELSE: not found? - Then take it from this extension
            else {
                require_once( $rootDir . '/vendor/do-while/contao-pdf-gateway-bundle/contao/config/tcpdf.php' );
            }

            //--- Create and save PDF file ---
            $arrTokens['pdfnc_attachment'] = $arrTokens['pdfnc_document'] = '';
            $pdfdatei = $savepath . '/' . $filename . '.pdf';

            //--- Create token for created file ---
            $arrTokens['pdfnc_attachment'] = $pdfdatei;
            $arrTokens['pdfnc_document'] = basename( $pdfdatei );


            $content = $this->generatePdfContent( $arrTokens );
            file_put_contents( $pdfdatei, $content );

            //--- PDF-Datei erstellen und speichern ---
//            if( pdfnc_helper::getPdfData( 'S', $arrPDF, $pdfdatei ) ) {
//
//                //--- PDF-Datei in der Dateiverwaltung eintragen ---
//                $objFile = Dbafs::addResource( $pdfdatei );
//
//                // HOOK: after pdf generation
//                if( isset( $GLOBALS['TL_HOOKS']['pdfnc_AfterPdf'] ) && \is_array( $GLOBALS['TL_HOOKS']['pdfnc_AfterPdf'] ) ) {
//                    foreach( $GLOBALS['TL_HOOKS']['pdfnc_AfterPdf'] as $callback ) {
//                        System::importStatic( $callback[0] )->{$callback[1]}( $pdfdatei, $arrPDF );
//                    }
//                }
//            }
//            else {
//                $pdfdatei = '';        // es wurde keine Datei erzeugt
//            }


// dd( $pdfdatei );
            // $name = basename( $pdfdatei );
            // $size = (int) filesize( $pdfdatei );

            // $voucher = $this->getNotificationCenter( )->getBulkyItemStorage( )->store( FileItem::fromPath( $pdfdatei, $name, 'application/pdf', $size ) );
            // $bulkyItemVouchers[] = $voucher;

            // $stamps = $stamps->with( new BulkyItemsStamp( $bulkyItemVouchers ) );

// VarDumper::dump( $arrTokens );
// VarDumper::dump( $gatewayConfig );
// VarDumper::dump( $parcel );

            // PDF-Inhalt generieren (Platzhalter)
// VarDumper::dump( $stamps );


            // BulkyItemsStamp hinzufügen
            // $parcel = $parcel->with( new BulkyItemsStamp( [$voucher] ) );

            // Receipt erzeugen und als zugestellt markieren
            // $receipt = new Receipt( $parcel );
            // $receipt->setDelivered( true );
        }
//        return (!isset( $arrTokens['do_not_send_notification'] ) || empty( $arrTokens['do_not_send_notification'] ) ) && 
//               (!isset( $arrTokens['form_do_not_send_notification'] ) || empty( $arrTokens['form_do_not_send_notification'] ) );    // Notification may be sent

        // Weiterleitung an nächstes Gateway (NC-Core kümmert sich um Dispatch)
        return Receipt::createForSuccessfulDelivery( $parcel );
    }

    protected function generatePdfContent( array $tokens ): string
    {
        // Hier PDF-Logik einbinden (z.B. TCPDF, DOMPDF)
        $lines = [];
        foreach( $tokens as $key => $value ) {
            $lines[] = "$key: $value";
        }
        return implode( "\n", $lines );
    }


}

