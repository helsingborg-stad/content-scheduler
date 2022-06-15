<?php

namespace ContentScheduler;

class Unpublish
{
    public function __construct()
    {
        add_action('post_submitbox_misc_actions', array($this, 'setupUi'));
        add_action('save_post', array($this, 'saveUnpublish'));
        add_action('unpublish_post', array($this, 'unpublishPost'), 10, 2);
    }

    /**
     * Does the actual unpublishing, called from cron
     * @param  integer $postId The post to unpublisch
     * @param  string $action  What to do with it (trash or draft)
     * @return boolean
     */
    public function unpublishPost($postId, $action = 'trash')
    {
        $postStatus = get_post_status($postId);
        if (!in_array($postStatus, array('publish', 'private', 'password', 'draft'))) {
            return false;
        }

        switch ($action) {
            case 'draft':
                wp_update_post(array(
                    'ID' => (int)$postId,
                    'post_status' => 'draft'
                ));
                break;

            default:
                wp_trash_post($postId);
                break;
        }

        return true;
    }

    /**
     * Saves the unpublish settings and sets up scheduled task
     * @param  integer $postId Post id
     * @return void
     */
    public function saveUnpublish()
    {
        $postId = $_POST['post_ID'];
        
        // Do not proceed if post is a revision
        if (wp_is_post_revision($postId)) {
            return false;
        }

        $args = array(
            'post_id' => $postId
        );

        wp_unschedule_event(wp_next_scheduled('unpublish_post', $args), 'unpublish_post', $args);

        if (!isset($_POST['unpublish-active']) || $_POST['unpublish-active'] != 'true') {
            delete_post_meta($postId, 'unpublish-date');
            delete_post_meta($postId, 'unpublish-action');

            return;
        }

        $offset = get_option('gmt_offset');

        if ($offset > -1) {
            $offset = '-' . $offset;
        } else {
            $offset = '+' . (1 * abs($offset));
        }

        $unpublishDate = $_POST['unpublish-aa'] . '-' . $_POST['unpublish-mm'] . '-' . $_POST['unpublish-jj'] . ' ' . $_POST['unpublish-hh'] . ':' . $_POST['unpublish-mn'] . ':00';
        $unpublishTime = strtotime($offset . ' hours', strtotime($unpublishDate));

        $unpubParts = array(
            'aa' => $_POST['unpublish-aa'],
            'mm' => $_POST['unpublish-mm'],
            'jj' => $_POST['unpublish-jj'],
            'hh' => $_POST['unpublish-hh'],
            'mn' => $_POST['unpublish-mn']
        );

        $action = isset($_POST['unpublish-action']) && !empty($_POST['unpublish-action']) ? $_POST['unpublish-action'] : 'trash';

        update_post_meta($postId, 'unpublish-date', $unpubParts);
        update_post_meta($postId, 'unpublish-action', $action);

        wp_schedule_single_event($unpublishTime, 'unpublish_post', array(
            'post_id' => $postId,
            'action' => $action
        ));
    }

    /**
     * The UI for the unpublish section
     * @return void
     */
    public function setupUi($post)
    {
        include CONTENTSCHEDULER_TEMPLATE_PATH . '/unpublish.php';
    }
}
