<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DevException;
use DB;

use PDO;
use Exception;
use DateTime;

// Ctl of kit for all developers
class KitController extends Controller
{

    /**
     * @SWG\Post(path="/dev/kit/sql",
     *     summary="PDO::execute()",
     *     description="PDO::execute()",
     *     tags={"Dev"},
     *     operationId="api.dev.kit.sql",
     * @SWG\Parameter(name="q",description="sql",type="string",in="body",schema="json",required=true),
     * @SWG\Response(response="200",description="",examples="")
     * )
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function sql(Request $request)
    {
        $q = $request->json('q');

        $execTime = (new DateTime())->format('c');
        $pdo = DB::getPdo();

        $startTime = microtime(true);

        $st = $pdo->prepare($q);
        $success = $st->execute();

        $res = $st->fetchAll(PDO::FETCH_ASSOC);

        $endTime = microtime(true);
        $elapsed = round(($endTime - $startTime) * 1e3, 3);

        return response()->json(compact('q', 'success', 'elapsed', 'execTime', 'res'));
    }
}
