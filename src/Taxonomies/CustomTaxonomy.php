<?php

namespace UTDallasResourceCatalog\Taxonomies;

abstract class CustomTaxonomy
{
    /** @var string Name. Defaults to lowercase class basename. */
    public $name;

    /** @var array|string Name of the objects to register this to. */
    public $objects = [];

    /** @var string Singular name. Defaults to capitalized class basename */
    public $singular;

    /** @var string Plural name. Defaults to singular name + 's' */
    public $plural;

    /** @var array WordPress custom taxonomy settings */
    public $settings = [];

    /** @var array Display labels */
    public $labels = [];

    /**
     * Class constructor. Sets defaults if not specified.
     */
    public function __construct($objects = [])
    {
        $this->objects = $objects ?: $this->defaultObjects();
        $this->name = $this->name ?: strtolower((new \ReflectionClass($this))->getShortName());
        $this->singular = $this->singular ?: ucfirst($this->name);
        $this->plural = $this->plural ?: $this->singular . 's';
        $this->labels = $this->labels + $this->defaultLabels();
    }

    public function register()
    {
        $settings = $this->settings + ['label' => $this->plural] + ['labels' => $this->labels];

        register_taxonomy($this->name, $this->objects, $settings);

        // Better safe than sorry for custom posts
        foreach ($this->objects as $object) {
            if (!in_array($object, ['post', 'page', 'attachment', 'menu'])) {
                register_taxonomy_for_object_type($this->name, $object);
            }
        }
    }

    /**
     * Generates a set of default labels based on the singular and plural names.
     * 
     * @return array
     */
    public function defaultLabels()
    {
        return [
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'search_items' => "Search $this->plural",
            'not_found' => "No $this->plural Found",
            'not_found_in_trash' => "No $this->plural Found in Trash",
            'all_items' => "All $this->plural",
            'parent_item' => "Parent $this->singular",
            'parent_item_colon' => "Parent $this->singular:",
            'edit_item' => "Edit $this->singular",
            'update_item' => "Update $this->singular",
            'add_new_item' => "Add New $this->singular",
            'new_item_name' => "New $this->singular",
            'menu_name' => $this->plural,
        ];
    }

    /**
     * The default objects to which to associate this custom taxonomy
     *
     * @return array
     */
    public function defaultObjects()
    {
        return [
            'post',
        ];
    }
}