<?php
/**
 * US-8.2 : En-tête de tous les e-mails (logo + titre), comme sur la maquette.
 *
 * Le logo et sa largeur sont réglés dans WooCommerce > Réglages > E-mails.
 *
 * @package WooCommerce\Templates\Emails
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

$logo         = get_option( 'woocommerce_email_header_image' );
$largeur_logo = absint( get_option( 'woocommerce_email_header_image_width', 120 ) );
$largeur_logo = $largeur_logo ? $largeur_logo : 120;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></title>
	<?php // Oswald et Lato, comme sur le site (si la messagerie ne les charge pas : Arial). ?>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&amp;family=Oswald:wght@400&amp;display=swap" rel="stylesheet">
</head>
<body class="mail-fond" style="margin: 0; padding: 0;">
	<table class="mail-fond" role="presentation" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="mail-fond__cellule" align="center">
				<table class="mail" role="presentation" width="600" cellpadding="0" cellspacing="0">
					<tr>
						<td class="mail__haut" align="center">
							<?php if ( $logo ) : ?>
								<img class="mail__logo" src="<?php echo esc_url( $logo ); ?>" width="<?php echo esc_attr( $largeur_logo ); ?>" alt="Watson's Pub">
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td class="mail__corps">
							<?php if ( ! empty( $email_heading ) ) : ?>
								<h1 class="mail__titre"><?php echo esc_html( $email_heading ); ?></h1>
							<?php endif; ?>