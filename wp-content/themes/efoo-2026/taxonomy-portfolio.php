<?php
/**
 * portfolio タクソノミー（お知らせ・掲載実績の分類）のアーカイブ。
 * wpzoom-portfolio はテーマにこのファイルがあればプラグイン側のテンプレートより優先して使う。
 */
get_header();
?>
<main>
	<section class="page-band">
		<p class="en page-band-label">CONTENTS</p>
		<h1 class="page-band-title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
	</section>
	<div class="list-wrap" style="display: flex; flex-direction: column; gap: 36px">
		<?php get_template_part( 'template-parts/category-chips' ); ?>
		<?php get_template_part( 'template-parts/news-list' ); ?>
	</div>
</main>
<?php
get_footer();
