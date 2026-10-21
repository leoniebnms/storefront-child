<?php
/**
 * US-8.2 / US-8.3 : e-mail "Commande terminée", utilisé comme "Votre commande est prête !".
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email );

if ( watsons_commande_retrait( $order ) ) {
	// Retrait : instructions en premier, comme sur la maquette.
	watsons_mail_intro( $order, "Venez la récupérer au comptoir du Watson's Pub.", 'Bonne nouvelle : votre commande vous attend. Pensez à présenter votre numéro de commande.' );
	watsons_mail_instructions_retrait( $order, "Votre commande vous attend au comptoir du Watson's Pub, 17 rue Saint-Front à Périgueux, avec votre numéro de commande." );
	watsons_mail_recapitulatif( $order );
} else {
	// Livraison : la commande part chez le client.
	watsons_mail_intro( $order, 'Elle est en route vers chez vous.', 'Bonne nouvelle : votre commande est prête et part en livraison.' );
	watsons_mail_recapitulatif( $order );
	watsons_mail_adresse( $order );
}

do_action( 'woocommerce_email_footer', $email );