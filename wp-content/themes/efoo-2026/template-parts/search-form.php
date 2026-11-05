<?php
/**
 * サイト内検索のフォーム。404 と検索結果のページで使う。
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="efoo-search-input">サイト内を検索</label>
	<input type="search" id="efoo-search-input" class="search-input" name="s" value="<?php echo get_search_query(); // get_search_query() は属性用にエスケープ済み ?>" placeholder="キーワードを入力">
	<button type="submit" class="search-submit">検索</button>
</form>
