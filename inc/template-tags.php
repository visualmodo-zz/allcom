<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package AllCom
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'allcom_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function allcom_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s"> (%4$s) </time>';
		}
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		$posted_on   = apply_filters(
			'allcom_posted_on',
			sprintf(
				'<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
				esc_html_x( 'Posted on', 'post date', 'allcom' ),
				esc_url( get_permalink() ),
				apply_filters( 'allcom_posted_on_time', $time_string )
			)
		);
		$byline      = apply_filters(
			'allcom_posted_by',
			sprintf(
				'<span class="byline"> %1$s<span class="author vcard"> <a class="url fn n" href="%2$s">%3$s</a></span></span>',
				$posted_on ? esc_html_x( 'by', 'post author', 'allcom' ) : esc_html_x( 'Posted by', 'post author', 'allcom' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_html( get_the_author() )
			)
		);
		echo $posted_on . $byline; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'allcom_entry_footer' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function allcom_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'allcom' ) );
			if ( $categories_list && allcom_categorized_blog() ) {
				/* translators: %s: Categories of current post */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %s', 'allcom' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html__( ', ', 'allcom' ) );
			if ( $tags_list ) {
				/* translators: %s: Tags of current post */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %s', 'allcom' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link( esc_html__( 'Leave a comment', 'allcom' ), esc_html__( '1 Comment', 'allcom' ), esc_html__( '% Comments', 'allcom' ) );
			echo '</span>';
		}
		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', 'allcom' ),
				the_title( '<span class="sr-only">"', '"</span>', false )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
}

if ( ! function_exists( 'allcom_categorized_blog' ) ) {
	/**
	 * Returns true if a blog has more than 1 category.
	 *
	 * @return bool
	 */
	function allcom_categorized_blog() {
		$all_the_cool_cats = get_transient( 'allcom_categories' );
		if ( false === $all_the_cool_cats ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories(
				array(
					'fields'     => 'ids',
					'hide_empty' => 1,
					// We only need to know if there is more than one category.
					'number'     => 2,
				)
			);
			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );
			set_transient( 'allcom_categories', $all_the_cool_cats );
		}
		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so allcom_categorized_blog should return true.
			return true;
		}
		// This blog has only 1 category so allcom_categorized_blog should return false.
		return false;
	}
}

add_action( 'edit_category', 'allcom_category_transient_flusher' );
add_action( 'save_post', 'allcom_category_transient_flusher' );

if ( ! function_exists( 'allcom_category_transient_flusher' ) ) {
	/**
	 * Flush out the transients used in allcom_categorized_blog.
	 */
	function allcom_category_transient_flusher() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Like, beat it. Dig?
		delete_transient( 'allcom_categories' );
	}
}

if ( ! function_exists( 'allcom_body_attributes' ) ) {
	/**
	 * Displays the attributes for the body element.
	 */
	function allcom_body_attributes() {
		/**
		 * Filters the body attributes.
		 *
		 * @param array $atts An associative array of attributes.
		 */
		$atts = array_unique( apply_filters( 'allcom_body_attributes', $atts = array() ) );
		if ( ! is_array( $atts ) || empty( $atts ) ) {
			return;
		}
		$attributes = '';
		foreach ( $atts as $name => $value ) {
			if ( $value ) {
				$attributes .= sanitize_key( $name ) . '="' . esc_attr( $value ) . '" ';
			} else {
				$attributes .= sanitize_key( $name ) . ' ';
			}
		}
		echo trim( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
