<?php

namespace UTDallasResourceCatalog\Taxonomies;

class Programs extends CustomTaxonomy
{
    /** @var string Name. Defaults to lowercase class basename. */
    public $name = 'resource_programs';

    /** @var string Singular name. Defaults to capitalized class basename */
    public $singular = 'Program';

    /** @var string Plural name. Defaults to singular name + 's' */
    public $plural = 'Programs';

    /** @var array WordPress custom taxonomy settings */
    public $settings = [
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => [
            'slug' => 'type',
        ],
    ];

}