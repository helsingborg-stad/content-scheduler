<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_59b0f1288d51b',
    'title' => __('Content scheduler options', 'content-scheduler'),
    'fields' => array(
        0 => array(
            'key' => 'field_59b0f12893ea1',
            'label' => __('Post types', 'content-scheduler'),
            'name' => 'content_scheduler_posttypes',
            'type' => 'posttype_select',
            'instructions' => __('Activate content scheduler for selected post types.', 'content-scheduler'),
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '30',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'allow_null' => 0,
            'multiple' => 1,
            'placeholder' => '',
            'disabled' => 0,
            'readonly' => 0,
        ),
        1 => array(
            'key' => 'field_605db120c6366',
            'label' => __('Modules', 'content-scheduler'),
            'name' => 'content_scheduler_modules',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'choices' => array(
            ),
            'default_value' => array(
            ),
            'allow_null' => 0,
            'multiple' => 1,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'content-scheduler-options',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}