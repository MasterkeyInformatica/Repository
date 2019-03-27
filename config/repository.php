<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the repository classes.
    |
    */
    'repository_namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Repository path
    |--------------------------------------------------------------------------
    |
    | The path to the repository folder.
    |
    */
    'repository_path' => 'app' . DIRECTORY_SEPARATOR . 'Repositories',

    /*
    |--------------------------------------------------------------------------
    | Criteria namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for the criteria classes.
    |
    */
    'criteria_namespace' => 'App\Repositories\Criteria',

    /*
    |--------------------------------------------------------------------------
    | Criteria path
    |--------------------------------------------------------------------------
    |
    | The path to the criteria folder.
    |
    */
    'criteria_path'=> 'app' . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'Criteria',

    /*
    |--------------------------------------------------------------------------
    | Model namespace
    |--------------------------------------------------------------------------
    |
    | The model namespace.
    |
    */
    'model_namespace' => 'App',

    /*
    |--------------------------------------------------------------------------
    | Criteria Config
    |--------------------------------------------------------------------------
    |
    | Settings of request parameters names that will be used by Criteria
    |
    */
    'criteria'   => [
        /*
        |--------------------------------------------------------------------------
        | Accepted Conditions
        |--------------------------------------------------------------------------
        |
        | Conditions accepted in consultations where the Criteria
        |
        | Ex:
        |
        | 'acceptedConditions'=>['=','like']
        |
        | $query->where('foo','=','bar')
        | $query->where('foo','like','bar')
        |
        */
        'acceptedConditions' => [
            '=',
            'like'
        ],
        /*
        |--------------------------------------------------------------------------
        | Request Params
        |--------------------------------------------------------------------------
        |
        | Request parameters that will be used to filter the query in the repository
        |
        | Params :
        |
        | - search : Searched value
        |   Ex: http://prettus.local/?search=lorem
        |
        | - searchFields : Fields in which research should be carried out
        |   Ex:
        |    http://prettus.local/?search=lorem&searchFields=name;email
        |    http://prettus.local/?search=lorem&searchFields=name:like;email
        |    http://prettus.local/?search=lorem&searchFields=name:like
        |
        | - filter : Fields that must be returned to the response object
        |   Ex:
        |   http://prettus.local/?search=lorem&filter=id,name
        |
        | - orderBy : Order By
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id
        |
        | - sortedBy : Sort
        |   Ex:
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=asc
        |   http://prettus.local/?search=lorem&orderBy=id&sortedBy=desc
        |
        | - searchJoin: Specifies the search method (AND / OR), by default the
        |               application searches each parameter with OR
        |   EX:
        |   http://prettus.local/?search=lorem&searchJoin=and
        |   http://prettus.local/?search=lorem&searchJoin=or
        |
        */
        'params'             => [
            'search'        => 'search',
            'searchFields'  => 'searchFields',
            'filter'        => 'filter',
            'orderBy'       => 'orderBy',
            'sortedBy'      => 'sortedBy',
            'with'          => 'with',
            'searchJoin'    => 'searchJoin',
            'limit'         => 'limit'
        ]
    ],
    /*
     |--------------------------------------------------------------------------
     | Cache Config
     |--------------------------------------------------------------------------
     |
     | Settings for cache used by repository
     |
     */
    'cache' => [
        /*
         |--------------------------------------------------------------------------
         | Cache Status
         |--------------------------------------------------------------------------
         |
         | Enable or disable cache
         |
         */
        'enabled'    => true,

        /*
         |--------------------------------------------------------------------------
         | Cache Minutes
         |--------------------------------------------------------------------------
         |
         | Time of expiration cache
         |
         */
        'minutes'    => 30,

        /*
          |--------------------------------------------------------------------------
          | Cache Clean Listener
          |--------------------------------------------------------------------------
          |
          |
          |
          */
        'clean'      => [
            /*
             |--------------------------------------------------------------------------
             | Enable clear cache on repository changes
             |--------------------------------------------------------------------------
             |
             */
            'enabled' => true,

            /*
             |--------------------------------------------------------------------------
             | Actions in Repository
             |--------------------------------------------------------------------------
             |
             | create : Clear Cache on create Entry in repository
             | update : Clear Cache on update Entry in repository
             | delete : Clear Cache on delete Entry in repository
             |
             */
            'on'      => [
                'create' => true,
                'update' => true,
                'delete' => true,
            ]
        ],

        'params' => [
            /*
             |--------------------------------------------------------------------------
             | Skip Cache Params
             |--------------------------------------------------------------------------
             |
             |
             | Ex: http://prettus.local/?search=lorem&skipCache=true
             |
             */
            'skipCache' => 'skipCache'
        ],

        /*
         |--------------------------------------------------------------------------
         | Methods Allowed
         |--------------------------------------------------------------------------
         |
         | methods cacheable : all, paginate [simplePaginate too], find, getByCriteria, findBy, findAllBy
         |
         | Ex:
         |
         | 'only'  =>['all','paginate'],
         |
         | or
         |
         | 'except'  =>['find'],
         */
        'allowed'    => [
            'only'   => null,
            'except' => null
        ],
    ]
];
