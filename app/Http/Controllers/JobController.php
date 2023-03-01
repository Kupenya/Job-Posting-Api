<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\JobResource;


class JobController extends BaseController
{
    /**
     * Display a listing of the job
     */
    public function index()
    {
        $jobs = Job::orderBy('id', 'DESC')->get();

        $collection =JobResource::collection($jobs);

        return $this->sendResponse($collection, 'Job gotten Successfully...');
    }


    /**
     * Store a newly created jobs in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'category' => 'required',
            'location' => 'required',
            'description' => 'required',
            'salary' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError('validation Error', $validator->errors());
        }

        $job = Job::create($input);

        return $this->sendResponse(new JobResource($job), "Job Created Succcessfully");
    }

    /**
     * Display the specified job by id.
     */
    public function show($id)
    {
        $job =Job::find($id);
        if (is_null($id)) {
            return $this->sendError('Job not found.');
        }

        return $this->sendResponse(new JobResource($job), "Job Found Successfully...");
    }

    // * Search the specified job by name.

    public function search(Request $request)
    {
        $query = $request->get('query');
        $jobs = Job::where('name', 'like', '%'. $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%')
                    ->get();
        return view('search-results', ['jobs' => $jobs]);
    }

    /**
     * Show the form for filtering the specified job.
     */
    
    public function filter(Request $request)
    {
        $query = Job::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->has('salary_range')) {
            $salary_range = explode('-', $request->input('salary_range'));
            $query->whereBetween('salary', [$salary_range[0], $salary_range[1]]);
        }

        $jobs = $query->get();

        return view('jobs.index', compact('jobs'));
    }


    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'category' => 'required',
            'location' => 'required',
            'description' => 'required',
            'salary' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError('validation Error', $validator->errors());
        }

        $job = Job::create($input);

        return $this->sendResponse(new JobResource($job), "Job Updated Succcessfully");

        $job->name = $input['name'];
        $job->category = $input['category'];
        $job->description= $input['description'];
        $job->salary= $input['salary'];
        $job->save();

        return $this->sendResponse(new JobResource($job), "Job Updated Succcessfully...");


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return $this->sendResponse([], 'Job Deleted Successfully');
    }
}
