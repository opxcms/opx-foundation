<?php

namespace Core\Foundation\Template;

trait SeoFields
{
    public static function metaTitle(string $placement = 'seo/common'): array
    {
        return self::string('meta_title', $placement, '', ['counter' => ['min' => 20, 'max' => 120]]);
    }

    public static function metaKeywords(string $placement = 'seo/common'): array
    {
        return self::string('meta_keywords', $placement, '', [], 'fields.meta_keywords_info');
    }

    public static function metaDescription(string $placement = 'seo/common'): array
    {
        return self::text('meta_description', $placement, '', ['counter' => ['min' => 20, 'max' => 120]]);
    }

    public static function robotsNoIndex(string $placement = 'seo/robots'): array
    {
        return self::checkbox('no_index', $placement, false, 'fields.no_index_info');
    }

    public static function robotsNoFollow(string $placement = 'seo/robots'): array
    {
        return self::checkbox('no_follow', $placement, false, 'fields.no_follow_info');
    }

    public static function robotsCanonical(string $placement = 'seo/robots'): array
    {
        return self::string('canonical', $placement, '', [], 'fields.canonical_info');
    }

    public static function sitemapEnable(string $placement = 'seo/sitemap'): array
    {
        return self::checkbox('site_map_enable', $placement, true);
    }

    public static function sitemapUpdateFrequency(string $placement = 'seo/sitemap'): array
    {
        return self::select('site_map_update_frequency', $placement, 'monthly', [
            'always' => 'fields.site_map_update_frequency_always',
            'hourly' => 'fields.site_map_update_frequency_hourly',
            'daily' => 'fields.site_map_update_frequency_daily',
            'weekly' => 'fields.site_map_update_frequency_weekly',
            'monthly' => 'fields.site_map_update_frequency_monthly',
            'yearly' => 'fields.site_map_update_frequency_yearly',
            'never' => 'fields.site_map_update_frequency_never',
        ]);
    }

    public static function sitemapPriority(string $placement = 'seo/sitemap'): array
    {
        return self::string('site_map_priority', $placement, '0.50');
    }

    public static function sitemapLastModEnable(string $placement = 'seo/sitemap'): array
    {
        return self::checkbox('site_map_last_mod_enable', $placement, true);
    }
}