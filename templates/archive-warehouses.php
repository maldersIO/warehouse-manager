<?php get_header(); ?>
<?php if (!is_user_logged_in()) {
    echo 'Please log in to view this content.';
    return; // Stop further processing if the user is not logged in.
} ?>
<div class="warehouse-archive warehouse">
    <div class="container">

        <div class="row mb-5">
            <div class="col-md-12">
                <header class="archive-header">
                    <h1 class="archive-title mb-0">Warehouses</h1>
                </header>
                <?php if (have_posts()) : ?>
                    <div class="warehouses-list">
                        <div class="row">
                            <?php while (have_posts()) : the_post(); ?>
                                <div class="col-md-3 mb-4">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="card" style="">
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pb-0">!</span>
                                            <?php
                                                $image_url = wp_get_attachment_url(1234);
                                                // change if post have feature image
                                                if (has_post_thumbnail(get_the_ID())) {
                                                    $image_url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), '');
                                                }
                                                ?>

                                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background-image: url('<?php echo $image_url ?>'); background-repeat: no-repeat; background-size: cover;">
                                              
                                                <div class="card-body">
                                                    <h5 class="card-title mb-0"><?php the_title(); ?></h5>
                                                    <p class="card-text mb-2"><small><?php echo get_post_meta(get_the_ID(), 'warehouse_address', true); ?></small></p>
                                                    <p class="mb-0"><a href="<?php the_permalink(); ?>" class="btn btn-primary">Manage</a></p>
                                                </div>
                                            </article>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <?php the_posts_navigation(); ?>
                <?php else : ?>
                    <p><?php _e('Sorry, no warehouses matched your criteria.', 'textdomain'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>