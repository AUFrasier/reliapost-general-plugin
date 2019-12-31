<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:18 PM
 */
namespace reliapost_registration;

$user = wp_get_current_user()->data;
$userID = $user->ID;
$user_info = get_userdata( $userID );
$type = $user_info->roles[0];
$stripeController = new \reliapost_registration\StripeController();
$stripeToken = $stripeController->getStripeToken();
$sub = $stripeController->getSubscriptionDetails($stripeToken);
$status = $sub->status;
$cancelledSubs = [];
if (is_front_page() || is_page('my-profile')) {
    if ($type == "subscriber") {
        $json_data = json_encode($stripeController->getCancelledSubs($stripeToken));
        $decoded = json_decode($json_data);
        $cancelledSubsTotal = count($decoded->data);
        $cancelledSubs = $stripeController->getCancelledSubs($stripeToken);
    }
}

$args = array(
    'post_type' => 'post',
    'posts_per_page' => 4
);
$post_query = null;
$post_query = new \WP_Query($args);
?>

<div class="page-wrapper">
<div class="home-content">
    <div class="text-card">
        <p>ReliaPost is an opportunity to deliver polished, professional content to your target market on your social media channels. ReliaPost gives manufacturers a way to provide you, the business owner, with easy-to-post content that is consistent with proven-performance marketing data and branding.</p>
        <p>Here you can view and schedule posts that will then be shared to your desired social media account.  Click the icon of your desired social media platform to schedule and share — easy as 1-2-3.</p>
        <p>Make your selections below to schedule your posts on your social media platforms.  Your scheduled posts are displayed on the “Scheduled Posts” page.</p>
    </div>
<div class="grid-container">
    <div class="post-grid">
        <?php if ($post_query->have_posts()) { while ($post_query->have_posts()) : $post_query->the_post(); ?>
            <div class="post-card">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
                    <div class="featured-image">
                        <?php
                        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
                        ?>
                        <a href="<?= the_permalink(); ?>"><img src="<?= $featured_img_url; ?>" /></a>
                    </div>
                    <div class="post-title">
                        <a href="<?= the_permalink(); ?>"><h1><?php the_title(); ?></h1></a>
                    </div>
                    <div class="post-content trimmed-content">
                        <?php 
                        /* $content = the_content();
                        $trimmed_content = wp_trim_words( $content, 50, NULL ); 
                        echo $trimmed_content; */
                        ?>
                    </div>
                    <!--Social Buttons-->
                    <div id="social_button_container">
                        <a id="social-icon-links" href="<?= the_permalink(); ?>"></a>
                    </div>
                </article><!-- #post-<?php the_ID(); ?> -->
            </div>
        <?php endwhile;
        wp_reset_postdata();
        ?>
        <?php } else { ?>
            <div id="no_posts" style="text-align: center;">
                <p style="color:red;">There are no posts</p>
            </div>
        <?php }  ?>
    </div><!-- .post-grid -->
</div><!-- .grid-container -->
</div><!-- .home-content -->
</div><!-- .page-wrapper -->

<?php if ($cancelledSubsTotal >= 1 && $status == null) { ?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".post").hide();
		jQuery( ".fusion-text" ).append( "<p style='color:red; font-size: 25px;'>Your account has been canceled.  Please visit the My Account page to renew your account.  Thank you!.</p>" );
	});
	</script>
<?php } ?>
