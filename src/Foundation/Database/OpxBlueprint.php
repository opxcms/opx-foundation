<?php

namespace Core\Foundation\Database;

use Illuminate\Database\Schema\Blueprint;

class OpxBlueprint extends Blueprint
{

    /**
     * Add id attribute to the table.
     *
     * @return  void
     */
    public function id(): void
    {
        $this->increments('id');
    }

    /**
     * Add parent_id attribute to the table.
     *
     * @param string $column
     *
     * @return  void
     */
    public function parentId($column = 'parent_id'): void
    {
        $this->integer($column)->default(0);
    }

    /**
     * Add alias attribute to the table.
     *
     * @return  void
     */
    public function alias(): void
    {
        $this->string('alias')->nullable();
    }

    /**
     * Add name attribute to the table.
     *
     * @return  void
     */
    public function name(): void
    {
        $this->string('name');
    }

    /**
     * Add order attribute to the table.
     *
     * @param string $column
     *
     * @return  void
     */
    public function order($column = 'order'): void
    {
        $this->integer($column)->default(null)->nullable();
    }

    /**
     * Add content attribute to the table.
     *
     * @return  void
     */
    public function content(): void
    {
        $this->longText('content')->nullable();
    }

    /**
     * Add image attributes to the table.
     *
     * @param string $column
     *
     * @return  void
     */
    public function image($column = 'image'): void
    {
        $this->text($column)->nullable();
    }

    /**
     * Add data field to the table.
     *
     * @return  void
     */
    public function data(): void
    {
        $this->longText('data')->nullable();
    }

    /**
     * Add template field to the table.
     *
     * @param string $column
     *
     * @return  void
     */
    public function template($column = 'template'): void
    {
        $this->string($column)->nullable();
    }

    /**
     * Add data field to the table.
     *
     * @param string $column
     *
     * @return  void
     */
    public function layout($column = 'layout'): void
    {
        $this->string($column)->nullable();
    }

    /**
     * Add sitemap attributes to the table.
     *
     * @return  void
     */
    public function sitemap(): void
    {
        $this->boolean('site_map_enable')->default(1)->nullable();
        $this->enum('site_map_update_frequency', ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'])->default('monthly');
        $this->string('site_map_priority')->default('0.50')->nullable();
        $this->boolean('site_map_last_mod_enable')->default(1)->nullable();
    }

    /**
     * Add robots indexing attributes to the table.
     *
     * @return  void
     */
    public function robots(): void
    {
        $this->boolean('no_index')->default(0)->nullable();
        $this->boolean('no_follow')->default(0)->nullable();
        $this->string('canonical')->nullable();
    }

    /**
     * Add SEO attributes to the table.
     *
     * @return  void
     */
    public function seo(): void
    {
        $this->string('meta_title')->nullable();
        $this->string('meta_keywords')->nullable();
        $this->text('meta_description')->nullable();
    }

    /**
     * Add publication attributes to the table.
     *
     * @return  void
     */
    public function publication(): void
    {
        $this->boolean('published')->default(1)->nullable();
        $this->timestamp('publish_start')->nullable();
        $this->timestamp('publish_end')->nullable();
    }
}