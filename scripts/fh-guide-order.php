<?php
/**
 * One-off: fill fh_guide_order (position of a post in its guide, from 1) for EN, RU and UK.
 *
 * Usage (from the WordPress root):
 *   wp eval-file wp-content/themes/news/scripts/fh-guide-order.php dry-run   # check only
 *   wp eval-file wp-content/themes/news/scripts/fh-guide-order.php           # write
 *
 * Only the fh_guide_order meta is written. Every ID is checked first: it must be a
 * published post in the expected language and in the expected pillar category.
 * Running it again gives the same result.
 *
 * @package News
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

$dry_run = in_array( 'dry-run', isset( $args ) ? (array) $args : array(), true );

// [EN, RU, UK] per position; null = no version in that language.
$guides = json_decode(
	'{
	"G1": [[1638,1662,1663],[1253,1249,1254],[726,1091,1094],[1712,1713,1714],[1767,1768,1769],[1526,1459,1535],[1761,1762,1763],[1870,1871,1872],[1429,1416,1432],[1396,1152,1399],[2399,2393,2396],[1630,1674,1675],[1634,1680,1681],[1706,1707,1708],[null,900,null],[1898,1899,1900],[1930,1931,1932],[1913,1914,1915],[1942,1943,1944],[1755,1756,1757]],
	"G2": [[718,783,784],[1876,1877,1878],[1907,1908,1909],[1924,1925,1926]],
	"G3": [[682,685,673],[1437,1348,1440],[1306,1307,1301],[1443,1135,1446],[510,515,516],[2416,2412,2420]],
	"G4": [[1691,1692,1693],[1821,1822,1823],[1837,1838,1839],[1815,1816,1817],[1936,1937,1938],[1749,1750,1751],[1799,1800,1801],[808,2204,802]],
	"G5": [[1734,1735,1736],[746,1113,1110],[2408,835,2410],[1724,1725,1726],[1330,786,1331],[1773,1774,1775],[1882,1883,1884]]
	}',
	true
);

$languages = array( 'en', 'ru', 'uk' );
$errors    = array();
$plan      = array();

foreach ( $guides as $guide_key => $rows ) {
	foreach ( $rows as $index => $row ) {
		foreach ( $row as $language_index => $post_id ) {
			if ( null === $post_id ) {
				continue;
			}

			$language = $languages[ $language_index ];
			$post     = get_post( $post_id );
			$details  = apply_filters( 'wpml_post_language_details', null, $post_id );
			$actual   = is_array( $details ) && isset( $details['language_code'] ) ? $details['language_code'] : '';

			if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
				$errors[] = "$guide_key #" . ( $index + 1 ) . " $language: post $post_id is missing or not published";
				continue;
			}

			if ( $actual !== $language ) {
				$errors[] = "$guide_key #" . ( $index + 1 ) . " $language: post $post_id is in language '$actual'";
				continue;
			}

			if ( fh_guide_key_by_post_categories( $post_id ) !== $guide_key ) {
				$errors[] = "$guide_key #" . ( $index + 1 ) . " $language: post $post_id is not in the $guide_key pillar category";
				continue;
			}

			$plan[ $post_id ] = array( $guide_key, $index + 1, $language );
		}
	}
}

foreach ( $errors as $error ) {
	WP_CLI::warning( $error );
}

if ( $errors ) {
	WP_CLI::error( count( $errors ) . ' problem(s) found, nothing written. Fix the list or the posts and run again.' );
}

$changed = 0;
$summary = array();

foreach ( $plan as $post_id => list( $guide_key, $position, $language ) ) {
	$current = (int) get_post_meta( $post_id, 'fh_guide_order', true );
	$summary[ "$guide_key $language" ] = isset( $summary[ "$guide_key $language" ] ) ? $summary[ "$guide_key $language" ] + 1 : 1;

	if ( $current === $position ) {
		continue;
	}

	++$changed;
	WP_CLI::log( sprintf( '%s %s #%d: post %d (%s) %s', $guide_key, $language, $position, $post_id, get_the_title( $post_id ), $dry_run ? '[dry-run]' : '' ) );

	if ( ! $dry_run ) {
		update_post_meta( $post_id, 'fh_guide_order', $position );
	}
}

foreach ( $summary as $label => $count ) {
	WP_CLI::log( "$label: $count posts" );
}

WP_CLI::success( sprintf( '%d posts checked, %d %s.', count( $plan ), $changed, $dry_run ? 'would be updated' : 'updated' ) );
