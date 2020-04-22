<?php

namespace UTDallasResourceCatalog\Taxonomies;

class Audiences extends CustomTaxonomy
{
    /** @var string Name. Defaults to lowercase class basename. */
    public $name = 'resource_audiences';

    /** @var string Singular name. Defaults to capitalized class basename */
    public $singular = 'Audience';

    /** @var string Plural name. Defaults to singular name + 's' */
    public $plural = 'Audiences';

    /** @var array WordPress custom taxonomy settings */
    public $settings = [
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'query_var' => true,
        'rewrite' => [
            'slug' => 'type',
        ],
    ];

}