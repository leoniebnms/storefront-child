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
 * Icônes Facebook, Instagram et TripAdvisor à droite du menu, puis l'icône Mon compte.
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
			'<a class="watsons-social__lien" href="%1$s" aria-label="%2$s" target="_blank" rel="noopener"><svg class="watsons-social__icone" viewBox="0 0 24 24" aria-hidden="true">%3$s</svg></a>',
			esc_url( $lien[0] ),
			esc_attr( $nom ),
			$lien[1]
		);
	}

	// US-7.2 : icône "Mon compte", en rouille pour la distinguer des réseaux sociaux.
	printf(
		'<a class="watsons-social__lien watsons-social__lien--compte" href="%s" aria-label="Mon compte"><svg class="watsons-social__icone" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8.5" r="3.6"/><path d="M5 20c.8-4 3.6-6 7-6s6.2 2 7 6z"/></svg></a>',
		esc_url( wc_get_page_permalink( 'myaccount' ) )
	);
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
				<svg class="watsons-contact__icone" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 11.5 21 3l-8.5 19-2.3-8.2L2 11.5z"/></svg>
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
			<a class="watsons-copyright__lien" href="#">Mentions légales</a> -
			<a class="watsons-copyright__lien" href="#">Politique de confidentialité</a>
			<span class="watsons-copyright__sep">|</span>
			Réalisation : Léonie Beau-Nimis
		</p>
	</div>
	<?php
}

/**
 * US-2.3 : Afficher la carte du Watson's Pub.
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
			<button type="button" class="carte-filtres__lien<?php echo $active ? '' : ' carte-filtres__lien--actif'; ?>"
				data-filtre="tout" aria-pressed="<?php echo $active ? 'false' : 'true'; ?>">Tout</button>

			<?php foreach ( $categories as $categorie ) : ?>
				<?php $est_active = ( $active === $categorie->slug ); ?>
				<button type="button" class="carte-filtres__lien<?php echo $est_active ? ' carte-filtres__lien--actif' : ''; ?>"
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
					b.classList.toggle( 'carte-filtres__lien--actif', b === bouton );
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
		'<a class="retour-carte" href="%s"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour à la carte</a>',
		esc_url( home_url( '/#carte' ) )
	);
}, 5 );

// Libellé "Quantité" au-dessus du champ de quantité.
add_action( 'woocommerce_before_add_to_cart_quantity', function () {
	echo '<span class="produit-quantite">Quantité</span>';
} );

// Fiche produit : pas de loupe ni de zoom sur la photo.
add_action( 'after_setup_theme', function () {
	remove_theme_support( 'wc-product-gallery-zoom' );
	remove_theme_support( 'wc-product-gallery-lightbox' );
}, 100 );

// Pas de lien "Modifier" (raccourci admin de Storefront) sur les pages et les fiches produit.
add_action( 'init', function () {
	remove_action( 'storefront_page', 'storefront_edit_post_link', 30 );
	remove_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );
} );

/** US-3.2 : sélecteur d'options comme sur la maquette */
/** Prix des produits variables sur la fiche produit : "À partir de 15,00 €" au lieu de "15,00 € – 19,00 €" et sur la carte : seulement le prix de départ, "15,00 €".*/
add_filter( 'woocommerce_variable_price_html', function ( $prix, $produit ) {
	$prix_min = wc_price( $produit->get_variation_price( 'min', true ) );

	if ( is_product() && get_queried_object_id() === $produit->get_id() ) {
		return 'À partir de ' . $prix_min;
	}

	return $prix_min;
}, 10, 2 );

/* US-3.1 : "En stock", "Stock faible", "Épuisé". Le seuil "faible" est celui réglé dans WooCommerce > Réglages > Produits > Inventaire. */
add_filter( 'woocommerce_get_availability_text', function ( $texte, $produit ) {
	if ( ! $produit->is_in_stock() ) {
		return 'Épuisé';
	}

	if ( $produit->managing_stock() ) {
		$quantite = (int) $produit->get_stock_quantity();

		if ( $quantite <= 0 ) {
			return 'Épuisé';
		}

		if ( $quantite <= wc_get_low_stock_amount( $produit ) ) {
			return 'Stock faible';
		}
	}

	return 'En stock';
}, 10, 2 );

/**
 * Cases à cocher "Ingrédients à retirer", sous les listes Sauce et Taille.
 * Les ingrédients viennent de l'attribut "Ingrédients à retirer" (slug : ingredients)
 * ajouté au produit dans l'admin.
 */
function watsons_ingredients_du_produit( $produit_id ) {
	$ingredients = wc_get_product_terms( $produit_id, 'pa_ingredients', array( 'fields' => 'names' ) );

	return is_wp_error( $ingredients ) ? array() : $ingredients;
}

add_action( 'woocommerce_after_variations_table', function () {
	global $product;

	$ingredients = watsons_ingredients_du_produit( $product->get_id() );

	if ( empty( $ingredients ) ) {
		return;
	}
	?>
	<fieldset class="ingredients-retirer">
		<legend class="ingredients-retirer__titre">Ingrédients à retirer</legend>
		<?php foreach ( $ingredients as $ingredient ) : ?>
			<label class="ingredients-retirer__option">
				<input class="ingredients-retirer__case" type="checkbox" name="ingredients_retires[]" value="<?php echo esc_attr( $ingredient ); ?>">
				<?php echo esc_html( $ingredient ); ?>
			</label>
		<?php endforeach; ?>
	</fieldset>
	<?php
} );

/**
 * Ligne "Prix sélectionné : 15,00 € - En stock", remplie par le JavaScript.
 */
add_action( 'woocommerce_before_single_variation', function () {
	echo '<p class="prix-selectionne" hidden></p>';
} );

/**
 * Les ingrédients cochés sont gardés dans le panier...
 */
add_filter( 'woocommerce_add_cart_item_data', function ( $donnees, $produit_id ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- formulaire d'ajout au panier de WooCommerce.
	if ( empty( $_POST['ingredients_retires'] ) ) {
		return $donnees;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$coches = array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['ingredients_retires'] ) );

	// On ne garde que les ingrédients qui existent vraiment pour ce produit.
	$coches = array_values( array_intersect( $coches, watsons_ingredients_du_produit( $produit_id ) ) );

	if ( $coches ) {
		$donnees['ingredients_retires'] = $coches;
	}

	return $donnees;
}, 10, 2 );

/**
 * ... affichés dans le panier ("Sans : Oignons, Salade")...
 */
add_filter( 'woocommerce_get_item_data', function ( $infos, $article ) {
	if ( ! empty( $article['ingredients_retires'] ) ) {
		$infos[] = array(
			'key'   => 'Sans',
			'value' => implode( ', ', $article['ingredients_retires'] ),
		);
	}

	return $infos;
}, 10, 2 );

/**
 * ... et enregistrés dans la commande, pour la cuisine.
 */
add_action( 'woocommerce_checkout_create_order_line_item', function ( $ligne, $cle, $article ) {
	if ( ! empty( $article['ingredients_retires'] ) ) {
		$ligne->add_meta_data( 'Sans', implode( ', ', $article['ingredients_retires'] ) );
	}
}, 10, 3 );

/**
 * JavaScript de la fiche produit : quand le visiteur choisit ses options,
 * on affiche "Prix sélectionné : ... - En stock" et on met à jour le prix du haut.
 */
add_action( 'wp_footer', function () {
	if ( ! is_product() ) {
		return;
	}
	?>
	<script>
	jQuery( function ( $ ) {
		// true : le prix du haut change aussi. false : il reste sur "À partir de..." comme sur la maquette.
		var majPrixDuHaut = true;

		var $formulaire = $( 'form.variations_form' );
		var $prix = $( '.summary p.price' ).first();
		var $ligne = $formulaire.find( '.prix-selectionne' );

		if ( ! $formulaire.length ) {
			return;
		}

		var prixDeDepart = $prix.html();

		$formulaire.on( 'found_variation', function ( evenement, variation ) {
			var prixChoisi = variation.price_html ? $( variation.price_html ).html() : prixDeDepart;
			var stock = $( '<div>' ).html( variation.availability_html ).text().trim() || 'En stock';
			var classe = ! variation.is_in_stock ? 'stock-statut--epuise' : ( stock === 'Stock faible' ? 'stock-statut--faible' : 'stock-statut--dispo' );

			if ( majPrixDuHaut && variation.price_html ) {
				$prix.html( prixChoisi );
			}

			$ligne
				.html( '<span class="stock-statut ' + classe + '">' + stock + '</span>' )
				.prop( 'hidden', false );
		} );

		$formulaire.on( 'reset_data hide_variation', function () {
			$prix.html( prixDeDepart );
			$ligne.prop( 'hidden', true ).empty();
		} );
	} );
	</script>
	<?php
} );

/** US-4.3 : afficher les champs personnalisés (SCF) sur la fiche produit, entre la description et le prix. */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;

	// Nom du champ dans SCF => titre affiché sur le site.
	$champs = array(
		'composition'             => 'Composition',
		'allergenes'              => 'Allergènes',
		'valeurs_nutritionnelles' => 'Valeurs nutritionnelles',
	);

	$blocs = '';

	foreach ( $champs as $nom => $titre ) {
		$valeur = trim( (string) get_post_meta( $product->get_id(), $nom, true ) );

		// Un champ vide n'est pas affiché.
		if ( '' === $valeur ) {
			continue;
		}

		$blocs .= sprintf(
			'<details class="infos-produit__bloc infos-produit__bloc--%1$s"><summary class="infos-produit__titre">%2$s</summary><p class="infos-produit__texte">%3$s</p></details>',
			esc_attr( $nom ),
			esc_html( $titre ),
			nl2br( esc_html( $valeur ) )
		);
	}

	// Aucun champ rempli (boissons, desserts...) : rien n'est affiché.
	if ( $blocs ) {
		echo '<div class="infos-produit">' . $blocs . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé ci-dessus.
	}
}, 22 );

/** US-5.3 : page Panier comme sur la maquette */

/**
 * Lien "Continuer ma commande" au-dessus du titre "Votre panier".
 * Même composant que "Retour à la carte" sur la fiche produit.
 */
add_action( 'storefront_page_before', function () {
	if ( ! is_cart() ) {
		return;
	}

	printf(
		'<a class="retour-carte" href="%s"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Continuer ma commande</a>',
		esc_url( home_url( '/#carte' ) )
	);
} );

/**
 * Boutons − et + autour du champ Quantité, et mise à jour automatique du panier
 * (il n'y a pas de bouton "Mettre à jour le panier" sur la maquette).
 */
add_action( 'wp_footer', function () {
	if ( ! is_cart() ) {
		return;
	}
	?>
	<script>
	jQuery( function ( $ ) {
		var minuteur;

		// Ajoute les boutons − et + (à refaire après chaque mise à jour du panier).
		function ajouterBoutons() {
			$( '.woocommerce-cart-form .quantity' ).each( function () {
				var $bloc = $( this );
				var $champ = $bloc.find( '.qty' );

				if ( $bloc.hasClass( 'quantite' ) || ! $champ.length || $champ.attr( 'type' ) === 'hidden' ) {
					return;
				}

				$bloc.addClass( 'quantite' );
				$champ.addClass( 'quantite__champ' );
				$( '<button type="button" class="quantite__bouton quantite__bouton--moins" aria-label="Enlever un">−</button>' ).insertBefore( $champ );
				$( '<button type="button" class="quantite__bouton quantite__bouton--plus" aria-label="Ajouter un">+</button>' ).insertAfter( $champ );
			} );
		}

		// Clique (en caché) sur le bouton "Mettre à jour le panier" de WooCommerce.
		function mettreAJourPanier() {
			clearTimeout( minuteur );
			minuteur = setTimeout( function () {
				$( '.woocommerce-cart-form [name="update_cart"]' ).prop( 'disabled', false ).trigger( 'click' );
			}, 500 );
		}

		$( document.body ).on( 'click', '.quantite__bouton', function () {
			var $champ = $( this ).siblings( '.qty' );
			var quantite = parseInt( $champ.val(), 10 ) || 1;
			var maximum = parseInt( $champ.attr( 'max' ), 10 );

			quantite += $( this ).hasClass( 'quantite__bouton--plus' ) ? 1 : -1;

			// Minimum 1 : pour retirer un produit, on utilise la croix.
			if ( quantite < 1 ) {
				quantite = 1;
			}
			if ( maximum > 0 && quantite > maximum ) {
				quantite = maximum;
			}

			$champ.val( quantite ).trigger( 'change' );
			mettreAJourPanier();
		} );

		// Si le visiteur tape la quantité au clavier.
		$( document.body ).on( 'input', '.woocommerce-cart-form .qty', mettreAJourPanier );

		ajouterBoutons();
		$( document.body ).on( 'updated_wc_div', ajouterBoutons );
	} );
	</script>
	<?php
} );

/** Bouton panier sticky : toujours visible en bas à droite de l'écran, pour consulter son panier à tout moment. */
add_action( 'wp_footer', function () {
	// Pas besoin du bouton quand on est déjà sur le panier ou la commande.
	if ( ! function_exists( 'WC' ) || is_cart() || is_checkout() ) {
		return;
	}

	$nombre = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	?>
	<a class="panier-flottant" href="<?php echo esc_url( wc_get_cart_url() ); ?>"
		aria-label="<?php echo esc_attr( sprintf( 'Voir mon panier (%d article%s)', $nombre, $nombre > 1 ? 's' : '' ) ); ?>">
		<svg class="panier-flottant__icone" viewBox="4 2.5 16 19" aria-hidden="true">
			<path d="M5.5 8h13l-1.2 12.5H6.7L5.5 8z"/>
			<path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>
		</svg>
		<?php if ( $nombre > 0 ) : ?>
			<span class="panier-flottant__nombre"><?php echo esc_html( $nombre ); ?></span>
		<?php endif; ?>
	</a>
	<?php
} );

/** US-6.1 : texte "Instructions pour le retrait", modifiable dans WooCommerce > Réglages > Général (en bas de la page). */
add_filter( 'woocommerce_general_settings', function ( $reglages ) {
	$reglages[] = array(
		'title' => 'Click & Collect',
		'type'  => 'title',
		'desc'  => 'Texte affiché sur la page de commande quand le client choisit le retrait en boutique.',
		'id'    => 'watsons_retrait',
	);

	$reglages[] = array(
		'title'    => 'Instructions pour le retrait',
		'id'       => 'watsons_instructions_retrait',
		'type'     => 'textarea',
		'css'      => 'min-width: 400px; height: 120px;',
		'default'  => "Votre commande sera prête 20 minutes après sa validation.\nPrésentez-vous au comptoir du Watson's Pub, 17 rue Saint-Front à Périgueux, avec votre numéro de commande.",
		'desc_tip' => 'Une ligne par paragraphe.',
	);

	$reglages[] = array(
		'type' => 'sectionend',
		'id'   => 'watsons_retrait',
	);

	return $reglages;
} );

// Le client a-t-il choisi "Retrait en boutique" ?
function watsons_retrait_choisi() {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return false;
	}

	foreach ( (array) WC()->session->get( 'chosen_shipping_methods' ) as $mode ) {
		if ( 0 === strpos( (string) $mode, 'local_pickup' ) || 0 === strpos( (string) $mode, 'pickup_location' ) ) {
			return true;
		}
	}

	return false;
}

// Le bloc "Instructions pour le retrait" (caché si le client choisit la livraison).
function watsons_bloc_instructions_retrait() {
	$texte = trim( (string) get_option( 'watsons_instructions_retrait', '' ) );

	if ( '' === $texte ) {
		return '<div class="instructions-retrait" hidden></div>';
	}

	return sprintf(
		'<div class="instructions-retrait"%s><h3 class="instructions-retrait__titre">Instructions pour le retrait</h3><div class="instructions-retrait__texte">%s</div></div>',
		watsons_retrait_choisi() ? '' : ' hidden',
		wpautop( esc_html( $texte ) )
	);
}

/** US-6.2 : le bloc s'affiche sur la page de commande, sous les informations du client... */
add_action( 'woocommerce_after_order_notes', function () {
	echo watsons_bloc_instructions_retrait(); // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé.
} );

// ... et se met à jour tout seul quand la page de commande se recharge.
add_filter( 'woocommerce_update_order_review_fragments', function ( $fragments ) {
	$fragments['.instructions-retrait'] = watsons_bloc_instructions_retrait();
	return $fragments;
} );

/** US-6.2 : page Commande comme la maquette Finalisation. */
add_action( 'init', function () {
	// Pas de bandeau "Avez-vous un code promo ?" (il reviendra avec les promotions, IT 10).
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

	// Le choix du paiement passe dans le cadre de gauche, sous les informations du client.
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
	add_action( 'woocommerce_checkout_shipping', 'woocommerce_checkout_payment', 20 );
} );

// Lien "Retour au panier" au-dessus du titre (le mode de réception se change dans le panier).
add_action( 'storefront_page_before', function () {
	if ( ! is_checkout() || is_wc_endpoint_url() ) {
		return;
	}

	printf(
		'<a class="retour-carte" href="%s"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour au panier</a>',
		esc_url( wc_get_cart_url() )
	);
} );

// En haut du cadre : "Le Watson's Pub" et le mode de réception choisi.
add_action( 'woocommerce_checkout_billing', function () {
	printf(
		'<div class="commande-entete"><p class="commande-entete__titre">Le Watson\'s Pub</p><p class="commande-entete__mode">%s</p></div>',
		esc_html( watsons_retrait_choisi() ? 'Click & Collect' : 'Livraison à domicile' )
	);
}, 5 );

// "Détails de facturation" devient "Informations personnelles".
add_filter( 'gettext', function ( $traduction, $texte, $domaine ) {
	if ( ! is_admin() && 'woocommerce' === $domaine && in_array( $texte, array( 'Billing details', 'Billing &amp; Shipping' ), true ) ) {
		return 'Informations personnelles';
	}

	return $traduction;
}, 10, 3 );

// Les champs du formulaire, dans l'ordre de la maquette : Nom, Prénom, E-mail, Rue, Ville, Code postal, Téléphone.
add_filter( 'woocommerce_checkout_fields', function ( $champs ) {
	// Pas de société ni de complément d'adresse sur la maquette.
	unset( $champs['billing']['billing_company'], $champs['billing']['billing_address_2'] );

	$ordre = array(
		'billing_last_name'  => 10,
		'billing_first_name' => 11,
		'billing_email'      => 12,
		'billing_address_1'  => 13,
		'billing_city'       => 14,
		'billing_postcode'   => 15,
		'billing_phone'      => 16,
	);

	foreach ( $ordre as $nom => $priorite ) {
		if ( isset( $champs['billing'][ $nom ] ) ) {
			$champs['billing'][ $nom ]['priority'] = $priorite;
			$champs['billing'][ $nom ]['class']    = array( 'form-row-wide' );
		}
	}

	// Retrait en boutique : l'adresse n'est pas obligatoire (elle l'est pour la livraison).
	if ( watsons_retrait_choisi() ) {
		foreach ( array( 'billing_address_1', 'billing_city', 'billing_postcode', 'billing_state' ) as $nom ) {
			if ( isset( $champs['billing'][ $nom ] ) ) {
				$champs['billing'][ $nom ]['required'] = false;
			}
		}
	}

	// Exemple dans le champ Notes, comme sur la maquette.
	if ( isset( $champs['order']['order_comments'] ) ) {
		$champs['order']['order_comments']['placeholder'] = "Ex. : code de l'immeuble, étage…";
	}

	return $champs;
} );

// Pas de "Expédier à une adresse différente ?" : on utilise l'adresse du formulaire.
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

// Titre au-dessus des moyens de paiement.
add_action( 'woocommerce_review_order_before_payment', function () {
	echo '<h3 class="commande-paiement__titre">Choisissez votre moyen de paiement</h3>';
} );

// Bouton "Payer 15,00 €" au lieu de "Commander".
add_filter( 'woocommerce_order_button_text', function ( $texte ) {
	if ( ! WC()->cart || is_wc_endpoint_url( 'order-pay' ) ) {
		return $texte;
	}

	return 'Payer ' . html_entity_decode( wp_strip_all_tags( WC()->cart->get_total() ), ENT_QUOTES, 'UTF-8' );
} );

// Titre du récapitulatif à droite, avec le total juste en dessous.
add_action( 'woocommerce_checkout_order_review', function () {
	echo '<h2 class="commande-panier__titre">Votre panier</h2>';
	printf(
		'<p class="commande-panier__total"><span>Total (TTC)</span><span>%s</span></p>',
		wp_kses_post( WC()->cart->get_total() )
	);
}, 5 );

// Dans le récapitulatif : "Retrait en boutique | Gratuit" ou "Livraison à domicile | 6,90 €".
add_filter( 'woocommerce_cart_shipping_method_full_label', function ( $libelle, $mode ) {
	if ( ! is_checkout() ) {
		return $libelle;
	}

	$cout = (float) $mode->get_cost();
	if ( WC()->cart->display_prices_including_tax() ) {
		$cout += (float) $mode->get_shipping_tax();
	}

	return sprintf(
		'<span class="commande-panier__mode">%s</span><span>%s</span>',
		esc_html( $mode->get_label() ),
		$cout > 0 ? wc_price( $cout ) : 'Gratuit'
	);
}, 10, 2 );

/** US-6.2 : page "Commande reçue" (le contenu est dans woocommerce/checkout/thankyou.php). */
add_filter( 'woocommerce_endpoint_order-received_title', function () {
	return 'Merci pour votre commande !';
} );

/** US-7 : compte client */

// Un champ souligné (libellé gris au-dessus), utilisé dans tous les formulaires du compte.
function watsons_champ_compte( $nom, $libelle, $valeur = '', $type = 'text', $autocomplete = '', $id = '' ) {
	$id = $id ? $id : $nom;

	printf(
		'<p class="compte-champ"><label class="compte-champ__libelle" for="%1$s">%2$s</label><input class="compte-champ__saisie input-text" type="%3$s" name="%4$s" id="%1$s" value="%5$s" autocomplete="%6$s"></p>',
		esc_attr( $id ),
		esc_html( $libelle ),
		esc_attr( $type ),
		esc_attr( $nom ),
		esc_attr( $valeur ),
		esc_attr( $autocomplete )
	);
}

// Pas de menu à gauche (Tableau de bord, Commandes...) : tout est sur une seule page, comme la maquette.
add_action( 'init', function () {
	remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );
} );

// Titres des pages : "Me connecter / M'inscrire" et "Recuperer mon mot de passe" (sans accent : la police Tall Dark n'en a pas).
add_filter( 'the_title', function ( $titre, $id = 0 ) {
	if ( is_admin() || ! in_the_loop() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return $titre;
	}

	if ( ! is_user_logged_in() && ! is_wc_endpoint_url() && (int) $id === (int) wc_get_page_id( 'myaccount' ) ) {
		return 'Me connecter / M\'inscrire';
	}

	return $titre;
}, 10, 2 );

add_filter( 'woocommerce_endpoint_lost-password_title', function () {
	return 'Recuperer mon mot de passe';
} );

// Classes sur la page, pour placer le titre comme sur les maquettes (à gauche pour la connexion, centré sinon).
add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		$classes[] = is_wc_endpoint_url( 'lost-password' ) ? 'compte-page--mdp' : 'compte-page--connexion';
	}

	return $classes;
} );

/**
 * Formulaire "Informations personnelles" : enregistrement.
 */
add_action( 'template_redirect', function () {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- le nonce est vérifié juste en dessous.
	if ( empty( $_POST['watsons_infos_perso'] ) || ! is_user_logged_in() ) {
		return;
	}

	$nonce = isset( $_POST['watsons-infos-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['watsons-infos-nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'watsons_infos_perso' ) ) {
		return;
	}

	$valeur = function ( $nom ) {
		return isset( $_POST[ $nom ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nom ] ) ) : '';
	};
	// phpcs:enable

	$civilite    = in_array( $valeur( 'civilite' ), array( 'mme', 'm' ), true ) ? $valeur( 'civilite' ) : '';
	$nom         = $valeur( 'billing_last_name' );
	$prenom      = $valeur( 'billing_first_name' );
	$telephone   = $valeur( 'billing_phone' );
	$rue         = $valeur( 'billing_address_1' );
	$code_postal = $valeur( 'billing_postcode' );
	$ville       = $valeur( 'billing_city' );

	// Vérifications.
	if ( '' === $nom || '' === $prenom ) {
		wc_add_notice( 'Le nom et le prénom sont obligatoires.', 'error' );
	}
	if ( '' !== $telephone && ! WC_Validation::is_phone( $telephone ) ) {
		wc_add_notice( 'Le numéro de téléphone n\'est pas valide.', 'error' );
	}
	if ( '' !== $code_postal && ! WC_Validation::is_postcode( $code_postal, 'FR' ) ) {
		wc_add_notice( 'Le code postal n\'est pas valide.', 'error' );
	}
	if ( wc_notice_count( 'error' ) > 0 ) {
		return;
	}

	// Les infos sont enregistrées comme adresse de facturation : la page Commande sera déjà remplie.
	$client = new WC_Customer( get_current_user_id() );
	$client->set_first_name( $prenom );
	$client->set_last_name( $nom );
	$client->set_billing_first_name( $prenom );
	$client->set_billing_last_name( $nom );
	$client->set_billing_phone( $telephone );
	$client->set_billing_address_1( $rue );
	$client->set_billing_postcode( $code_postal );
	$client->set_billing_city( $ville );
	if ( ! $client->get_billing_country() ) {
		$client->set_billing_country( 'FR' );
	}
	$client->save();

	update_user_meta( get_current_user_id(), 'watsons_civilite', $civilite );

	wc_add_notice( 'Vos informations personnelles ont été enregistrées.' );
	wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
	exit;
} );

// Formulaire "Changer mon identifiant et mon mot de passe" : géré par WooCommerce,
// seul l'e-mail est obligatoire (le nom et le prénom sont dans l'autre formulaire).
add_filter( 'woocommerce_save_account_details_required_fields', function () {
	return array( 'account_email' => 'E-mail' );
} );

// US-7.1 : la civilité est visible par l'administrateur dans Comptes > Profil (partie Facturation).
add_filter( 'woocommerce_customer_meta_fields', function ( $champs ) {
	$champs['billing']['fields'] = array(
		'watsons_civilite' => array(
			'label'   => 'Civilité',
			'type'    => 'select',
			'options' => array(
				''    => '—',
				'mme' => 'Mme',
				'm'   => 'M.',
			),
		),
	) + $champs['billing']['fields'];

	return $champs;
} );

/** US-8 : e-mails aux couleurs du Watson's Pub (les templates sont dans woocommerce/emails/) */

// La commande est-elle en retrait en boutique ? (comme watsons_retrait_choisi(), mais pour une commande déjà passée)
function watsons_commande_retrait( $commande ) {
	if ( ! $commande instanceof WC_Order ) {
		return false;
	}

	foreach ( $commande->get_shipping_methods() as $mode ) {
		if ( in_array( $mode->get_method_id(), array( 'local_pickup', 'pickup_location' ), true ) ) {
			return true;
		}
	}

	return false;
}

// Un cadre du mail : titre + contenu. $rouille = true pour le cadre "Instructions pour le retrait".
function watsons_mail_cadre( $titre, $contenu, $rouille = false ) {
	printf(
		'<table class="mail-cadre%1$s" role="presentation" width="100%%" cellpadding="0" cellspacing="0"><tr><td class="mail-cadre__cellule"><h2 class="mail-cadre__titre">%2$s</h2>%3$s</td></tr></table>',
		$rouille ? ' mail-cadre--rouille' : '',
		esc_html( $titre ),
		$contenu // phpcs:ignore WordPress.Security.EscapeOutput -- déjà échappé par les fonctions ci-dessous.
	);
}

// Début du mail : sous-titre, numéro de commande et "Bonjour Léonie,".
function watsons_mail_intro( $commande, $sous_titre, $texte ) {
	printf(
		'<p class="mail__sous-titre">%1$s</p><table class="mail__numero" role="presentation" align="center" cellpadding="0" cellspacing="0"><tr><td class="mail__numero-texte">N° %2$s</td></tr></table><p class="mail__texte">Bonjour %3$s,<br>%4$s</p>',
		esc_html( $sous_titre ),
		esc_html( $commande->get_order_number() ),
		esc_html( $commande->get_billing_first_name() ),
		esc_html( $texte )
	);
}

// Cadre "Récapitulatif de votre panier" : produits, réception, paiement et total.
function watsons_mail_recapitulatif( $commande ) {
	$devise = array( 'currency' => $commande->get_currency() );
	$lignes = '';

	foreach ( $commande->get_items() as $article ) {
		$quantite = $article->get_quantity();
		$nom      = ( $quantite > 1 ? $quantite . ' × ' : '' ) . $article->get_name();

		// Ingrédients retirés (US-3) : "Sans : Oignons".
		$options = wc_display_item_meta(
			$article,
			array(
				'before'       => '',
				'after'        => '',
				'separator'    => ' – ',
				'label_before' => '',
				'label_after'  => ' : ',
				'echo'         => false,
				'autop'        => false,
			)
		);

		$lignes .= sprintf(
			'<tr class="mail-recap__produit"><td class="mail-recap__nom">%1$s%2$s</td><td class="mail-recap__prix">%3$s</td></tr>',
			esc_html( $nom ),
			$options ? '<span class="mail-recap__options">' . wp_strip_all_tags( $options ) . '</span>' : '',
			wc_price( $commande->get_line_subtotal( $article, true ), $devise )
		);
	}

	// US-10.3 : code promo (les produits sont affichés avant la réduction).
	foreach ( $commande->get_coupon_codes() as $code_promo ) {
		$lignes .= sprintf(
			'<tr class="mail-recap__info mail-recap__info--premiere"><td>Code %1$s</td><td class="mail-recap__prix">−%2$s</td></tr>',
			esc_html( strtoupper( $code_promo ) ),
			wc_price( (float) $commande->get_discount_total() + (float) $commande->get_discount_tax(), $devise )
		);
	}

	// Réception : "Retrait en boutique – Gratuit" ou "Livraison à domicile – 6,90 €".
	if ( $commande->get_shipping_method() ) {
		$frais   = (float) $commande->get_shipping_total() + (float) $commande->get_shipping_tax();
		$lignes .= sprintf(
			'<tr class="mail-recap__info mail-recap__info--premiere"><td>%1$s</td><td class="mail-recap__prix">%2$s</td></tr>',
			esc_html( $commande->get_shipping_method() ),
			$frais > 0 ? wc_price( $frais, $devise ) : 'Gratuit'
		);
	}

	$lignes .= sprintf(
		'<tr class="mail-recap__info"><td>Mode de paiement</td><td class="mail-recap__prix">%s</td></tr>',
		esc_html( $commande->get_payment_method_title() )
	);

	$lignes .= sprintf(
		'<tr class="mail-recap__total"><td>Total (TTC)</td><td class="mail-recap__prix">%s</td></tr>',
		wc_price( $commande->get_total(), $devise )
	);

	watsons_mail_cadre(
		'Récapitulatif de votre panier',
		'<table class="mail-recap" role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $lignes . '</table>'
	);
}

// Cadre "Nos coordonnées bancaires" (paiement par virement), avec le compte réglé dans Paiements > Virement.
function watsons_mail_coordonnees_bancaires( $commande ) {
	$contenu = '';

	foreach ( (array) get_option( 'woocommerce_bacs_accounts', array() ) as $compte ) {
		$contenu .= '<p class="mail-cadre__texte">' . esc_html( isset( $compte['account_name'] ) ? $compte['account_name'] : "Watson's Pub" );
		if ( ! empty( $compte['iban'] ) ) {
			$contenu .= '<br>IBAN : ' . esc_html( $compte['iban'] );
		}
		if ( ! empty( $compte['bic'] ) ) {
			$contenu .= '<br>BIC : ' . esc_html( $compte['bic'] );
		}
		$contenu .= '</p>';
	}

	$contenu .= '<p class="mail-cadre__texte">Référence à indiquer : ' . esc_html( $commande->get_order_number() ) . '</p>';

	watsons_mail_cadre( 'Nos coordonnées bancaires', $contenu );
}

// Cadre "Adresse de livraison".
function watsons_mail_adresse( $commande ) {
	$adresse = $commande->get_formatted_shipping_address();
	if ( ! $adresse ) {
		$adresse = $commande->get_formatted_billing_address();
	}

	watsons_mail_cadre( 'Adresse de livraison', '<p class="mail-cadre__texte">' . wp_kses( $adresse, array( 'br' => array() ) ) . '</p>' );
}

// US-8.3 : cadre rouille "Instructions pour le retrait".
// $texte = les lignes à afficher (si vide : le texte par défaut de l'IT6). Si le client paie au retrait, on lui rappelle le montant à régler.
function watsons_mail_instructions_retrait( $commande, $texte ) {
	$contenu = '';

	if ( '' === trim( (string) $texte ) ) {
		$texte = "Votre commande sera prête 20 minutes après sa validation.\nPrésentez-vous au comptoir du Watson's Pub, 17 rue Saint-Front à Périgueux, avec votre numéro de commande.";
	}

	// Une ligne du texte = un paragraphe.
	foreach ( preg_split( '/\R+/', trim( $texte ) ) as $ligne ) {
		$contenu .= '<p class="mail-cadre__texte">' . esc_html( $ligne ) . '</p>';
	}

	if ( 'cod' === $commande->get_payment_method() ) {
		$contenu .= '<p class="mail-cadre__texte">Le règlement de ' . wc_price( $commande->get_total(), array( 'currency' => $commande->get_currency() ) ) . ' se fait au comptoir, au moment du retrait.</p>';
	}

	watsons_mail_cadre( 'Instructions pour le retrait', $contenu, true );
}

/**
 * US-8.2 / US-8.4 : le style des e-mails est à la fin de style.css,
 * entre les commentaires "DEBUT E-MAILS" et "FIN E-MAILS".
 */
add_filter( 'woocommerce_email_styles', function ( $css ) {
	$feuille = file_get_contents( get_stylesheet_directory() . '/style.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier du thème.
	$debut   = strpos( $feuille, '/* DEBUT E-MAILS' );
	$fin     = strpos( $feuille, '/* FIN E-MAILS */' );

	if ( false !== $debut && false !== $fin ) {
		$css .= substr( $feuille, $debut, $fin - $debut );
	}

	return $css;
}, 20 );


/** US-9.2 : statut "Prête" pour les commandes à retirer en boutique */
// 1. Le nouveau statut "Prête" (wc-prete).
add_action( 'init', function () {
	register_post_status(
		'wc-prete',
		array(
			'label'                     => 'Prête',
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s : nombre de commandes */
			'label_count'               => _n_noop( 'Prête <span class="count">(%s)</span>', 'Prêtes <span class="count">(%s)</span>' ),
		)
	);
} );

// 2. "Prête" apparaît dans la liste des statuts de commande, juste après "En cours".
add_filter( 'wc_order_statuses', function ( $statuts ) {
	$nouveaux = array();

	foreach ( $statuts as $cle => $nom ) {
		$nouveaux[ $cle ] = $nom;

		if ( 'wc-processing' === $cle ) {
			$nouveaux['wc-prete'] = 'Prête';
		}
	}

	return $nouveaux;
} );

// 3. Action groupée "Changer le statut en Prête" dans WooCommerce > Commandes.
function watsons_action_groupee_prete( $actions ) {
	$actions['mark_prete'] = 'Changer le statut en Prête';
	return $actions;
}
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'watsons_action_groupee_prete' );
add_filter( 'bulk_actions-edit-shop_order', 'watsons_action_groupee_prete' );

// 4. Nouvel e-mail "Commande prête", envoyé quand la commande passe en "Prête".
// La classe de l'e-mail est dans inc/class-watsons-email-commande-prete.php.
add_filter( 'woocommerce_email_classes', function ( $emails ) {
	require_once get_stylesheet_directory() . '/inc/class-watsons-email-commande-prete.php';
	$emails['Watsons_Email_Commande_Prete'] = new Watsons_Email_Commande_Prete();
	return $emails;
} );

// WooCommerce n'envoie un e-mail sur un changement de statut que si on le lui demande.
add_filter( 'woocommerce_email_actions', function ( $actions ) {
	$actions[] = 'woocommerce_order_status_prete';
	return $actions;
} );

/** US-9.3 : mes commandes, détail d'une commande et annulation par le client */

// Statuts pour lesquels le client peut encore annuler sa commande (pas encore prête ni expédiée).
function watsons_commande_annulable( $commande ) {
	return $commande instanceof WC_Order && $commande->has_status( array( 'pending', 'on-hold', 'processing' ) );
}

// La commande a-t-elle déjà été payée ? (virement reçu : "En cours" et pas en paiement au retrait)
function watsons_commande_payee( $commande ) {
	return $commande->has_status( 'processing' ) && 'cod' !== $commande->get_payment_method();
}

// Badge de statut : "En cours", "Prête", "Annulée"... avec une couleur par statut (voir style.css).
function watsons_badge_statut( $commande ) {
	return sprintf(
		'<span class="commande-statut commande-statut--%1$s">%2$s</span>',
		esc_attr( $commande->get_status() ),
		esc_html( wc_get_order_status_name( $commande->get_status() ) )
	);
}

// Titre de la page détail : "Commande N°97".
add_filter( 'woocommerce_endpoint_view-order_title', function ( $titre ) {
	$commande = wc_get_order( absint( get_query_var( 'view-order' ) ) );
	return $commande ? 'Commande N°' . $commande->get_order_number() : $titre;
} );

// Lien "Retour à mes commandes" au-dessus du titre (même composant que "Retour à la carte").
add_action( 'storefront_page_before', function () {
	if ( ! is_wc_endpoint_url( 'view-order' ) ) {
		return;
	}

	printf(
		'<a class="retour-carte" href="%s"><svg class="retour-carte__icone" viewBox="0 0 9 14" aria-hidden="true"><path d="M8 1 2 7l6 6"/></svg>Retour à mes commandes</a>',
		esc_url( wc_get_page_permalink( 'myaccount' ) )
	);
} );

/**
 * Bouton "Oui, annuler ma commande" : annulation par le client.
 * WooCommerce remet alors le stock à jour tout seul (US-9.1).
 */
add_action( 'template_redirect', function () {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- le nonce est vérifié juste en dessous.
	if ( empty( $_POST['watsons_annuler_commande'] ) || ! is_user_logged_in() ) {
		return;
	}

	$nonce = isset( $_POST['watsons-annulation-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['watsons-annulation-nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'watsons_annuler_commande' ) ) {
		return;
	}

	$commande = wc_get_order( absint( $_POST['watsons_annuler_commande'] ) );
	// phpcs:enable

	// Seulement ses propres commandes, et seulement si elles peuvent encore être annulées.
	if ( ! $commande || (int) $commande->get_customer_id() !== get_current_user_id() || ! watsons_commande_annulable( $commande ) ) {
		return;
	}

	// On note si le client avait déjà payé : il faudra le rembourser (mails de l'étape 4).
	$commande->update_meta_data( '_watsons_a_rembourser', watsons_commande_payee( $commande ) ? 'oui' : 'non' );
	$commande->update_status( 'cancelled', 'Commande annulée par le client depuis son compte.' );

	wp_safe_redirect( add_query_arg( 'annulee', '1', $commande->get_view_order_url() ) );
	exit;
} );

/** US-9.3 : e-mails "Paiement reçu", "Commande annulée" et "Remboursement effectué" */

// Le client avait-il déjà payé sa commande annulée ? (noté au moment de l'annulation, sinon on regarde le paiement)
function watsons_commande_a_rembourser( $commande ) {
	$note = $commande->get_meta( '_watsons_a_rembourser' );

	if ( $note ) {
		return 'oui' === $note;
	}

	return $commande->get_date_paid() && 'cod' !== $commande->get_payment_method();
}

// Nouvel e-mail "Commande annulée" envoyé au client (la classe est dans inc/).
add_filter( 'woocommerce_email_classes', function ( $emails ) {
	require_once get_stylesheet_directory() . '/inc/class-watsons-email-commande-annulee.php';
	$emails['Watsons_Email_Commande_Annulee'] = new Watsons_Email_Commande_Annulee();
	return $emails;
} );

// Objet du mail "Commande en cours" quand le virement est reçu : "Paiement reçu...".
add_filter( 'woocommerce_email_subject_customer_processing_order', function ( $objet, $commande ) {
	if ( $commande instanceof WC_Order && 'bacs' === $commande->get_payment_method() ) {
		return 'Paiement reçu pour votre commande n° ' . $commande->get_order_number() . " au Watson's Pub";
	}

	return $objet;
}, 10, 2 );

/** US-10.3 : code promo dans le panier (le bloc est dans woocommerce/cart/cart-totals.php) */

/**
 * Boutons "Appliquer" et "Retirer" du bloc "Code promo".
 * Le message (réussite ou erreur) est gardé pour l'afficher sous le champ, et non en haut de la page.
 */
add_action( 'template_redirect', function () {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() || ! WC()->cart ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification -- le nonce est vérifié juste en dessous.
	$ajouter = isset( $_POST['watsons_code_promo'] );
	$retirer = isset( $_GET['retirer_code'] );

	if ( ! $ajouter && ! $retirer ) {
		return;
	}

	$nonce = $ajouter
		? ( isset( $_POST['watsons-promo-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['watsons-promo-nonce'] ) ) : '' )
		: ( isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '' );

	if ( ! wp_verify_nonce( $nonce, 'watsons_code_promo' ) ) {
		return;
	}

	$code = wc_format_coupon_code( wp_unslash( $ajouter ? $_POST['watsons_code_promo'] : $_GET['retirer_code'] ) );
	// phpcs:enable

	// On met de côté les autres messages de la page, pour ne garder que celui du code promo.
	$autres_messages = wc_get_notices();
	wc_clear_notices();

	if ( $retirer ) {
		WC()->cart->remove_coupon( $code );
		WC()->cart->calculate_totals();
		$message = array( 'type' => 'success', 'texte' => 'Le code ' . strtoupper( $code ) . ' a été retiré.' );
	} elseif ( '' === $code ) {
		$message = array( 'type' => 'error', 'texte' => 'Saisissez un code promo.' );
	} else {
		WC()->cart->apply_coupon( $code );

		// WooCommerce a laissé un message : erreur en priorité, sinon réussite.
		$messages = wc_get_notices();
		$type     = ! empty( $messages['error'] ) ? 'error' : 'success';
		$texte    = ! empty( $messages[ $type ][0]['notice'] ) ? $messages[ $type ][0]['notice'] : '';
		$message  = array( 'type' => $type, 'texte' => wp_strip_all_tags( $texte ) );
	}

	wc_set_notices( $autres_messages );

	$message['code'] = $code;
	WC()->session->set( 'watsons_message_promo', $message );

	wp_safe_redirect( wc_get_cart_url() );
	exit;
} );

// Messages d'erreur des maquettes : code erroné, expiré, indisponible pour le panier.
add_filter( 'woocommerce_coupon_error', function ( $erreur, $code_erreur, $coupon ) {
	switch ( (int) $code_erreur ) {
		case WC_Coupon::E_WC_COUPON_NOT_EXIST:
			return "Ce code n'existe pas. Vérifiez l'orthographe.";

		case WC_Coupon::E_WC_COUPON_EXPIRED:
			$date = ( $coupon instanceof WC_Coupon && $coupon->get_date_expires() ) ? ' le ' . wc_format_datetime( $coupon->get_date_expires(), 'd/m/Y' ) : '';
			return 'Ce code a expiré' . $date . '.';

		case WC_Coupon::E_WC_COUPON_NOT_APPLICABLE:
			return "Ce code ne s'applique à aucun produit de votre panier.";

		case WC_Coupon::E_WC_COUPON_ALREADY_APPLIED:
			return 'Ce code est déjà appliqué.';
	}

	return $erreur;
}, 10, 3 );

// Message de réussite : "Le code PUB10 a bien été appliqué."
add_filter( 'woocommerce_coupon_message', function ( $message, $code_message, $coupon ) {
	if ( WC_Coupon::WC_COUPON_SUCCESS === (int) $code_message && $coupon instanceof WC_Coupon ) {
		return 'Le code ' . strtoupper( $coupon->get_code() ) . ' a bien été appliqué.';
	}

	return $message;
}, 10, 3 );

/** US-11.1 : type de contenu "Équipe" : une page par membre de l'équipe, et une page qui les liste tous */
add_action( 'init', function () {
	register_post_type(
		'membre',
		array(
			'labels'        => array(
				'name'                  => 'Équipe',
				'singular_name'         => "Membre de l'équipe",
				'menu_name'             => 'Équipe',
				'all_items'             => 'Tous les membres',
				'add_new'               => 'Ajouter un membre',
				'add_new_item'          => "Ajouter un membre de l'équipe",
				'edit_item'             => 'Modifier le membre',
				'new_item'              => 'Nouveau membre',
				'view_item'             => 'Voir le profil',
				'search_items'          => 'Rechercher un membre',
				'not_found'             => 'Aucun membre',
				'archives'              => "L'équipe",
				'featured_image'        => 'Photo',
				'set_featured_image'    => 'Choisir la photo',
				'remove_featured_image' => 'Retirer la photo',
				'use_featured_image'    => 'Utiliser comme photo',
			),
			'public'        => true,
			'has_archive'   => 'equipe',                // La page qui liste tous les membres.
			'rewrite'       => array( 'slug' => 'equipe' ),
			'menu_icon'     => 'dashicons-groups',
			'menu_position' => 25,
			// Titre = prénom et nom, éditeur = présentation, image mise en avant = photo, extrait = phrase courte, attributs = ordre d'affichage.
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		)
	);
} );

// Dans l'admin : "Prénom et nom" au lieu de "Saisissez le titre".
add_filter( 'enter_title_here', function ( $texte, $publication ) {
	return 'membre' === $publication->post_type ? 'Prénom et nom' : $texte;
}, 10, 2 );

// Page "L'équipe" : tous les membres, dans l'ordre choisi (champ "Ordre" du membre), puis par nom.
add_action( 'pre_get_posts', function ( $requete ) {
	if ( ! is_admin() && $requete->is_main_query() && $requete->is_post_type_archive( 'membre' ) ) {
		$requete->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$requete->set( 'posts_per_page', -1 );
	}
} );

/** US-11.3 : petites fonctions utilisées par archive-membre.php et single-membre.php */

// Un champ SCF du membre (poste, arrivee, plat_prefere, specialite).
function watsons_champ_membre( $nom, $id = null ) {
	return trim( (string) get_post_meta( $id ? $id : get_the_ID(), $nom, true ) );
}

// La photo du membre (image mise en avant). Sans photo, le cadre reste gris comme sur la maquette.
function watsons_photo_membre( $id = null ) {
	$id = $id ? $id : get_the_ID();

	if ( has_post_thumbnail( $id ) ) {
		echo get_the_post_thumbnail( $id, 'medium_large', array( 'class' => 'membre-photo', 'alt' => get_the_title( $id ) ) );
	}
}