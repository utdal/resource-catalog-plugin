<?php

namespace UTDallasResourceCatalog\PostTypes;

abstract class CustomPost
{
    /** @var string Name. Defaults to lowercase class basename. */
    public $name;

    /** @var string Singular name. Defaults to capitalized class basename */
    public $singular;

    /** @var string Plural name. Defaults to singular name + 's' */
    public $plural;

    /** @var array WordPress custom post type settings */
    public $settings = [];

    /** @var array Display labels */
    public $labels = [];

    /** @var array Custom field descriptors */
    public $custom_fields = [];

    /** @var array Settings for the meta box container for custom fields */
    public $metabox_settings = [];

    /** @var array Custom taxonomies specific to this post type */
    public $custom_taxonomies = [];

    /**
     * Class constructor. Sets defaults if not specified.
     */
    public function __construct()
    {
        $this->name = $this->name ?: strtolower((new \ReflectionClass($this))->getShortName());
        $this->singular = $this->singular ?: ucfirst($this->name);
        $this->plural = $this->plural ?: $this->singular . 's';
        $this->labels = $this->labels + $this->defaultLabels();
        $this->metabox_settings = $this->metabox_settings ?: $this->defaultMetaboxSettings();
    }

    /**
     * Registers this custom post type with WordPress.
     * 
     * @return WP_Post_Type|WP_Error The registered post type object, or an error object.
     */
    public function register()
    {
        $settings = $this->settings + ['label' => $this->plural] + ['labels' => $this->labels];

        return register_post_type($this->name, $settings);
    }

    /**
     * Registers any custom fields associated with this custom post type.
     *
     * This uses the CMB2 library.
     * 
     * @return int number of fields registered
     */
    public function registerFields()
    {
        $num_registered = 0;

        if ($this->custom_fields) {
            $box = new_cmb2_box($this->metabox_settings);

            foreach ($this->custom_fields as $custom_field) {
                $box->add_field($custom_field);
                $num_registered++;
            }
        }

        return $num_registered;
    }

    /**
     * Registers and custom fields to the custom post API
     *
     * @return void
     */
    public function registerFieldAPIs()
    {
        foreach ($this->custom_fields as $custom_field) {
            register_rest_field($this->name, $custom_field['id'], [
                'get_callback' => function ($custom_post) use ($custom_field) {
                    return get_post_meta($custom_post['id'], $custom_field['id'], true);
                },
                'schema' => [
                    'description' => $custom_field['name'],
                    'type' => 'text'
                ],
            ]);
        }
    }

    /**
     * Registers any custom taxonomies specific to this custom post type
     *
     * @return int number of taxonomies registered
     */
    public function registerTaxonomies()
    {
        $num_registered = 0;

        foreach ($this->custom_taxonomies as $custom_taxonomy => $args) {
            register_taxonomy($custom_taxonomy, $this->name, $args);
        }

        return $num_registered;
    }

    /**
     * Determines if this custom post type has the named custom field.
     * 
     * @param  string  $field
     * @return boolean
     */
    public function hasField($field)
    {
        return array_search($field, array_column($this->custom_fields, 'id')) !== false;
    }

    /**
     * Gets the stored value of a custom field.
     * 
     * @param  string $field
     * @return mixed|null
     */
    public function getFieldValue($field)
    {
        return get_post_meta(get_the_ID(), $field, true);
    }

    /**
     * Gets the stored values of an array of custom fields.
     * 
     * @param  array  $fields
     * @return array
     */
    public function getFieldValues(array $fields)
    {
        return array_map([$this, 'getFieldValue'], $fields);
    }

    /**
     * Gets any non-empty field values of an array of custom fields.
     * 
     * @param  array  $fields
     * @return array
     */
    public function getNonEmptyFieldValues(array $fields)
    {
        $values = $this->getFieldValues($fields);

        return array_filter($values, function($value) {
            return !empty($value);
        });
    }

    /**
     * Generates a set of default labels based on the singular and plural names.
     * 
     * @return array
     */
    public function defaultLabels()
    {
        return [
            'name'                  => $this->plural,
            'singular_name'         => $this->singular,
            'menu_name'             => $this->plural,
            'all_items'             => "All $this->plural",
            'add_new'               => "Add $this->singular",
            'add_new_item'          => "Add New $this->singular",
            'edit'                  => "Edit",
            'edit_item'             => "Edit $this->singular",
            'new_item'              => "New $this->singular",
            'view'                  => "View $this->singular",
            'view_item'             => "View $this->singular",
            'search_items'          => "Search $this->plural",
            'not_found'             => "No $this->plural Found",
            'not_found_in_trash'    => "No $this->plural Found in Trash",
            'parent'                => "Parent $this->singular",
        ];
    }

    /**
     * Generates default meta box container settings.
     * 
     * @return array
     */
    public function defaultMetaboxSettings()
    {
        return [
            'id'            => $this->name . '_custom_fields_box',
            'object_types'  => [$this->name],
            'context'       => 'after_title',
            'remove_box_wrap'       => true,
        ];
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    protected static function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    /**
     * Magic properties.
     *
     * 1) Access custom fields as class properties, by field id
     * 2) Access virtual attributes from get[StudlyName]Attribute() methods
     * 
     * @param  string $property
     * @return mixed|null
     */
    public function __get($property)
    {
        if ($this->hasField($property)) {
            return $this->getFieldValue($property);
        }

        if (method_exists($this, 'get'.static::studly($property).'Attribute')) {
            return $this->{'get'.static::studly($property).'Attribute'}();
        }

        return null;
    }
}