<?php

namespace App\JsonApi\Authors;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'authors';

    /**
     * @param \App\Models\Author $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \App\Models\Author $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }


    /**
     * Get resource links.
     *
     * @param object $author
     * @param bool   $isPrimary
     * @param array  $includeRelationships A list of relationships that will be included as full entrys.
     *
     * @return array
     */
    public function getRelationships($author, $isPrimary, array $includeRelationships)
    {
        return [
            'entries' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['entries']),
                self::DATA => function () use ($author) {
                    return $author->entries;
                }
            ]
        ];
    }
}
