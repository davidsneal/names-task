<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// requests
use App\Http\Requests\GetNamesRequest;

// models
use App\Models\Name;

// resources
use App\Http\Resources\NameResource;

class NameController extends Controller
{
    /**
     * A paginated list of names
     *
     * @return void
     */
    public function index(GetNamesRequest $request)
    {
        // initiate query to get names, ordered in the default manner
        $names = Name::ordered();

        // if a search term was entered
        if ($request->get('term')) {
            // use the term to add where clauses to the query builder
            $names->where('first_name', 'like', '%'.$request->get('term').'%')
                ->orWhere('last_name', 'like', '%'.$request->get('term').'%');
        }

        // if duplicates are not wanted
        if (! $request->get('dupes')) {
            // limit select to first/last names to be able to apply groupBy
            $names->select('first_name', 'last_name');

            // add groupBy to query builder so that duplicates are removed
            $names->groupBy([
                'first_name',
                'last_name',
            ]);
        }

        // return with the names, paginated, using our resource as a collection
        return NameResource::collection($names->paginate(15));
    }
}
