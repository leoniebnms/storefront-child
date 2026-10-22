<?php
/**
 * US-11.3 : page "L'équipe" qui liste tous les membres, comme la maquette.
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main equipe">

		<?php /* Titre sans accent : la police Tall Dark n'en a pas */ ?>
		<header class="equipe__entete">
			<h1 class="equipe__titre">L'equipe</h1>
			<p class="equipe__intro">Ils vous accueillent tous les jours au Watson's Pub. Découvrez l'équipe qui fait vivre le pub.</p>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="equipe__liste">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article class="membre-carte">
						<a class="membre-carte__photo" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php watsons_photo_membre(); ?>
						</a>

						<h2 class="membre-carte__nom"><?php the_title(); ?></h2>

						<?php if ( watsons_champ_membre( 'poste' ) ) : ?>
							<p class="membre-carte__poste"><?php echo esc_html( watsons_champ_membre( 'poste' ) ); ?></p>
						<?php endif; ?>

						<?php if ( has_excerpt() ) : ?>
							<p class="membre-carte__phrase"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>

						<a class="membre-carte__bouton" href="<?php the_permalink(); ?>">Voir le profil<span class="screen-reader-text"> de <?php the_title(); ?></span></a>
					</article>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="equipe__vide">L'équipe sera bientôt présentée ici.</p>
		<?php endif; ?>

	</main>
</div>

<?php
get_footer();