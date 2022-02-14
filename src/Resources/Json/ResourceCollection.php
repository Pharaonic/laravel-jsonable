<?php

namespace Pharaonic\Laravel\Jsonable\Resources\Json;

use Illuminate\Http\Resources\Json\ResourceCollection as IlluminateResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;

class ResourceCollection extends IlluminateResourceCollection
{
    /**
     * The name of the resource being collected.
     *
     * @var string
     */
    public $collects;

    /**
     * Response message
     *
     * @var string
     */
    public static $message = null;

    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @param  string  $collects
     * @param  string|null  $message
     * @return void
     */
    public function __construct($resource, $collects, ?string $message = null)
    {
        $this->collects = $collects;
        self::$message = $message;

        parent::__construct($resource);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        if ($this->resource instanceof AbstractPaginator || $this->resource instanceof AbstractCursorPaginator)
            return $this->preparePaginatedResponse($request);
        

        return json()->success($this->toArray($request), static::$message ?? null);
    }

    /**
     * Create a paginate-aware HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function preparePaginatedResponse($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return json()->success($this->resource->items(), $this->message ?? null, $this->paginationInformation($request));
    }

    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->toArray();

        $default = [
            'links' => $this->paginationLinks($paginated),
            'meta' => $this->meta($paginated),
        ];

        if (method_exists($this->resource, 'paginationInformation')) {
            return $this->resource->paginationInformation($request, $paginated, $default);
        }

        return $default;
    }

    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
}
