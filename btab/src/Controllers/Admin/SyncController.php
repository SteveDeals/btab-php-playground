<?php

namespace Btab\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Btab\Jobs\ImportProductsJob;

class SyncController extends Controller
{
    public function sync(Request $request)
    {
        dispatch(new ImportProductsJob());
        return back()->with('status', 'Product synchronization started. Check logs for progress.');
    }
}
