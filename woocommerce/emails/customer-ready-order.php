<?php
/**
 * US-9.2 : e-mail "Votre commande est prête !" (nouveau statut "Prête").
 *
 * @package WooCommerce\Templates\Emails
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email );

// Instructions en premier, comme sur la maquette.
watsons_mail_intro( $order, "Venez la récupérer au comptoir du Watson's Pub.", 'Bonne nouvelle : votre commande vous attend. Pensez à présenter votre numéro de commande.' );
watsons_mail_instructions_retrait( $order, "Votre commande vous attend au comptoir du Watson's Pub, 17 rue Saint-Front à Périgueux, avec votre numéro de commande." );
watsons_mail_recapitulatif( $order );

do_action( 'woocommerce_email_footer', $email );