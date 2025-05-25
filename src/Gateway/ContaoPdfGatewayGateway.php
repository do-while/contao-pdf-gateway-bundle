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

use Terminal42\NotificationCenter\Gateway\GatewayInterface;
use Terminal42\NotificationCenter\Gateway\GatewayContext;
use Terminal42\NotificationCenter\Gateway\GatewayManager;

/**
 * Gateway that generates a file from SimpleTokens and attaches it to the notification.
 * Gateway, das eine Datei aus SimpleTokens erzeugt und an die Benachrichtigung anhängt.
 */
class ContaoPdfGatewayGateway implements GatewayInterface
{
    public const NAME = 'FileGenerator';

    /**
     * Return the internal gateway name (unique identifier).
     * Gibt den Namen des internen Gateways zurück (eindeutige Kennung).
     */
    public function getName( ): string
    {
        return self::NAME;
    }

    /**
     * Return a human‑readable label for the gateway.
     * Rückgabe einer von Menschen lesbaren Bezeichnung für das Gateway.
     */
    public function getLabel( ): string
    {
        return 'Datei-Generator';
    }

    /**
     * Define configurable options for this gateway.
     * Definieren Sie konfigurierbare Optionen für dieses Gateway.
     */
    public function getOptions( ): array
    {
        return [
            'gateway' => [
                'label'       => 'Weiteres Gateway',
                'inputType'   => 'select',
                'options_callback' => [static::class, 'getAvailableGateways'],
                'eval'        => ['includeBlankOption' => true],
            ],
            'filename' => [
                'label'     => 'Dateiname',
                'inputType' => 'text',
                'eval'      => ['mandatory' => true],
            ],
        ];
    }

    /**
     * Callback to populate the gateway selection dropdown.
     * Callback zum Auffüllen des Dropdown-Menüs für die Gateway-Auswahl.
     */
    public static function getAvailableGateways( ): array
    {
        $gateways = [];

        foreach( GatewayManager::getInstance( )->getGateways( ) as $id => $class ) {
            if( $id === 'file_generator' ) {
                continue;
            }
            $gateways[$id] = $class::getLabel(  );
        }

        return $gateways;
    }

    /**
     * Generate the file and attach it, then optionally forward to another gateway.
     * Erzeugen Sie die Datei und hängen Sie sie an, und leiten Sie sie dann optional an ein anderes Gateway weiter.
     */
    public function compile( GatewayContext $context ): void
    {
        // Fetch all tokens
        // Alle Token abrufen
        $tokens = $context->getTokens( );

        // Build file content (e.g. CSV)
        // Dateiinhalt erstellen (z. B. CSV)
        $content = $this->generateContent( $tokens );

        // Determine filename and path
        // Dateiname und Pfad bestimmen
        $filename = $context->getOption( 'filename' );
        $tmpPath  = 'system/tmp/' . uniqid( 'nc_file_' ) . '_' . basename( $filename );
        $fullPath = TL_ROOT . '/' . $tmpPath;

        // Write to disk
        // Schreiben auf Festplatte
        file_put_contents( $fullPath, $content );

        // Attach the file to the notification
        // Hängen Sie die Datei an die Meldung an
        $context->addAttachment( [
            'path' => $fullPath,
            'name' => $filename,
        ] );

        // Forward to another gateway if configured
        // Weiterleitung an ein anderes Gateway, falls konfiguriert
        if( $next = $context->getOption('gateway') ) {
            $context->setNextGateway( $next );
        }
    }

    /**
     * Convert tokens into a simple CSV (key;value).
     * Konvertiert Token in eine einfache CSV-Datei (Schlüssel;Wert).
     */
    protected function generateContent( array $tokens ): string
    {
        $lines = [];
        foreach( $tokens as $key => $value ) {
            // Escape semicolons
            // Semikolon auslassen
            $escaped = str_replace( ';', '\\;', $value );
            $lines[] = sprintf( "%s;%s", $key, $escaped );
        }

        return implode( "\n", $lines );
    }
}
