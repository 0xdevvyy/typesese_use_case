<?php

use Illuminate\Support\Facades\Route;
use Typesense\Client;

Route::get('/create-collection', function () {
    $client = new Client([
        'api_key' => config('services.typesense.api_key'),
        'nodes' => [
            [
                'host' => config('services.typesense.host'),
                'port' => config('services.typesense.port'),
                'protocol' => config('services.typesense.protocol'),
            ]
        ],
        'connection_timeout_seconds' => 2
    ]);

    // dd($client);
    $bookSchema = [
        'name' => 'books',
        'fields' => [
            //title, author, year, ratings,
            ['name' => 'title', 'type' => 'string'],
            ['name' => 'authors', 'type' => 'string[]'], //array of strings
            ['name' => 'publication_year', 'type' => 'int32'],
            ['name' => 'ratings_count', 'type' => 'int32'],
            ['name' => 'average_rating', 'type' => 'float'],

        ],
        'default_sorting_field' => 'ratings_count',
    ];

    $client->collections->create($bookSchema);
    
});

Route::get('/import-collection', function () {
    $client = new Client([
        'api_key' => config('services.typesense.api_key'),
        'nodes' => [
            [
                'host' => config('services.typesense.host'),
                'port' => config('services.typesense.port'),
                'protocol' => config('services.typesense.protocol'),
            ]
        ],
        'connection_timeout_seconds' => 2
    ]);
    

    // $client->collections->create($bookSchema);
    $books = file_get_contents(base_path('books.jsonl'));
    // dd($books);
    $client->collections['books']->documents->import($books);
    return 'books imported';
});


Route::get('/search-collection', function () {
    $client = new Client([
        'api_key' => config('services.typesense.api_key'),
        'nodes' => [
            [
                'host' => config('services.typesense.host'),
                'port' => config('services.typesense.port'),
                'protocol' => config('services.typesense.protocol'),
            ]
        ],
        'connection_timeout_seconds' => 2
    ]);
    
    $results = $client->collections['books']->documents->search([
        'q' => request('q'),
        'query_by' => 'title,authors', //look into title and author
        'sort_by' => 'ratings_count:desc',
        'per_page' => 3
    ]);
    $titles = collect($results['hits'])->map(fn($hit) => $hit['document']['title']);

    return $titles;
});


