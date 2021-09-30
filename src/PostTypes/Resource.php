<?php

namespace UTDallasResourceCatalog\PostTypes;

class Resource extends CustomPost
{
    /** @var string Internal Name. */
    public $name = 'resource';

    /** @var string Singular name. */
    public $singular = 'Resource';

    /** @var array Display labels */
    public $labels = [
        'menu_name' => 'Catalog',
    ];

    /** @var array WordPress custom post type settings */
    public $settings = [
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "delete_with_user" => false,
        "show_in_rest" => true,
        // "rest_base" => "",
        // "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "exclude_from_search" => false,
        'menu_icon' => 'dashicons-book-alt',
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => true,
        "rewrite" => [
            "slug" => "resource",
            "with_front" => true,
        ],
        "query_var" => true,
        "supports" => [
            "title",
            "editor",
            "excerpt",
            "revisions",
            "thumbnail",
            "page-attributes",
            "custom-fields",
        ],
        'taxonomies' => [
            'post_tag',
            'category',
        ],
    ];

    /** @var array Custom field settings (CMB2) */
    public $custom_fields = [
        [
            'name'  => 'Link',
            'desc'  => 'Add a link (internal or external) for the item.',
            'id'    => 'resource_link',
            'type'  => 'text_url',
            'protocols' => ['http', 'https', 'mailto'],
        ],
    ];

    public $metabox_settings = [
        'id'            => 'resource_metabox',
        'title'         => 'Resource Attributes',
        'object_types'  => ['resource'],
        'context'       => 'side',
        'priority'      => 'default',
        'show_names'    => true,
        'show_in_rest'  => true,
    ];

}