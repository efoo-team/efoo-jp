<?php
/**
 * お知らせ一覧の本体。メインループの行とページ送りを出す。
 * $args['empty'] で 0 件のときの文言を変えられる（検索結果など）。
 */
$efoo_empty = isset( $args['empty'] ) ? (string) $args['empty'] : '記事がまだありません。';
?>
<div style="display: flex; flex-direction: column">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/news-row' );
		}
	} else {
		?>
	<p style="margin: 0; padding: 40px 20px; color: #44504A; border-top: 1px solid #E2E7E3"><?php echo esc_html( $efoo_empty ); ?></p>
		<?php
	}
	?>
	<span style="border-top: 1px solid #E2E7E3"></span>
</div>
<?php
the_posts_pagination(
	array(
		'mid_size'  => 2,
		'prev_text' => '←',
		'next_text' => '→',
	)
);
