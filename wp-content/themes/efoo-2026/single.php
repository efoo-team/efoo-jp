<?php
/**
 * 投稿・ポートフォリオ（お知らせ・掲載実績）の詳細。
 * 見出し帯（分類・日付・タイトル）、帯の下端にまたがるアイキャッチ、本文、記事の終わり
 * （同じ分類のほかの記事 3 件と一覧への導線）の順に並べる。分類で変えるのは、
 * 掲載実績の本文をカードに収めることと、お問い合わせのリンクの有無だけである。
 */
get_header();
while ( have_posts() ) {
	the_post();
	$efoo_role      = efoo_entry_role();
	$efoo_label     = efoo_entry_term_label();
	$efoo_term      = efoo_entry_term();
	$efoo_term_link = $efoo_term ? get_term_link( $efoo_term ) : '';
	$efoo_title_cls = mb_strlen( get_post_field( 'post_title', get_post(), 'raw' ) ) > 50 ? ' entry-title--long' : '';
	?>
<main>
	<article class="entry entry--<?php echo esc_attr( $efoo_role ); ?>">
		<header class="entry-head">
			<div class="entry-head-inner">
				<p class="entry-meta">
					<?php if ( $efoo_term_link && ! is_wp_error( $efoo_term_link ) ) { ?>
					<a class="entry-term" href="<?php echo esc_url( $efoo_term_link ); ?>"><?php echo esc_html( $efoo_label ); ?></a>
					<?php } else { ?>
					<span class="entry-term"><?php echo esc_html( $efoo_label ); ?></span>
					<?php } ?>
					<time class="en entry-date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
				</p>
				<h1 class="entry-title<?php echo esc_attr( $efoo_title_cls ); ?>"><?php the_title(); ?></h1>
			</div>
		</header>
		<?php if ( efoo_entry_shows_cover() ) { ?>
		<figure class="entry-cover">
			<?php
			the_post_thumbnail(
				'large',
				array(
					'sizes'   => '(max-width: 944px) 100vw, 880px',
					'loading' => 'eager',
				)
			);
			?>
		</figure>
		<?php } ?>
		<div class="prose">
			<?php if ( 'media' === $efoo_role ) { ?>
			<div class="prose-card">
				<?php the_content(); ?>
			</div>
			<?php } else { ?>
				<?php the_content(); ?>
			<?php } ?>
		</div>
		<footer class="entry-foot">
			<hr class="entry-foot-rule">
			<?php
			// 同じ分類の新しい順に 3 件（この記事を除く）。分類が無い記事・0 件のときは節ごと出さない
			if ( $efoo_term && post_type_exists( 'portfolio_item' ) ) {
				$efoo_more = new WP_Query(
					array(
						'post_type'      => 'portfolio_item',
						'post_status'    => 'publish',
						'posts_per_page' => 3,
						'post__not_in'   => array( get_the_ID() ),
						'no_found_rows'  => true,
						'tax_query'      => array(
							array(
								'taxonomy' => 'portfolio',
								'terms'    => $efoo_term->term_id,
							),
						),
					)
				);
				if ( $efoo_more->have_posts() ) {
					?>
			<section class="entry-more">
				<h2 class="entry-more-title">ほかの<?php echo esc_html( $efoo_term->name ); ?></h2>
				<div style="display: flex; flex-direction: column">
					<?php
					while ( $efoo_more->have_posts() ) {
						$efoo_more->the_post();
						get_template_part( 'template-parts/news-row' );
					}
					?>
					<span style="border-top: 1px solid #E2E7E3"></span>
				</div>
			</section>
					<?php
				}
				wp_reset_postdata();
			}
			?>
			<div class="entry-nav">
				<a class="btn-ghost" href="<?php echo esc_url( efoo_news_index_url() ); ?>">お知らせ一覧へ</a>
				<?php if ( in_array( $efoo_role, array( 'press', 'media' ), true ) ) { ?>
				<a class="entry-nav-contact" href="<?php echo esc_url( efoo_contact_url() ); ?>">取材・掲載のお問い合わせ</a>
				<?php } ?>
			</div>
		</footer>
	</article>
</main>
	<?php
}
get_footer();
