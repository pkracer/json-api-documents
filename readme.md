! (https://travis-ci.org/pkracer/json-api-documents.svg)

# Json Api Documents
This package can be used to format api responses to adhere to the Json Api specification v1.0

## Install

Via Composer

``
$ composer require pkracer/json-api-documents
``

## Requirements

The following versions of PHP are required by this version.

* PHP 5.5
* PHP 5.6
* PHP 7.0-dev
* HHVM

## Documentation

> $document = new \Pkracer\JsonApiDocuments\Document(); <br>
> $document->describe(['version' => '1.0']) <br>
> ->meta(['count' => '10']) <br>
> ->links([ <br>
  'self' => 'http://example.com/articles', <br>
  'next' => 'http://example.com/articles?page[offset]=2', <br>
  'last' => 'http://example.com/articles?page[offset]=10' <br>
]);

## Laravel

> $document = new \Pkracer\JsonApiDocuments\Document(); <br>
> $document->describe(['version' => '1.0']) <br>
> $posts = \App\Post::all(); <br>
> $document->data($posts);


## License

The MIT License (MIT). Please see [License File](https://github.com/pkracer/json-api-documents/blob/master/LICENSE) for more information.