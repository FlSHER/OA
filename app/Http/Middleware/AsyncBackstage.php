<?php

namespace App\Http\Middleware;

use Closure;

class AsyncBackstage
{

    private $except = [
        'entrance',
        'reset_password',
        'finance/reimburse/print/',
        'finance/check_reimburse/print/',
        'finance/reimburse/excel',
        'finance/check_reimburse/excel',
        'personal/refresh_authority',
        'workflow/formDesignList',
        'workflow/formConfigExcelBlade',
        'workflow/flowAttribute',
        'workflow/getInternalDataField',
        'workflow/flowDesignPreview',
        'workflow/deviseFlowSteps',
        'workflow/AddFlowStepsList',
        'workflow/updateFlowStepsList',
        'workflow/passReadPerson',
        'workflow/passReadDept',
        'workflow/passReadRole',
        'workflow/dbConnectionInfo',
        'workflow/optionalFieldList',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $async = true;
        $uri = $request->path();
        foreach ($this->except as $value) {
            if (strstr($uri, $value)) {
                $async = false;
            }
        }
        if ($request->isMethod('get') && !($request->has('iframe') || $request->isXmlHttpRequest()) && $async) {
            return response()->view('layouts.full_page');
        }
        return $next($request);
    }

}
