<?php
/**
 * US-8.2 / US-8.3 : e-mail "Commande en cours" (Merci pour votre commande), comme sur la maquette.
 * US-9.3 : quand le virement est reçu, le même mail devient "Paiement reçu !".
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$date = wc_format_datetime( $order->get_date_created(), 'd/m/Y' );

if ( 'bacs' === $order->get_payment_method() ) {
	// US-9.3 : le virement est arrivé, la commande passe en préparation.
	do_action( 'woocommerce_email_header', 'Paiement reçu !', $email );

	$montant = html_entity_decode( wp_strip_all_tags( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ), ENT_QUOTES, 'UTF-8' );
	$suite   = watsons_commande_retrait( $order ) ? 'Nous préparons votre commande et vous prévenons dès qu\'elle est prête.' : 'Nous préparons votre commande et vous prévenons dès son expédition.';
	watsons_mail_intro( $order, 'Votre commande est en préparation.', 'Nous avons bien reçu votre virement de ' . $montant . '. ' . $suite );
} else {
	do_action( 'woocommerce_email_header', $email_heading, $email );

	watsons_mail_intro( $order, 'Nous préparons actuellement votre commande.', 'Nous avons bien reçu votre commande du ' . $date . '. Voici le récapitulatif de votre commande.' );
}

watsons_mail_recapitulatif( $order );

if ( watsons_commande_retrait( $order ) ) {
	// US-8.3 : le même texte que sur la page de commande (WooCommerce > Réglages > Général > Click & Collect).
	watsons_mail_instructions_retrait( $order, get_option( 'watsons_instructions_retrait', '' ) );
} else {
	watsons_mail_adresse( $order );
}

do_action( 'woocommerce_email_footer', $email );