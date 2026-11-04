<?php
/**
 * アーカイブ（カテゴリ・日付など）。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">CONTENTS</p>
		<h1 class="page-band-title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
	</section>
	<div style="max-width: 1240px; margin: 0 auto; padding: 48px 32px 110px; box-sizing: border-box; display: flex; flex-direction: column; gap: 36px">
		<?php get_template_part( 'template-parts/category-chips' ); ?>
		<?php get_template_part( 'template-parts/news-list' ); ?>
	</div>
</main>
<?php
get_footer();
