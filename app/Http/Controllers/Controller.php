<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Controller as BaseController;
use Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $limit;

    protected $sortBy;

    protected $sort;

    protected $page;

    public function __construct()
    {
        $this->limit = request()->get('limit') ? intval(request()->get('limit')) : 10;

        $this->page = request()->get('page') ? request()->get('page') : 1;

        $this->sortBy = request()->get('sortBy') ? request()->get('sortBy') : 'id';

        $this->sort = request()->get('sort') ? request()->get('sort') : 'DESC';
    }

    public function sendResponse($result, $message)
    {
        return Response::json($this->makeResponse($message, $result));
    }

    public function sendError($result, $error, $code = HttpResponse::HTTP_BAD_REQUEST)
    {
        return Response::json($this->makeError($error, $result), $code);
    }

    private function makeResponse(string $message, mixed $data): array
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    private function makeError(string $message, mixed $data): array
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }

}
