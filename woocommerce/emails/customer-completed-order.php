<?php
/**
 * US-9.2 : e-mail "Commande terminée".
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( watsons_commande_retrait( $order ) ) {
	do_action( 'woocommerce_email_header', 'Merci de votre visite !', $email );

	watsons_mail_intro( $order, 'Votre commande a bien été retirée et réglée.', "Merci d'être passé au Watson's Pub ! Voici le récapitulatif de votre commande, à conserver." );
	watsons_mail_recapitulatif( $order );

	// Cadre "Paiement" : réglé au comptoir (paiement au retrait) ou déjà payé (virement).
	$date    = $order->get_date_completed() ? $order->get_date_completed() : $order->get_date_modified();
	$montant = wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) );
	$phrase  = 'cod' === $order->get_payment_method()
		? 'Réglé au comptoir le ' . wc_format_datetime( $date, 'd/m/Y' ) . ' : ' . $montant
		: 'Payé par ' . esc_html( mb_strtolower( $order->get_payment_method_title() ) ) . ' : ' . $montant;

	watsons_mail_cadre( 'Paiement', '<p class="mail-cadre__texte">' . $phrase . "</p><p class=\"mail-cadre__texte\">À bientôt au Watson's Pub !</p>" );
} else {
	do_action( 'woocommerce_email_header', 'Votre commande est en route !', $email );

	watsons_mail_intro( $order, 'Elle est en route vers chez vous.', 'Bonne nouvelle : votre commande est prête et part en livraison.' );
	watsons_mail_recapitulatif( $order );
	watsons_mail_adresse( $order );
}

do_action( 'woocommerce_email_footer', $email );