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
            'createdAt' => $entry->created_at,
            'updatedAt' => $entry->updated_at,
        ];
    }
}
