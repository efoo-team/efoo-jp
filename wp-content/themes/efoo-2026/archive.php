<?php
/**
 * アーカイブ（カテゴリ・日付など）。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">CONTENTS</p>
		<h1 class="page-band-title"><?php echo esc_html( is_post_type_archive( 'portfolio_item' ) ? 'お知らせ・掲載実績' : wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
	</section>
	<div class="list-wrap" style="display: flex; flex-direction: column; gap: 36px">
		<?php get_template_part( 'template-parts/category-chips' ); ?>
		<?php get_template_part( 'template-parts/news-list' ); ?>
	</div>
</main>
<?php
get_footer();
