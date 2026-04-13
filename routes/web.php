<?php

use Illuminate\Support\Facades\Route;
use Typesense\Client;

Route::get('/create-collection', function (Client $client) {


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

Route::get('/import-collection', function (Client $client) {
    
    

    // $client->collections->create($bookSchema);
    $books = file_get_contents(base_path('books.jsonl'));
    // dd($books);
    $client->collections['books']->documents->import($books);
    return 'books imported';
});


Route::get('/filter-search', function (Client $client) {

    
    $results = $client->collections['books']->documents->search([
        'q' => request('q'),
        'query_by' => 'title,authors', //look into title and author
        'sort_by' => 'ratings_count:desc',
        'per_page' => 3,
        'filter_by' => 'publication_year:=[2000...2010, 2010...2020]' //range 2000 to 2010 and 2010 to 2020
    ]);
    // $titles = collect($results['hits'])->map(fn($hit) => $hit['document']['title']);

    return $results;
});


