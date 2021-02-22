<?php

namespace App\JsonApi\Entries;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'entries';

    /**
     * @param \app\Models\Entry $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param \app\Models\Entry $entry
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($entry)
    {
        return [
            'name' =>  $entry->name,
            'slug' =>  $entry->slug,
            'username' =>  $entry->username,
            'password' =>  $entry->password,
            'url' =>  $entry->url,
            'comment' =>  $entry->comment,
            'createdAt' => $entry->created_at->toAtomString(),
            'updatedAt' => $entry->updated_at->toAtomString(),
        ];
    }

    /**
     * Get resource links.
     *
     * @param object $entry
     * @param bool   $isPrimary
     * @param array  $includeRelationships A list of relationships that will be included as full entrys.
     *
     * @return array
     */
    public function getRelationships($entry, $isPrimary, array $includeRelationships)
    {
        return [
            'authors' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['authors']),
                self::DATA => function () use ($entry) {
                    return $entry->user;
                }
            ]
        ];
    }


}
