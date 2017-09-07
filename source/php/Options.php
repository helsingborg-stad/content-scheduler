<?php

namespace ContentScheduler;

class Options
{
    public function __construct()
    {
        if (function_exists('acf_add_options_sub_page')) {
            acf_add_options_sub_page(array(
                'page_title'    => __('Content scheduler options', 'content-scheduler'),
                'menu_title'    => __('Content scheduler', 'content-scheduler'),
                'menu_slug'     => 'content-scheduler-options',
                'parent_slug'   => 'options-general.php',
                'capability'    => 'manage_options'
            ));
        }
    }
}
