<?php

namespace Core\Providers;

use Illuminate\Support\ServiceProvider;
//use Core\Traits\DataTemplater\DataTemplateableTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExtendValidatorServiceProvider extends ServiceProvider
{
//    use DataTemplateableTemplate;

    public function boot(): void
    {
//	    Validator::extend('alias', function ($attribute, $value, $parameters, $validator) {
//	    	$table = $validator->customAttributes['table'] ?? null;
//	    	$modelId = $validator->customAttributes['id'] ?? null;
//	    	$modelParentId = $validator->customAttributes['parent_id'] ?? null;
//
//	    	if(!$table || !($modelId && $modelParentId)) {
//				return true;
//			}
//
//			$query = DB::table($table)
//				->where($attribute, $value)
//				->whereNull('deleted_at');
//	    	if($modelId !== null) {
//				$query = $query->where('id', '<>', $modelId);
//            }
//	    	if($modelParentId !== null) {
//	    		$query = $query->where('parent_id', $modelParentId);
//		    }
//			$count = $query->count();
//		    return $count === 0;
//	    });
    }
}
