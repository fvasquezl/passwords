<?php

namespace App\JsonApi\Categories;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'categories';

    /**
     * @param \App\Models\Category $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Models\Category $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'slug' => $resource->slug,
        ];
    }


    /**
     * Get resource links.
     *
     * @param object $category
     * @param bool   $isPrimary
     * @param array  $includeRelationships A list of relationships that will be included as full entrys.
     *
     * @return array
     */
    public function getRelationships($category, $isPrimary, array $includeRelationships)
    {
        return [
            'entries' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['entries']),
                self::DATA => function () use ($category) {
                    return $category->entries;
                }
            ]
        ];
    }
}
