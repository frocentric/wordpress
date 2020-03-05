<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php
        // Start the loop.
        while ( have_posts() ) : the_post();
        ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><!-- .entry-header -->

                <div class="entry-content">

                    <?php if (is_preview()) { ?>
                        <style scoped>
                            .twig-anything-preview-warning {
                                display: block;
                                margin-bottom: 1em;
                                border: 2px dashed darkorange;
                                font-size: 80%;
                                padding: 0.5em;
                                background-color: lightgoldenrodyellow;
                            }
                            .twig-anything-preview-warning p {
                                padding: 0;
                                margin: 0;
                            }
                        </style>
                        <div class="twig-anything-preview-warning">
                            <?php _e('<p>You are viewing the Twig Anything template in the preview mode. In this mode:<br/>- data is always fetched, cache is never used<br/>- retrieved data is never cached<br/>- any errors are always displayed</p>', 'twig-anything'); ?>
                        </div>
                    <?php } ?>

                    <?php the_content(); ?>
                </div><!-- .entry-content -->

            </article><!-- #post-## -->

        <?php
            // End the loop.
        endwhile;
        ?>

    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer(); ?>
