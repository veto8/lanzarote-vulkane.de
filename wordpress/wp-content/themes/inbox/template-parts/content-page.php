<?php
/**
 * Template part for displaying single posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbox
 */

?>

		<article data-simplebar id="single-post-pull" <?php post_class('col-12 col-lg-12 col-sm-12 col-xl-12'); ?>>
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
				<?php  if ( has_post_thumbnail() ) {?>
					<figure class="image is-5by3 post-thumbnail">
                        <?php the_post_thumbnail('inbox-single', array('class' => 'img-responsive')); ?>
                    </figure>
                <?php } ?>                
                
                <div class="entry-content">
					<?php 
                    the_content(); 
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'inbox' ),
                        'after'  => '</div>',
                    ) );
                    
                    ?>
                </div>
            </div>
        </article>