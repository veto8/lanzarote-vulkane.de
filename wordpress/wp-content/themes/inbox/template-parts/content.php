<?php
/**
 * Template part for displaying post content
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package inbox
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('single-post-list'); ?>>
		<div class="row">
				<?php
                if ( is_sticky() ) { 
                ?>                            
                    <div class="ribbon"><span><?php echo esc_html('STICKY', 'inbox'); ?></span></div>                            
                <?php } ?>                                                                        
        		<div class="col-3 col-lg-2 col-md-2 col-xl-2 d-none d-xl-block post-thumbnail">                
				<?php  if ( has_post_thumbnail() ) {?>
                        <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid rounded-circle')); ?>
                <?php } 
				else {
				?>    
                <div class="no-thumbnail">
                	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/placeholder.png" class="img-fluid rounded-circle" />
                </div>                
                <?php } ?>                 
                </div>                                    
                <div class="col-12 col-lg-12 col-md-12 col-xl-10">
                    <div class="text-holder">
                        <span class="author-name"><?php the_author(); ?></span>
                        <span class="post-date"><?php echo esc_html(get_the_date(get_option( 'date-format' )) ); ?></span>
                        <a rel="<?php echo esc_url(get_the_permalink()); ?>" href="<?php the_permalink(); ?>" class="entry-title trick"><?php the_title(); ?></a>
                        <p class="post-category"><?php the_category( '&nbsp;&bull;&nbsp;' ); ?></p>
                        <div class="entry-excerpt"><?php the_excerpt(); ?></div>
                    </div>
                </div>
        </div>
</article>