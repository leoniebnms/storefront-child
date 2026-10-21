<?php
/**
 * US-9.3 : e-mail "Commande annulée", comme la maquette.
 *
 * @package WooCommerce\Templates\Emails
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email );

watsons_mail_intro( $order, 'Votre commande a bien été annulée.', 'Vous avez annulé votre commande du ' . wc_format_datetime( $order->get_date_created(), 'd/m/Y' ) . '. Elle ne sera pas préparée.' );

// Le client avait déjà payé : on lui annonce le remboursement.
if ( watsons_commande_a_rembourser( $order ) ) {
	$montant = wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) );

	watsons_mail_cadre(
		'Remboursement',
		'<p class="mail-cadre__texte">Le montant payé (' . $montant . ') va vous être remboursé par virement sur votre compte bancaire.</p><p class="mail-cadre__texte">Vous recevrez un e-mail dès que le remboursement sera effectué.</p>',
		true
	);
}

watsons_mail_recapitulatif( $order );

do_action( 'woocommerce_email_footer', $email );