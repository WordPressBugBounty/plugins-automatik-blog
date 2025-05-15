<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = get_the_ID();
$pages   = get_post_meta( $post_id, 'webstory_pages', true );
if ( ! is_array( $pages ) ) {
    $pages = [];
}

$logo_url     = get_post_meta( $post_id, 'webstory_logo_url', true );
$cta_text     = get_post_meta( $post_id, 'webstory_cta_text', true );
$cta_link     = get_post_meta( $post_id, 'webstory_cta_link', true );
$cta_position = get_post_meta( $post_id, 'webstory_cta_position', true );
$cta_color    = get_post_meta( $post_id, 'webstory_cta_color', true );

// Validate that the color is a proper hex code (3 or 6 digits). If not, use default.
if ( empty( $cta_color ) || ! preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $cta_color ) ) {
    $cta_color = '#9900ee'; // default color
}

// Ads config (e.g. ['publisher_code' => 'ca-pub-123456', 'ad_slot' => '1234567890'])
$ads_config    = get_post_meta( $post_id, 'webstory_ads', true );
$canonical_url = get_permalink( $post_id );

// Allowed animation types.
$valid_animations = array(
    'fade-in',
    'fly-in-left',
    'fly-in-right',
    'fly-in-top',
    'fly-in-bottom',
    'drop',
    'drop-in',
    'drop-out',
    'twirl-in',
    'whoosh-in-left',
    'whoosh-in-right',
    'rotate-in-left',
    'rotate-in-right',
    'scale-in-center',
);
?>
<!doctype html>
<html amp lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo esc_html( get_the_title( $post_id ) ); ?></title>
    <link rel="canonical" href="<?php echo esc_url( $canonical_url ); ?>">
    <meta
        name="viewport"
        content="width=device-width,minimum-scale=1,initial-scale=1,maximum-scale=1"
    >

    <!-- Core AMP scripts -->
    <script async src="https://cdn.ampproject.org/v0.js"></script> <!-- phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -->
    <script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script> <!-- phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -->

    <!-- Conditionally load amp-ad if we have a publisher_code -->
    <?php if ( ! empty( $ads_config['publisher_code'] ) ) : ?>
        <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script> <!-- phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -->
        <script async custom-element="amp-story-auto-ads" src="https://cdn.ampproject.org/v0/amp-story-auto-ads-0.1.js"></script> <!-- phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -->
    <?php endif; ?>

    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1,end) 0s 1 normal both;
               -moz-animation: -amp-start 8s steps(1,end) 0s 1 normal both;
               -ms-animation: -amp-start 8s steps(1,end) 0s 1 normal both;
               animation: -amp-start 8s steps(1,end) 0s 1 normal both
        }
        @-webkit-keyframes -amp-start {
            from {visibility: hidden} to {visibility: visible}
        }
        @-moz-keyframes -amp-start {
            from {visibility: hidden} to {visibility: visible}
        }
        @-ms-keyframes -amp-start {
            from {visibility: hidden} to {visibility: visible}
        }
        @-o-keyframes -amp-start {
            from {visibility: hidden} to {visibility: visible}
        }
        @keyframes -amp-start {
            from {visibility: hidden} to {visibility: visible}
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>

    <style amp-custom>
        /* Base styles for body */
        body {
            margin: 0;
            padding: env(safe-area-inset-top)
                     env(safe-area-inset-right)
                     env(safe-area-inset-bottom)
                     env(safe-area-inset-left);
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Story wrapper uses dynamic sizing */
        .story-wrapper {
            width: 90%;
            max-width: 400px;
            /* Maintain the original aspect ratio (400:711) */
            aspect-ratio: 400 / 711;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        @media (max-width: 600px) {
            .story-wrapper {
                width: 100vw;
                height: 100vh;
                max-width: none;
                aspect-ratio: auto;
            }
        }

        /* Ensure the amp-story fills its container */
        amp-story {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        /* Fill-layer pages */
        amp-story-page,
        amp-story-page > amp-story-grid-layer {
            width: 100%;
            height: 100%;
        }
        amp-story-grid-layer[template="fill"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .overlay {
            background: rgba(0,0,0,0.5);
            width: 100%;
            height: 100%;
        }

        amp-story-grid-layer[template="vertical"] {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 0;
        }

        /* Desktop text container using dynamic values */
        .text-container {
            position: absolute;
            bottom: 18%; /* originally approximates 130px in a 711px tall container */
            left: 0;
            right: 0;
            width: 90%;
            max-width: 400px;
            margin: 0 auto;
            text-align: left;
        }
        /* Desktop CTA container using dynamic values */
        .cta-container {
            position: absolute;
            bottom: 10%; /* originally approximates 70px in a 711px tall container */
            left: 0;
            right: 0;
            width: 90%;
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }

        .left-text {
            width: 100%;
            text-align: left;
        }
        .left-text h2,
        .left-text p {
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .left-text h2 {
            margin-bottom: 10px;
        }
        .left-text p {
            margin-bottom: 10px;
        }
        .cta-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 2%;
        }
        .cta-wrapper a {
            display: inline-block;
            padding: 2% 4%;
            background: <?php echo esc_attr( $cta_color ); ?>;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .logo-container {
            position: relative;
            margin-top: calc(2% + env(safe-area-inset-top));
            margin-left: calc(2% + env(safe-area-inset-left));
        }
        .logo-container amp-img {
            display: block;
        }

        /* Mobile Styles */
        @media (max-width: 600px) {
            .text-container {
                width: calc(100% - 4%);
                left: 2%;
                right: auto;
                margin: 0;
                /* Increase bottom offset by roughly 50px (approx. 7%) */
                bottom: calc(18% + 7%);
                text-align: left;
            }
            .cta-container {
                width: 100%;
                left: 0;
                right: 0;
                margin: 0 auto;
                /* Increase bottom offset by roughly 50px (approx. 7%) */
                bottom: calc(10% + 7%);
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <amp-story class="story-wrapper"
        standalone
        title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
        publisher="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
        publisher-logo-src="<?php echo esc_url( $logo_url ); ?>"
        poster-portrait-src="<?php echo esc_url( get_the_post_thumbnail_url( $post_id ) ?: $logo_url ); ?>"
    >
        <!-- BEGIN: Our normal story pages -->
        <?php
        $page_count  = 1;
        $total_pages = count( $pages );

        foreach ( $pages as $page ) :
            // Get raw values first.
            $raw_image_url  = isset( $page['image_url'] ) ? $page['image_url'] : '';
            $raw_page_title = isset( $page['page_title'] ) ? $page['page_title'] : '';
            $raw_page_text  = isset( $page['page_text'] ) ? $page['page_text'] : '';
            $raw_anim       = isset( $page['animation_type'] ) ? trim( $page['animation_type'] ) : 'fade-in';
            $anim_type      = in_array( $raw_anim, $valid_animations, true ) ? $raw_anim : 'fade-in';
            $anim_dur       = isset( $page['animation_duration'] ) ? intval( $page['animation_duration'] ) : 500;
            ?>
            <amp-story-page
                id="page-<?php echo esc_attr( $page_count ); ?>"
                auto-advance-after="5s"
            >
                <amp-story-grid-layer template="fill">
                    <amp-img
                        src="<?php echo esc_url( $raw_image_url ); ?>"
                        layout="responsive"
                        width="720"
                        height="1280"
                        alt="Background image"
                        animate-in="<?php echo esc_attr( $anim_type ); ?>"
                        animate-in-duration="<?php echo esc_attr( $anim_dur ); ?>"
                    ></amp-img>
                </amp-story-grid-layer>

                <amp-story-grid-layer template="fill">
                    <div class="overlay"></div>
                </amp-story-grid-layer>

                <amp-story-grid-layer template="fill">
                    <div class="logo-container">
                        <amp-img
                            src="<?php echo esc_url( $logo_url ); ?>"
                            width="30"
                            height="30"
                            layout="fixed"
                            alt="Publisher Logo"
                        ></amp-img>
                    </div>
                </amp-story-grid-layer>

                <amp-story-grid-layer template="vertical">
                    <!-- Text container -->
                    <div class="text-container">
                        <div class="left-text">
                            <h2
                                animate-in="<?php echo esc_attr( $anim_type ); ?>"
                                animate-in-duration="<?php echo esc_attr( $anim_dur ); ?>"
                            >
                                <?php echo esc_html( $raw_page_title ); ?>
                            </h2>
                            <p animate-in="fade-in" animate-in-duration="500">
                                <?php echo esc_html( $raw_page_text ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- CTA container (only output if conditions are met) -->
                    <?php if (
                        ! empty( $cta_text ) &&
                        ! empty( $cta_link ) &&
                        (
                            $cta_position === 'all_pages' ||
                            ( $cta_position === 'last_page' && $page_count === $total_pages )
                        )
                    ) : ?>
                        <div class="cta-container">
                            <div
                                class="cta-wrapper"
                                animate-in="fade-in"
                                animate-in-duration="500"
                            >
                                <a
                                    href="<?php echo esc_url( $cta_link ); ?>"
                                    target="_blank"
                                >
                                    <?php echo esc_html( $cta_text ); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </amp-story-grid-layer>
            </amp-story-page>
            <?php
            $page_count++;
        endforeach;
        ?>
        <!-- END: Normal story pages -->

        <!-- BEGIN: Auto Ads (optional) -->
        <?php if ( ! empty( $ads_config['publisher_code'] ) ) : ?>
            <amp-story-auto-ads>
                <script type="application/json">
                {
                    "ad-attributes": {
                        "type": "adsense",
                        "data-ad-client": "<?php echo esc_js( $ads_config['publisher_code'] ); ?>",
                        "data-ad-slot": "<?php echo esc_js( isset( $ads_config['ad_slot'] ) ? $ads_config['ad_slot'] : '1234567890' ); ?>"
                    }
                }
                </script>
            </amp-story-auto-ads>
        <?php endif; ?>
        <!-- END: Auto Ads -->
    </amp-story>
</body>
</html>
