<?php
/**
 * US-8.2 : e-mail "Commande en attente" (paiement par virement), comme sur la maquette.
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$date = wc_format_datetime( $order->get_date_created(), 'd/m/Y' );

do_action( 'woocommerce_email_header', $email_heading, $email );

if ( 'bacs' === $order->get_payment_method() ) {
	watsons_mail_intro( $order, 'Votre commande sera préparée dès réception de votre virement.', 'Validez votre commande du ' . $date . ' en effectuant un virement avec les coordonnées ci-dessous.' );
	watsons_mail_coordonnees_bancaires( $order );
} else {
	watsons_mail_intro( $order, 'Votre commande sera préparée dès réception de votre paiement.', 'Votre commande du ' . $date . ' est en attente de paiement.' );
}

watsons_mail_recapitulatif( $order );

// Livraison : on rappelle l'adresse. Retrait : pas d'instructions tant que le virement n'est pas reçu.
if ( ! watsons_commande_retrait( $order ) ) {
	watsons_mail_adresse( $order );
}

do_action( 'woocommerce_email_footer', $email );