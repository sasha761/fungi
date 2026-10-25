<?php
/**
 * Formatting of article and pillar content: key points, sources, disclaimer,
 * tables, quotes and safety sections. Post texts are not changed in the database;
 * the markup is adjusted on output (the_content) by the text of the headings.
 *
 * @package News
 */

/**
 * Check whether a page uses the pillar template.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function fh_is_pillar_page( $post_id ) {
	return 'page' === get_post_type( $post_id ) && 'template-pages/pillar-template.php' === get_page_template_slug( $post_id );
}

/**
 * Return the normalized text of a node: single spaces, trimmed, lower case.
 *
 * @param DOMNode $node Node.
 * @return string
 */
function fh_content_node_text( $node ) {
	$text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $node->textContent, ENT_QUOTES, 'UTF-8' ) ) );

	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
}

/**
 * Return the next element sibling, skipping whitespace and comments.
 *
 * @param DOMNode $node Node.
 * @return DOMElement|null
 */
function fh_content_next_element( $node ) {
	for ( $next = $node->nextSibling; $next; $next = $next->nextSibling ) {
		if ( XML_ELEMENT_NODE === $next->nodeType ) {
			return $next;
		}

		if ( XML_TEXT_NODE === $next->nodeType && '' !== trim( $next->nodeValue ) ) {
			return null;
		}
	}

	return null;
}

/**
 * Check whether an element has a class.
 *
 * @param DOMElement $element Element.
 * @param string     $class   Class name.
 * @return bool
 */
function fh_content_has_class( $element, $class ) {
	return $element instanceof DOMElement && in_array( $class, preg_split( '/\s+/', (string) $element->getAttribute( 'class' ) ), true );
}

/**
 * Add a class to an element.
 *
 * @param DOMElement $element Element.
 * @param string     $class   Class name.
 * @return void
 */
function fh_content_add_class( $element, $class ) {
	if ( ! fh_content_has_class( $element, $class ) ) {
		$element->setAttribute( 'class', trim( $element->getAttribute( 'class' ) . ' ' . $class ) );
	}
}

/**
 * Wrap a list of sibling nodes into a new element placed where the first node was.
 *
 * @param DOMDocument $doc   Document.
 * @param DOMNode[]   $nodes Sibling nodes in document order.
 * @param string      $tag   Wrapper tag.
 * @param string      $class Wrapper class.
 * @return DOMElement
 */
function fh_content_wrap( $doc, array $nodes, $tag, $class ) {
	$wrapper = $doc->createElement( $tag );
	$wrapper->setAttribute( 'class', $class );
	$nodes[0]->parentNode->insertBefore( $wrapper, $nodes[0] );

	foreach ( $nodes as $node ) {
		$wrapper->appendChild( $node );
	}

	return $wrapper;
}

/**
 * Check whether a paragraph is fully emphasized: <p><em>…</em></p>.
 *
 * @param DOMElement $paragraph Paragraph.
 * @return bool
 */
function fh_content_is_em_paragraph( $paragraph ) {
	$elements = 0;

	foreach ( $paragraph->childNodes as $child ) {
		if ( XML_ELEMENT_NODE === $child->nodeType ) {
			if ( 'em' !== strtolower( $child->nodeName ) || ++$elements > 1 ) {
				return false;
			}
		} elseif ( XML_TEXT_NODE === $child->nodeType && '' !== trim( $child->nodeValue ) ) {
			return false;
		}
	}

	return 1 === $elements;
}

/**
 * Format the content of a post or a pillar page.
 *
 * @param string $content HTML.
 * @param int    $post_id Post or page ID.
 * @param array  $options 'safety' (bool) wraps safety sections.
 * @return string
 */
function fh_content_format( $content, $post_id, array $options ) {
	if ( '' === trim( $content ) || ! class_exists( 'DOMDocument' ) ) {
		return $content;
	}

	$doc      = new DOMDocument( '1.0', 'UTF-8' );
	$previous = libxml_use_internal_errors( true );
	$loaded   = $doc->loadHTML( '<?xml encoding="UTF-8"?><html><body><div id="fh-content-root">' . $content . '</div></body></html>', LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	$root = $loaded ? $doc->getElementById( 'fh-content-root' ) : null;

	if ( ! $root ) {
		return $content;
	}

	$top = static function () use ( $root ) {
		$elements = array();

		foreach ( $root->childNodes as $child ) {
			if ( XML_ELEMENT_NODE === $child->nodeType ) {
				$elements[] = $child;
			}
		}

		return $elements;
	};

	// Key points: "<p><strong>Key points</strong></p>" followed by a list.
	$key_points = '/^(key points|at a glance|in brief|in short|коротко|кратко)\s*:?$/u';

	foreach ( $top() as $element ) {
		if ( 'p' !== $element->nodeName || ! preg_match( $key_points, fh_content_node_text( $element ) ) || ! $element->getElementsByTagName( 'strong' )->length ) {
			continue;
		}

		$list = fh_content_next_element( $element );

		if ( $list && in_array( $list->nodeName, array( 'ul', 'ol' ), true ) ) {
			fh_content_add_class( $element, 'fh-keypoints__title' );
			fh_content_wrap( $doc, array( $element, $list ), 'div', 'fh-keypoints' );
		}
	}

	// Sources: an H2 ending with "Sources / Источники / Джерела" followed by a list.
	foreach ( $top() as $element ) {
		if ( 'h2' !== $element->nodeName || ! preg_match( '/(^|\s)(sources|references|источники|джерела)$/u', fh_content_node_text( $element ) ) ) {
			continue;
		}

		$list = fh_content_next_element( $element );

		if ( $list && in_array( $list->nodeName, array( 'ol', 'ul' ), true ) ) {
			fh_content_wrap( $doc, array( $element, $list ), 'div', 'fh-sources' );
		}
	}

	// Disclaimer: the last fully emphasized paragraph after the last H2.
	$disclaimer = null;

	foreach ( $top() as $element ) {
		if ( 'h2' === $element->nodeName ) {
			$disclaimer = null;
		} elseif ( 'p' === $element->nodeName && fh_content_is_em_paragraph( $element ) ) {
			$disclaimer = $element;
		}
	}

	if ( $disclaimer ) {
		fh_content_add_class( $disclaimer, 'fh-disclaimer' );
	}

	// Tables scroll horizontally on phones.
	foreach ( iterator_to_array( $doc->getElementsByTagName( 'table' ) ) as $table ) {
		$target = $table;

		if ( $table->parentNode instanceof DOMElement && 'figure' === $table->parentNode->nodeName ) {
			$target = $table->parentNode;
		}

		if ( ! fh_content_has_class( $target->parentNode, 'fh-table-scroll' ) ) {
			fh_content_wrap( $doc, array( $target ), 'div', 'fh-table-scroll' );
		}
	}

	// Quotes with a source line.
	foreach ( $doc->getElementsByTagName( 'blockquote' ) as $quote ) {
		if ( $quote->getElementsByTagName( 'cite' )->length ) {
			fh_content_add_class( $quote, 'fh-quote' );
		}
	}

	// Safety sections: an H2 about safety, poisoning or emergency help and everything up to the next H2.
	if ( ! empty( $options['safety'] ) ) {
		$safety = '/(safety|poison|emergenc|безопасн|отравлен|скорую|скорой|экстренн|безпек|отруєн|швидку|швидкої|невідкладн)/u';

		foreach ( $top() as $element ) {
			if ( 'h2' !== $element->nodeName || ! preg_match( $safety, fh_content_node_text( $element ) ) ) {
				continue;
			}

			$nodes = array( $element );

			for ( $next = $element->nextSibling; $next; $next = $next->nextSibling ) {
				if ( $next instanceof DOMElement && ( 'h2' === $next->nodeName || fh_content_has_class( $next, 'fh-sources' ) || fh_content_has_class( $next, 'fh-disclaimer' ) || false !== strpos( $next->getAttribute( 'class' ), 'schema-faq' ) ) ) {
					break;
				}

				$nodes[] = $next;
			}

			while ( count( $nodes ) > 1 && XML_TEXT_NODE === end( $nodes )->nodeType && '' === trim( end( $nodes )->nodeValue ) ) {
				array_pop( $nodes );
			}

			fh_content_wrap( $doc, $nodes, 'section', 'fh-safety' );
		}
	}

	$html = '';

	foreach ( $root->childNodes as $child ) {
		$html .= $doc->saveHTML( $child );
	}

	// Safety net: if the parser lost text, keep the original markup.
	$text_length = static function ( $markup ) {
		return strlen( preg_replace( '/\s+/u', '', html_entity_decode( wp_strip_all_tags( $markup ), ENT_QUOTES, 'UTF-8' ) ) );
	};

	return $text_length( $html ) >= $text_length( $content ) * 0.99 ? $html : $content;
}

/**
 * the_content filter: format the main post or pillar of the page.
 *
 * Skipped for excerpts (wp_trim_excerpt runs the_content too) and anything that is
 * not the queried post itself.
 *
 * @param string $content HTML.
 * @return string
 */
function fh_content_filter( $content ) {
	if ( is_admin() || is_feed() || wp_is_json_request() || doing_filter( 'get_the_excerpt' ) ) {
		return $content;
	}

	$post_id = (int) get_the_ID();

	if ( ! $post_id || get_queried_object_id() !== $post_id ) {
		return $content;
	}

	if ( is_singular( 'post' ) ) {
		$guide = fh_guide_for_post( $post_id );

		return fh_content_format(
			$content,
			$post_id,
			array(
				'safety' => $guide && in_array( $guide['key'], array( 'G3', 'G4', 'G5' ), true ),
			)
		);
	}

	if ( is_page() && fh_is_pillar_page( $post_id ) ) {
		return fh_content_format(
			$content,
			$post_id,
			array(
				'safety' => in_array( fh_guide_key_by_page( $post_id ), array( 'G3', 'G4', 'G5' ), true ),
			)
		);
	}

	return $content;
}
add_filter( 'the_content', 'fh_content_filter', 30 );
