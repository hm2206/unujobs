<?php

namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Type as GraphQLType;
use Graphql\Type\Definition\Type;
use Graphql;
use App\Models\Work;

class WorkType extends GraphQLType
{
    protected $attributes = [
        'name' => 'WorkType',
        'description' => 'A type',
        'model' => Work::class
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
            'nombre_completo' => Type::string()
        ];
    }
}