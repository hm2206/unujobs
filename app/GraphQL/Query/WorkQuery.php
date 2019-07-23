<?php

namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Query;
use App\Models\Work;

class WorkQuery extends Query
{
    protected $attributes = [
        'name' => 'WorkQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(Type::string());
    }

    public function args()
    {
        return [
            'limite' => [
                'name' => 'limite',
                'type' => Type::int()
            ]
        ];
    }

    public function resolve($root, $args, SelectFields $fields, ResolveInfo $info)
    {
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $limite = isset($args['limite']) ? $args['limite'] : 20;

        return Work::take($limite)->get();
    }
}