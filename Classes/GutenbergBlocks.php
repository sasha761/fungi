<?php
/**
 * Register the theme's metadata-based Gutenberg blocks.
 *
 * @package News
 */

class GutenbergBlocks {
	/**
	 * Attach block registration hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'registerAcfBlocks' ) );
		add_filter( 'block_categories_all', array( $this, 'addGutenbergCategory' ) );
	}

	/**
	 * Register every block directory that contains block.json.
	 *
	 * @return void
	 */
	public function registerAcfBlocks(): void {
		if ( ! function_exists( 'register_block_type' ) || ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		$block_directories = glob( get_template_directory() . '/gutenberg/*', GLOB_ONLYDIR );

		if ( ! is_array( $block_directories ) ) {
			return;
		}

		foreach ( $block_directories as $block_directory ) {
			if ( file_exists( $block_directory . '/block.json' ) ) {
				register_block_type( $block_directory );
			}
		}
	}

	/**
	 * Add the theme block category to the inserter.
	 *
	 * @param array $categories Existing block categories.
	 * @return array
	 */
	public function addGutenbergCategory( array $categories ): array {
		$categories[] = array(
			'slug'  => 'fungi-category',
			'title' => esc_html__( 'Fungi blocks', 'news' ),
		);

		return $categories;
	}
}

$blocks = new GutenbergBlocks();
$blocks->init();
