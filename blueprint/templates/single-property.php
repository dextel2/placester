<?php
/**
 * Single template
 *
 * This template is used when a single post or page is viewed.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */

?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <?php pls_do_atomic( 'before_entry' ); ?>
    
    <article class="grid_8 alpha property-details" <?php post_class() ?> id="post-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Offer">
        <?php pls_do_atomic( 'open_entry' ); ?>

        <?php pls_do_atomic( 'before_entry_content' ); ?>

        <?php the_content(); ?>
        <div class="entry-meta">
        </div>

        <?php pls_do_atomic( 'after_entry_content' ); ?>

        <footer></footer>

        <?php pls_do_atomic( 'close_entry' ); ?>
        
    </article>

    <?php pls_do_atomic( 'after_entry' ); ?>
    
<?php endwhile; else: ?>
    
    <?php get_template_part( 'loop', 'error' ); ?>
    
<?php endif; ?>
