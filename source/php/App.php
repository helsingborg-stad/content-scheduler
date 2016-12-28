<?php

namespace ContentScheduler;

class App
{
    public function __construct()
    {
        new \ContentScheduler\Unpublish();

        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function isEditPage()
    {
        global $pagenow;

        if (!is_admin()) {
            return false;
        }

        if (get_post_type() === 'attachment') {
            return false;
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
