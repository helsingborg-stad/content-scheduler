<?php

namespace ContentScheduler;

class App
{
    public function __construct()
    {
        new Unpublish();
        new Options();

        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function isEditPage()
    {
        global $pagenow;
        $post_type = get_post_type();

        if (!is_admin() || !$post_type || $post_type === 'attachment') {
            return false;
        }

        $post_types = get_field('content_scheduler_posttypes', 'option');
        if (is_array($post_types) && !empty($post_types)) {
            if (!in_array($post_type, $post_types)) {
                return false;
            }
        }

        return in_array($pagenow, array('post.php', 'post-new.php'));
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        if (!$this->isEditPage()) {
            return false;
        }

        wp_register_style('content-scheduler', CONTENTSCHEDULER_URL . '/dist/css/content-scheduler.min.css', '', '1.0.0');
        wp_enqueue_style('content-scheduler');
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        if (!$this->isEditPage()) {
            return false;
        }

        wp_register_script('content-scheduler', CONTENTSCHEDULER_URL . '/dist/js/content-scheduler.min.js', '', '1.0.0', true);
        wp_enqueue_script('content-scheduler');
    }
}
