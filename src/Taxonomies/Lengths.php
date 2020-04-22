<?php

namespace UTDallasResourceCatalog\Taxonomies;

class Lengths extends CustomTaxonomy
{
    /** @var string Name. Defaults to lowercase class basename. */
    public $name = 'resource_lengths';

    /** @var string Singular name. Defaults to capitalized class basename */
    public $singular = 'Length';

    /** @var string Plural name. Defaults to singular name + 's' */
    public $plural = 'Lengths';

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