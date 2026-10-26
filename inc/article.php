<?php
/**
 * Article helpers: the trust line under the title and the author box.
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

/**
 * Return the author box under a post: photo, a short biography and links to the
 * author profile and the editorial policy (About us) in the current language.
 *
 * @param int $author_id Author user ID.
 * @return array|null
 */
function fh_article_author_box( $author_id ) {
	$author_id = absint( $author_id );

	if ( ! $author_id || ! get_userdata( $author_id ) ) {
		return null;
	}

	// ACF "user_photo" stores the attachment ID (its return format is a URL).
	$photo_id = absint( get_user_meta( $author_id, 'user_photo', true ) );
	$photo    = $photo_id ? get_image_data( $photo_id, 'thumbnail' ) : null;

	if ( ! $photo ) {
		$avatar = get_avatar_url( $author_id, array( 'size' => 160 ) );
		$photo  = $avatar ? array( 'url' => $avatar, 'webp' => null, 'width' => 160, 'height' => 160 ) : null;
	}

	$bio       = trim( wp_strip_all_tags( (string) get_the_author_meta( 'description', $author_id ) ) );
	$policy_id = fh_translated_id( (int) apply_filters( 'fh_editorial_policy_page', 17 ), 'page' );

	return array(
		'heading'      => __( 'About the author', 'news' ),
		'name'         => get_the_author_meta( 'display_name', $author_id ),
		'photo'        => $photo,
		'bio'          => '' !== $bio ? wp_trim_words( $bio, 45 ) : '',
		'profile_url'  => get_author_posts_url( $author_id ),
		'profile_text' => __( 'Author profile', 'news' ),
		'policy_url'   => 'publish' === get_post_status( $policy_id ) ? get_permalink( $policy_id ) : '',
		'policy_text'  => __( 'Editorial policy', 'news' ),
	);
}
