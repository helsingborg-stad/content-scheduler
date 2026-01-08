<?php

namespace ContentScheduler;

class App
{
    private $cacheBust;

    public function __construct()
    {
        new Unpublish();

        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));

        $this->cacheBust = new \ContentScheduler\Helper\CacheBust();
    }

    public function isEditPage()
    {
        global $pagenow;
        $post_type = get_post_type();

        if (!is_admin() || !$post_type || $post_type === 'attachment') {
            return false;
        }

        return in_array($pagenow, array('post.php', 'post-new.php'));
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

        wp_register_style(
            'content-scheduler-css',
            CONTENTSCHEDULER_URL . '/dist/' .
                $this->cacheBust->name('css/content-scheduler.css')
        );

        wp_enqueue_style('content-scheduler-css');

        wp_register_script(
            'content-scheduler-js',
            CONTENTSCHEDULER_URL . '/dist/' .
                $this->cacheBust->name('js/content-scheduler.js'),
            array(),
            '1.0.0',
            true
        );
        wp_enqueue_script('content-scheduler-js');
    }
}
