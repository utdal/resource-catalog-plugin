<?php

namespace UTDallasResourceCatalog\Shortcodes;

abstract class Shortcode
{
    /** @var string Shortcode name. Defaults to lowercase class basename. */
    public $name;

    /** @var array Shortcode attributes (options) */
    public $attributes;

    /** @var array Default shortcode attributes */
    public $default_attributes = [];

    /** @var array Filters to apply to attributes */
    public $attribute_filters = [];

    /** @var array Filter options to apply to attributes */
    public $attribute_filter_options = [];

    /**
     * Shortcode class constructor.
     */
    public function __construct()
    {
        $this->name = $this->name ?: strtolower((new \ReflectionClass($this))->getShortName());
    }

    /**
     * Registers this shortcode with WordPress
     */
    public function register()
    {
        add_shortcode($this->name, [$this, 'run']);
    }

    /**
     * Run the shortcode: set attributes, then render.
     * 
     * @param  array $attributes The attributes used on the shortcode
     */
    public function run($attributes)
    {
        $this->setAttributes($attributes);

        return $this->render();
    }

    /**
     * Sets the provided attributes, filling in defaults and filtering as needed.
     * 
     * @param array $attributes The attributes used on the shortcode
     */
    protected function setAttributes($attributes)
    {
        $this->attributes = shortcode_atts($this->default_attributes, $attributes);

        foreach ($this->attribute_filters as $attribute => $filter) {
            $this->attributes[$attribute] = filter_var($this->attributes[$attribute], $filter, $this->attribute_filter_options[$attribute] ?? []);
        }
    }

    /**
     * Render the shortcode. This method should be overridden on the child class.
     * 
     * @return string The rendered replacement text for the shortcode
     */
    public function render()
    {
        return "[{$this->name}]: Please override this with a custom render function.";
    }

    /**
     * Magic property getter.
     *
     * 1) Access attributes as class properties, by attribute name.
     * 
     * @param  string $property The name of the property to access
     * @return mixed|null
     */
    public function __get($property)
    {
        if ($this->attributes && array_key_exists($property, $this->attributes)) {
            return $this->attributes[$property];
        }

        return null;
    }

    /**
     * Magic property setter.
     *
     * This can be used to either override attributes or set custom ones.
     * 
     * @param string $property The name of the property to set
     * @param mixed  $value    The value to set
     */
    public function __set($property, $value)
    {
        $this->attributes[$property] = $value;
    }
}
