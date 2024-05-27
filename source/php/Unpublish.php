<?php

namespace ContentScheduler;

class Unpublish
{

    private int $saveUnpublishProiority = 1;

    public function __construct()
    {
        add_action('post_submitbox_misc_actions', array($this, 'setupUi'));
        add_action('save_post', array($this, 'saveUnpublish'), $this->saveUnpublishProiority);
        add_action('unpublish_post', array($this, 'unpublishPost'));
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
                    'ID' => (int) $postId,
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
    public function saveUnpublish($postId): void
    {
        // Do not proceed if post is a revision
        if ($this->isPostRevision($postId)) {
            return;
        }

        // Remove previous event
        $this->unschedulePreviousEvent($postId);

        // Clear event metadata if the 'unpublish-active' flag is not set to 'true'
        // And abort futher processing.
        if ($this->clearEventMetadata($postId)) {
            return;
        }

        //Allocate the necessary data
        $eventAction        = $this->getDesiredAction($postId);
        $eventTimeMetadata  = $this->getUnpublishTimeMetadata();
        $eventTimestamp     = $this->compileEventTimestamp($eventTimeMetadata);

        //Update posts meta accordingly
        update_post_meta($postId, 'unpublish-date', $eventTimeMetadata);
        update_post_meta($postId, 'unpublish-action', $eventAction);

        //Schedule new event
        wp_schedule_single_event($eventTimestamp, 'unpublish_post', array(
            'post_id' => $postId,
            'action' => $eventAction
        ));

        //Remove this action to avoid multiple posts being unscheduled
        remove_action('save_post', array($this, 'saveUnpublish'), $this->saveUnpublishProiority);
    }

    /**
     * Clears the event metadata if the 'unpublish-active' flag is not set to 'true'.
     *
     * @return bool Returns true if the event metadata is cleared, false otherwise.
     */
    private function clearEventMetadata($postId): bool
    {
        if (!isset($_POST['unpublish-active']) || $_POST['unpublish-active'] != 'true') {
            delete_post_meta($postId, 'unpublish-date');
            delete_post_meta($postId, 'unpublish-action');
            return true;
        }
        return false;
    }

    /**
     * Gets the metadata for the unpublish time.
     *
     * @return array The metadata for the unpublish time.
     * @throws \Exception If any of the required keys are missing.
     */
    private function getUnpublishTimeMetadata(): array
    {
        $keys = array('unpublish-aa', 'unpublish-mm', 'unpublish-jj', 'unpublish-hh', 'unpublish-mn');
        foreach ($keys as $key) {
            if (!array_key_exists($key, $_POST)) {
                throw new \Exception('Could not generate a unschedule date due to missing key: ' . $key);
            }
        }

        return array(
            'aa' => $_POST['unpublish-aa'],
            'mm' => $_POST['unpublish-mm'],
            'jj' => $_POST['unpublish-jj'],
            'hh' => $_POST['unpublish-hh'],
            'mn' => $_POST['unpublish-mn']
        );
    }

    /**
     * Compiles the event timestamp based on the given meta data.
     *
     * @param array $meta The meta data containing the event details.
     * @return string The compiled event timestamp in the format 'YYYY-MM-DD HH:MM:SS'.
     */
    private function compileEventTimestamp($meta): string
    {
        $offset = $this->getTimeZoneOffset();
        $timestamp = $meta['aa'] . '-' . $meta['mm'] . '-' . $meta['jj'] . ' ' . $meta['hh'] . ':' . $meta['mn'] . ':00';
        $eventTimestamp = gmdate('Y-m-d H:i:s', strtotime($timestamp . ' ' . $offset));
        return $eventTimestamp;
    }

    /**
     * Gets the desired action to make for the post.
     *
     * @param int $postId The ID of the post.
     * @return string The requested action.
     */
    private function getDesiredAction($postId): string
    {
        return isset($_POST['unpublish-action']) && !empty($_POST['unpublish-action']) ? $_POST['unpublish-action'] : 'trash';
    }

    /**
     * Gets the timezone offset.
     *
     * @return string The timezone offset.
     */
    private function getTimeZoneOffset() {
        $offset = get_option('gmt_offset');

        if ($offset > -1) {
            $offset = '-' . $offset;
        } else {
            $offset = '+' . (1 * abs($offset));
        }

        return $offset;
    }

    /**
     * Checks if a post is a revision.
     *
     * @param int $postId The ID of the post to check.
     * @return bool True if the post is a revision, false otherwise.
     */
    private function isPostRevision($postId): bool {
        return wp_is_post_revision($postId);
    }

    /**
     * Unschedules the previous event for a specific post.
     *
     * @param int $postId The ID of the post.
     * @return void
     */
    private function unschedulePreviousEvent($postId): void {
        $args = array(
            'post_id' => $postId
        );
        wp_unschedule_event(
            wp_next_scheduled(
                'unpublish_post', $args
            ), 
            'unpublish_post', $args
        );
    }

    /**
     * The UI for the unpublish section
     * @return void
     */
    public function setupUi($post): void
    {
        include CONTENTSCHEDULER_TEMPLATE_PATH . '/unpublish.php';
    }
}
