<?php


namespace Viscaweb;


class SportsbookMetabox
{
    public function __construct()
    {
        add_action('load-post.php', [$this ,'sportsbook_post_meta_boxes_setup']);
        add_action('load-post-new.php', [$this ,'sportsbook_post_meta_boxes_setup']);
    }

    public function sportsbook_post_meta_boxes_setup()
    {
        /* Add meta boxes on the 'add_meta_boxes' hook. */
        add_action('add_meta_boxes', [$this, 'sportsbook_add_post_meta_boxes']);

        /* Save post meta on the 'save_post' hook. */
        add_action('save_post', [$this,'sportsbook_save_meta'], 10, 2);
    }

    public function sportsbook_add_post_meta_boxes()
    {
        $api = new Api();
        $bookmakers = $api->get_bookmakers_data();

        if ($bookmakers) {
            add_meta_box(
                'sportsbook-id',      // Unique ID
                esc_html__('Sportsbooks', 'example'),    // Title
                [$this, 'sportsbook_meta_data_meta_box'],   // Callback function
                'page',         // Admin page (or post type)
                'side',         // Context
                'default',      //priority
                $bookmakers     //callback_args
            );
        }
    }

    public function sportsbook_meta_data_meta_box($post, $sportsbooks)
    {
        wp_nonce_field(basename(__FILE__), 'sportsbook_meta_data_nonce'); ?>

        <p>
            <label for="sportsbook-id"><?php echo __("Select Sports book from list"); ?></label>
            <br/>
            <br/>

            <select name="sportsbook-id" id="sportsbook-id">

                <?php

                $currentValue = esc_attr(get_post_meta($post->ID, 'sportsbook_meta_data', true));

                if (isset($sportsbooks) && !empty($sportsbooks)) { ?>

                    <option value=""><?php echo __('Choose an option') ?></option>
                    <?php

                    foreach ($sportsbooks['args'] as $sportsbook) {
                        ?>
                        <option value="<?php echo $sportsbook ?>" <?php echo ($currentValue == $sportsbook) ? 'selected' : '' ?>><?php echo $sportsbook ?></option>
                        <?php
                    }
                }
                ?>

            </select>
        </p>
    <?php }

    public function sportsbook_save_meta($post_id, $post)
    {
        /* Verify the nonce before proceeding. */
        if (!isset($_POST['sportsbook_meta_data_nonce']) || !wp_verify_nonce($_POST['sportsbook_meta_data_nonce'], basename(__FILE__)))
            return $post_id;

        /* Get the post type object. */
        $post_type = get_post_type_object($post->post_type);

        /* Check if the current user has permission to edit the post. */
        if (!current_user_can($post_type->cap->edit_post, $post_id))
            return $post_id;

        /* Get the posted data */
        $new_meta_value = (isset($_POST['sportsbook-id']) ? $_POST['sportsbook-id'] : '');

        /* Get the meta key. */
        $meta_key = 'sportsbook_meta_data';

        /* Get the meta value of the custom field key. */
        $meta_value = get_post_meta($post_id, $meta_key, true);

        /* If a new meta value was added and there was no previous value, add it. */
        if ($new_meta_value && $new_meta_value == $meta_value)
            add_post_meta($post_id, $meta_key, $new_meta_value, true);

        /* If the new meta value does not match the old value, update it. */
        elseif ($new_meta_value != '' && $new_meta_value != $meta_value)
            update_post_meta($post_id, $meta_key, $new_meta_value);

        /* If there is no new meta value but an old value exists, delete it. */
        elseif ($new_meta_value == '' && $meta_value)
            delete_post_meta($post_id, $meta_key, $meta_value);
    }
}