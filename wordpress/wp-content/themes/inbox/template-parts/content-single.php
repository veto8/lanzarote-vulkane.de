<?php
/**
 * Template part for displaying single posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbox
 */

?>

		<article id="single-post-pull" <?php post_class('col-12 col-lg-12 col-sm-12 col-xl-12'); ?> data-simplebar>
                <div class="entry-header">
	                <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="post-meta">
                    <span class="author-name"><?php the_author(); ?></span>
                    <span class="post-category"><?php the_category( '&nbsp;&bull;&nbsp;' ); ?></span>
                    <span class="post-date"><?php echo esc_html(get_the_date(get_option( 'date-format' )) ); ?></span>
                    </div>
                </div>                
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
                    <div class="post-tags">
                    <?php the_tags( esc_html('Tagged with: ', 'inbox'), esc_html(' &#8226; ', 'inbox'), '<br />' ); ?>
                    </div>
                    <p class="read-more"><a href="<?php the_permalink(); ?>" class="button"><?php echo esc_html('Read More', 'inbox'); ?></a></p>
                </div>
            </div>
        </article>