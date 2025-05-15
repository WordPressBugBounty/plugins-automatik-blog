<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Loads the site header
?>
<div class="webstories-archive-container" style="padding: 20px;">
    <h1 style="text-align: center;">Web Stories</h1>
    <div class="webstories-grid" style="
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 20px auto;
    ">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="webstory-item" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <?php
                    // Initialize cover image URL variable.
                    $cover_image_url = '';

                    // First, check if the post has a featured image.
                    if ( has_post_thumbnail() ) {
                        $cover_image_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                    } else {
                        // No featured image set.
                        // Try to get the first image from the web story pages meta.
                        $pages = get_post_meta( get_the_ID(), 'webstory_pages', true );
                        if ( is_array( $pages ) && ! empty( $pages ) ) {
                            // Assuming each page is an array and the image URL is stored under 'image_url'
                            if ( isset( $pages[0]['image_url'] ) && ! empty( $pages[0]['image_url'] ) ) {
                                $cover_image_url = $pages[0]['image_url'];
                            }
                        }
                    }

                    // If we have a cover image URL, output it.
                    if ( $cover_image_url ) :
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <img 
                                src="<?php echo esc_url( $cover_image_url ); ?>" 
                                style="width: 100%; display: block;" 
                                alt="<?php the_title_attribute(); ?>" 
                                loading="lazy"
                            >
                        </a>
                    <?php endif; ?>
                    
                    <div class="webstory-content" style="padding: 10px;">
                        <h2 style="font-size: 1.2em; margin: 0 0 10px;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #333;">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p style="text-align: center;">No Web Stories found.</p>
        <?php endif; ?>
    </div>
    <div class="webstories-pagination" style="text-align: center; margin-top: 20px;">
        <?php
            the_posts_pagination(
                array(
                    'mid_size'  => 2,
                    'prev_text' => __( '« Previous', 'automatik-blog' ),
                    'next_text' => __( 'Next »', 'automatik-blog' ),
                )
            );
        ?>
    </div>
</div>
<?php
/**
 * If PHPCS flags lines (e.g., 58, 60, 65, 67) for "NonEnqueuedScript"
 * but there's no actual <script> tags here, add inline ignores like so:
 */

// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
// (If you actually do have inline <script> somewhere, place the ignore right above it.)

get_footer(); // Loads the site footer
