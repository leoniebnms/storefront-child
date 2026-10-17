<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Polices de la maquette : Oswald (menu, titres) et Lato (texte).
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'watsons-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Oswald:wght@300;400&display=swap',
		array(),
		null
	);
} );

/**
 * Modifications de la structure de Storefront.
 */
add_action( 'init', function () {
	// En-tête sur une seule ligne : logo, menu, icônes sociales.
	remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_container_close', 41 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
	remove_action( 'storefront_header', 'storefront_header_cart', 60 );
	remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );

	add_action( 'storefront_header', 'watsons_social_icons', 55 );
	add_action( 'storefront_header', 'storefront_header_container_close', 69 );

	// Mobile : retire la barre d'icônes collée en bas de l'écran.
	remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999 );

	// Pied de page : on retire les widgets et "Built with WooCommerce" de Storefront.
	remove_action( 'storefront_footer', 'storefront_footer_widgets', 10 );
	remove_action( 'storefront_footer', 'storefront_credit', 20 );

	// Et on met ceux de la maquette.
	add_action( 'storefront_before_footer', 'watsons_footer_contact', 10 );
	add_action( 'storefront_footer', 'watsons_footer_bas', 20 );

	// Pour retirer le fil d'Ariane (Accueil > Shop).
	remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );

} );

/**
 * Icônes Facebook, Instagram et TripAdvisor à droite du menu.
 * Remplacer les # par les vraies adresses.
 */
function watsons_social_icons() {
	$liens = array(
		'Facebook'    => array( '#', '<path d="M13.5 8H15V5.2c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.4-3.7 3.9V11H6.7v3.1h2.4V21h3v-6.9h2.4l.4-3.1h-2.8V9.3c0-.9.3-1.3 1.4-1.3z"/>' ),
		'Instagram'   => array( '#', '<path d="M12 7.4A4.6 4.6 0 1 0 12 16.6 4.6 4.6 0 0 0 12 7.4zm0 7.5a2.9 2.9 0 1 1 0-5.8 2.9 2.9 0 0 1 0 5.8zM16.8 6.1a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2zM12 4.6c2.4 0 2.7 0 3.6.1 2.4.1 3.6 1.3 3.7 3.7.1.9.1 1.2.1 3.6s0 2.7-.1 3.6c-.1 2.4-1.3 3.6-3.7 3.7-.9.1-1.2.1-3.6.1s-2.7 0-3.6-.1c-2.4-.1-3.6-1.3-3.7-3.7-.1-.9-.1-1.2-.1-3.6s0-2.7.1-3.6C4.8 6 5.9 4.8 8.4 4.7c.9-.1 1.2-.1 3.6-.1z"/>' ),
		'TripAdvisor' => array( '#', '<path d="M7.5 9a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm0 5.4a1.9 1.9 0 1 1 0-3.8 1.9 1.9 0 0 1 0 3.8zm9-5.4a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm0 5.4a1.9 1.9 0 1 1 0-3.8 1.9 1.9 0 0 1 0 3.8zM12 7.2c-1.9 0-3.7.4-5 1.2h10c-1.3-.8-3.1-1.2-5-1.2z"/>' ),
	);

	echo '<div class="watsons-social">';
	foreach ( $liens as $nom => $lien ) {
		printf(
			'<a href="%1$s" aria-label="%2$s" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true">%3$s</svg></a>',
			esc_url( $lien[0] ),
			esc_attr( $nom ),
			$lien[1]
		);
	}
	echo '</div>';
}

/**
 * Pied de page, partie 1 : le bloc "Pour nous contacter" sur photo.
 * La photo se met dans le thème enfant : images/footer-contact.jpg
 */
function watsons_footer_contact() {
	$photo = '';
	if ( file_exists( get_stylesheet_directory() . '/images/footer-contact.jpg' ) ) {
		$photo = get_stylesheet_directory_uri() . '/images/footer-contact.jpg';
	}
	?>
	<section class="watsons-contact"<?php if ( $photo ) : ?> style="--photo-contact: url('<?php echo esc_url( $photo ); ?>');"<?php endif; ?>>
		<div class="watsons-contact__inner">
			<span class="watsons-sep" aria-hidden="true"></span>
			<h2 class="watsons-contact__titre">Pour nous contacter</h2>
			<p class="watsons-contact__texte">N'hésitez pas à nous contacter pour toute demande particulière. Le Watson's Pub est à votre écoute.</p>
			<span class="watsons-sep" aria-hidden="true"></span>

			<a class="watsons-contact__mail" href="mailto:contact@watsons-pub.fr">contact@watsons-pub.fr</a>
			<a class="watsons-contact__tel" href="tel:+33553459524">05 53 45 95 24</a>
			<p class="watsons-contact__adresse">17 rue Saint-Front 24000 Périgueux</p>

			<a class="watsons-contact__bouton" href="https://www.google.com/maps/search/?api=1&amp;query=17+rue+Saint-Front+24000+P%C3%A9rigueux" target="_blank" rel="noopener">
				Venir au Watson's Pub
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 11.5 21 3l-8.5 19-2.3-8.2L2 11.5z"/></svg>
			</a>
		</div>
	</section>
	<?php
}

/**
 * Pied de page, partie 2 : menu du bas et copyright.
 * Reprend le menu principal (le bouton COMMANDER est caché en CSS).
 */
function watsons_footer_bas() {
	?>
	<div class="watsons-footer-bas">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'container_class' => 'watsons-footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
		<p class="watsons-copyright">
			<?php echo esc_html( date( 'Y' ) ); ?> - Tous droits réservés Watson's Pub -
			<a href="#">Mentions légales</a> -
			<a href="#">Politique de confidentialité</a>
			<span class="watsons-copyright__sep">|</span>
			Réalisation : Léonie Beau-Nimis
		</p>
	</div>
	<?php
}

/**
 * US-2.3 : AFficher la carte du Watson's Pub.
 */
function watsons_carte( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'titre'  => 'h2',
			'active' => '',
		)
	);

	$balise_titre = ( 'h1' === $args['titre'] ) ? 'h1' : 'h2';
	$active       = sanitize_title( $args['active'] );

	// Toutes les catégories qui ont des produits, sauf "Uncategorized".
	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);

	if ( is_wp_error( $categories ) ) {
		$categories = array();
	}

	// Dans l'ordre choisi dans Produits > Catégories (glisser-déposer).
	usort(
		$categories,
		function ( $a, $b ) {
			return (int) get_term_meta( $a->term_id, 'order', true ) - (int) get_term_meta( $b->term_id, 'order', true );
		}
	);

	ob_start();
	?>
	<section id="carte" class="carte">

		<<?php echo $balise_titre; ?> class="carte__titre">La carte</<?php echo $balise_titre; ?>>

		<?php /* Les filtres : des boutons qui affichent ou masquent les catégories */ ?>
		<div class="carte-filtres" role="group" aria-label="Filtrer la carte">
			<button type="button" class="carte-filtres__lien<?php echo $active ? '' : ' is-active'; ?>"
				data-filtre="tout" aria-pressed="<?php echo $active ? 'false' : 'true'; ?>">Tout</button>

			<?php foreach ( $categories as $categorie ) : ?>
				<?php $est_active = ( $active === $categorie->slug ); ?>
				<button type="button" class="carte-filtres__lien<?php echo $est_active ? ' is-active' : ''; ?>"
					data-filtre="<?php echo esc_attr( $categorie->slug ); ?>"
					aria-pressed="<?php echo $est_active ? 'true' : 'false'; ?>">
					<?php echo esc_html( $categorie->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php /* Une section par catégorie, avec ses produits */ ?>
		<div class="carte-categories">
			<?php foreach ( $categories as $categorie ) : ?>
				<?php
				$produits = wc_get_products(
					array(
						'category' => array( $categorie->slug ),
						'status'   => 'publish',
						'limit'    => -1,
						'orderby'  => 'menu_order',
						'order'    => 'ASC',
					)
				);

				if ( empty( $produits ) ) {
					continue;
				}

				$cachee = ( $active && $active !== $categorie->slug );
				?>

				<div class="carte-categorie carte-categorie--<?php echo esc_attr( $categorie->slug ); ?>"
					data-categorie="<?php echo esc_attr( $categorie->slug ); ?>"<?php echo $cachee ? ' hidden' : ''; ?>>

					<h3 class="carte-categorie__titre"><?php echo esc_html( $categorie->name ); ?></h3>

					<?php foreach ( $produits as $produit ) : ?>
						<article class="carte-produit">
							<h4 class="carte-produit__nom">
								<a href="<?php echo esc_url( $produit->get_permalink() ); ?>">
									<?php echo esc_html( $produit->get_name() ); ?>
								</a>
							</h4>

							<?php if ( $produit->get_short_description() ) : ?>
								<div class="carte-produit__desc">
									<?php echo wp_kses_post( $produit->get_short_description() ); ?>
								</div>
							<?php endif; ?>

							<span class="carte-produit__prix"><?php echo wp_kses_post( $produit->get_price_html() ); ?></span>

							<a class="button carte-produit__bouton" href="<?php echo esc_url( $produit->get_permalink() ); ?>">
								Commander
							</a>
						</article>
					<?php endforeach; ?>
				</div>

			<?php endforeach; ?>
		</div>

	</section>

	<script>
	/* Filtres de la carte : on masque les catégories qui ne correspondent pas au bouton cliqué. */
	( function () {
		var carte = document.currentScript.previousElementSibling;
		var boutons = carte.querySelectorAll( '[data-filtre]' );
		var sections = carte.querySelectorAll( '[data-categorie]' );

		boutons.forEach( function ( bouton ) {
			bouton.addEventListener( 'click', function () {
				var filtre = bouton.dataset.filtre;

				boutons.forEach( function ( b ) {
					b.classList.toggle( 'is-active', b === bouton );
					b.setAttribute( 'aria-pressed', b === bouton ? 'true' : 'false' );
				} );

				sections.forEach( function ( section ) {
					section.hidden = ( filtre !== 'tout' && section.dataset.categorie !== filtre );
				} );
			} );
		} );
	} )();
	</script>
	<?php
	return ob_get_clean();
}

/**
 * Code court [watsons_carte] : à mettre dans le contenu de la page d'accueil.
 */
add_shortcode(
	'watsons_carte',
	function () {
		return watsons_carte();
	}
);

/** US-2.4 : fiche produit comme sur la maquette. */
add_action( 'init', function () {
	// Ordre de la maquette : titre, description, prix, puis ajout au panier.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );

	// Pas de catégorie ni d'UGS sous le bouton.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	// Pas d'onglets, de ventes croisées ni de produits apparentés.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	// Storefront : liens produit précédent/suivant et barre collante en bas de l'écran.
	remove_action( 'woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 30 );
	remove_action( 'storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999 );
} );

// Lien "Retour à la carte" au-dessus du produit : il ramène à la carte de la page d'accueil.
add_action( 'woocommerce_before_single_product', function () {
	printf(
		'<a class="retour-carte" href="%s"><svg viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour à la carte</a>',
		esc_url( home_url( '/#carte' ) )
	);
}, 5 );

// Libellé "Quantité" au-dessus du champ de quantité.
add_action( 'woocommerce_before_add_to_cart_quantity', function () {
	echo '<span class="produit-quantite__label">Quantité</span>';
} );

// Fiche produit : pas de loupe ni de zoom sur la photo.
add_action( 'after_setup_theme', function () {
	remove_theme_support( 'wc-product-gallery-zoom' );
	remove_theme_support( 'wc-product-gallery-lightbox' );
}, 100 );

// Pas de lien "Modifier" (raccourci admin de Storefront) sur les pages et les fiches produit.
add_action( 'init', function () {
	remove_action( 'storefront_page_after', 'storefront_edit_post_link', 30 );
	remove_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );
} );