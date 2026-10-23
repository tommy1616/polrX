<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class DataTablesHelper
{
    protected Builder $query;

    protected array $addedColumns = [];

    protected array $editedColumns = [];

    protected array $rawColumns = [];

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public static function of(Builder $query): self
    {
        return new self($query);
    }

    public function addColumn(string $name, callable $callback): self
    {
        $this->addedColumns[$name] = $callback;

        return $this;
    }

    public function editColumn(string $name, callable $callback): self
    {
        $this->editedColumns[$name] = $callback;

        return $this;
    }

    public function rawColumns(array $columns): self
    {
        $this->rawColumns = $columns;

        return $this;
    }

    public function escapeColumns(array $columns): self
    {
        return $this;
    }

    public function make(bool $mDataSupport = true): JsonResponse
    {
        $request = request();
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $searchValue = trim((string) data_get($request->input('search'), 'value', ''));
        $columns = (array) $request->input('columns', []);
        $orders = (array) $request->input('order', []);

        $allowedColumns = [
            'id', 'username', 'email', 'created_at', 'active', 'api_key',
            'api_active', 'api_quota', 'role', 'short_url', 'long_url',
            'clicks', 'creator', 'is_disabled',
        ];

        $recordsTotal = (clone $this->query)->count();

        if ($searchValue !== '') {
            $searchableCols = [];
            foreach ($columns as $col) {
                $colName = $col['name'] ?? $col['data'] ?? '';
                $searchable = ($col['searchable'] ?? 'true') !== 'false';
                if ($searchable && in_array($colName, $allowedColumns, true)) {
                    $searchableCols[] = $colName;
                }
            }
            if (empty($searchableCols)) {
                $searchableCols = ['short_url', 'long_url', 'creator', 'username', 'email'];
            }

            $this->query->where(function (Builder $q) use ($searchableCols, $searchValue) {
                foreach ($searchableCols as $colName) {
                    $q->orWhere($colName, 'LIKE', '%'.$searchValue.'%');
                }
            });
        }

        $recordsFiltered = (clone $this->query)->count();

        foreach ($orders as $order) {
            $colIdx = isset($order['column']) ? (int) $order['column'] : -1;
            $dir = strtolower((string) ($order['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
            if (isset($columns[$colIdx])) {
                $colDef = $columns[$colIdx];
                $orderable = ($colDef['orderable'] ?? 'true') !== 'false';
                $colName = $colDef['name'] ?? $colDef['data'] ?? '';
                if ($orderable && in_array($colName, $allowedColumns, true)) {
                    $this->query->orderBy($colName, $dir);
                }
            }
        }

        if ($length > 0) {
            $this->query->skip($start)->take($length);
        }

        $models = $this->query->get();
        $data = [];

        foreach ($models as $model) {
            $row = [];
            foreach ($model->getAttributes() as $key => $val) {
                $strVal = is_null($val) ? '' : (string) $val;
                $row[$key] = in_array($key, $this->rawColumns, true) ? $strVal : e($strVal);
            }

            foreach ($this->editedColumns as $col => $callback) {
                $val = $callback($model);
                $row[$col] = in_array($col, $this->rawColumns, true) ? $val : e((string) $val);
            }

            foreach ($this->addedColumns as $col => $callback) {
                $val = $callback($model);
                $row[$col] = in_array($col, $this->rawColumns, true) ? $val : e((string) $val);
            }

            $data[] = $row;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
