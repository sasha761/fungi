<?php
/**
 * Article helpers: the trust line under the title.
 *
 * @package News
 */

/**
 * Return the trust line of a post or page: author with a link to the profile
 * and the date of the last update.
 *
 * The date is post_modified, the same value Yoast prints as dateModified.
 *
 * @param int $post_id Post or page ID.
 * @return array
 */
function fh_article_trust( $post_id ) {
	$author_id = absint( get_post_field( 'post_author', $post_id ) );
	$modified  = get_post_modified_time( 'U', true, $post_id );
	$format    = in_array( news_get_current_language(), array( 'ru', 'uk' ), true ) ? 'd.m.Y' : 'j M Y';

	return array(
		'author_label'  => __( 'Author:', 'news' ),
		'author_name'   => $author_id ? get_the_author_meta( 'display_name', $author_id ) : '',
		'author_url'    => $author_id ? get_author_posts_url( $author_id ) : '',
		'updated_label' => __( 'Updated:', 'news' ),
		'updated'       => $modified ? wp_date( $format, $modified ) : '',
		'updated_iso'   => $modified ? wp_date( 'c', $modified ) : '',
	);
}
