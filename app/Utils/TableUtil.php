<?php

namespace App\Utils;

class TableUtil
{
    public static function setTableHeader($key, $label, $sortable = true, $searchable = true)
    {
        return [
            'key' => $key,
            'label' => $label,
            'sortable' => $sortable,
            'searchable' => $searchable,
        ];
    }

    public static function apply($query, $search, $sortColumn, $sortDirection, $headers = [])
    {
        $query->when($search, function ($q) use ($search, $headers) {
            $q->where(function ($subQ) use ($search, $headers) {
                foreach ($headers as $header) {
                    if ($header['searchable'] ?? false) {
                        $key = $header['key'];
                        if (str_contains($key, '.')) {
                            $lastDot = strrpos($key, '.');
                            $relation = substr($key, 0, $lastDot);
                            $column = substr($key, $lastDot + 1);

                            $subQ->orWhereHas($relation, function ($relQ) use ($column, $search) {
                                $relQ->where($column, 'like', '%' . $search . '%');
                            });
                        } else {
                            $subQ->orWhere($key, 'like', '%' . $search . '%');
                        }
                    }
                }
            });
        });

        $query->when($sortColumn, function ($q) use ($sortColumn, $sortDirection) {
            $q->orderBy($sortColumn, $sortDirection);
        });

        return $query;
    }

    public static function paginate($component, $query, $headers = [], $perPage = 10)
    {
        self::apply(
            $query,
            $component->search ?? null,
            $component->sortBy ?? null,
            $component->sortDirection ?? 'asc',
            $headers
        );

        return $query->paginate($perPage);
    }
}
