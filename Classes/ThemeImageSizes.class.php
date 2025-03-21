<?php

namespace Branded;

/**
 *
 */
class ThemeImageSizes
{

   public array $default_thumbnails = [
            'medium',
            'medium_large',
            // 'thumbnail',
            'large',
            '1536x1536',
            '2048x2048',
            'woocommerce_thumbnail',
            'woocommerce_single',
            'woocommerce_gallery_thumbnail',
            'shop_catalog',
            'shop_single',
            'shop_thumbnail'
        ];

    /**
     *
     */
    public function __construct()
    {
        add_filter( 'jpeg_quality', [$this, 'jpgQuality']);
        add_filter( 'intermediate_image_sizes',  [$this, 'removeDefaultImagesSizes'] );
        $this->thumbnailsSizes();
    }


    /**
     * Remove the default image sizes and the medium_large size.
     * @param $sizes
     * @return mixed
     */
    public function removeDefaultImagesSizes( $sizes ) {
        foreach($sizes as $size_index=>$size) {
            if(in_array($size, $this->default_thumbnails)) {
                unset($sizes[$size_index]);
            }
        }
        return $sizes;
    }

    /**
     * @return void
     */
    public function thumbnailsSizes()
    {
        add_image_size(
            'archive_post',
            600,
            350,
            true
        );

        // add_image_size(
        //     'archive_product',
        //     600,
        //     350,
        //     true
        // );

        add_image_size(
            'single_post_desktop',
            1200,
            600,
            true
        );

        add_image_size(
            'single_post_phone',
            450,
            290,
            true
        );

        add_image_size(
            'single_product_desktop',
            840,
            1080,
            true
        );

        add_image_size(
            'single_product_phone',
            532,
            665,
            true
        );
    }

    /**
     * We have optimized images set, do no need get more blur due to compress
     * @return int
     */
    function jpgQuality(): int
    {
        return 100;
    }
}

new ThemeImageSizes();