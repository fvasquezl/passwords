<?php

namespace App\JsonApi\Entries;

use App\Rules\Slug;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;
use Illuminate\Validation\Rule;

class Validators extends AbstractValidators
{

    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *      the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = ['authors', 'categories'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *      the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = [];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *      the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = [];

    /**
     * Get resource validation rules.
     *
     * @param mixed|null $record
     *      the record being updated, or null if creating a resource.
     * @param array $data
     *      the data being validated
     * @return array
     */
    protected function rules($record, array $data): array
    {

        return [
            'authors' => [
                Rule::requiredIf(!$record),
                new HasOne('authors')

            ],
            'categories' => [
                Rule::requiredIf(!$record),
                new HasOne('categories')
            ],
            'name' => ['required'],

            'slug' => [
                'required',
                'alpha_dash',
                Rule::unique('entries')->ignore($record),
                new Slug()
            ],
            'username' => ['required'],
            'password' => ['required'],
            'url' => [
                'required',
                'url'
            ],
            'comment' => [
                'required'
            ],

        ];
    }

    /**
     * Get query parameter validation rules.
     *
     * @return array
     */
    protected function queryRules(): array
    {
        return [
            //
        ];
    }

}
