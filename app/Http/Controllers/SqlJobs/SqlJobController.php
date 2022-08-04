<?php

namespace App\Http\Controllers\SqlJobs;

use Inertia\Inertia;
use App\Models\SqlJob;
use App\Jobs\SqlJobTask;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Blade;

class SqlJobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      $sqlJobs = SqlJob::with('connection:id,name,type,database')->orderBy('created_at')->paginate(2);

      return Inertia::render('SqlJobs/Index', [
        'sqlJobs' => $sqlJobs
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $connections = Connection::select(['id', 'name', 'type', 'database'])->get();
      return Inertia::render('SqlJobs/Create', [
        'connections' => $connections
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $validated = $request->validate([
        'title'           => 'required',
        'description'     => 'nullable',
        'connection_id'   => 'nullable',
        'execution_date'  => 'required',
        'script'          => 'required',
        'status'          => 'required'
      ]);

      // create new sql job
      $sqlJob = SqlJob::create($validated);

      // Dispatch new SqlJobTaks
      SqlJobTask::dispatch($sqlJob)->delay( Carbon::parse($sqlJob->execution_date) );

      return redirect()->route('sqlJobs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SqlJob  $sqlJob
     * @return \Illuminate\Http\Response
     */
    public function show(SqlJob $sqlJob)
    {
      $sqlJob->load('connection:id,name,type,database');
      return Inertia::render('SqlJobs/Show', [
        'sqlJob' => $sqlJob
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SqlJob  $sqlJob
     * @return \Illuminate\Http\Response
     */
    public function edit(SqlJob $sqlJob)
    {
      $connections = Connection::select(['id', 'name', 'type', 'database'])->get();
      return Inertia::render('SqlJobs/Edit', [
        'sqlJob'      => $sqlJob,
        'connections' => $connections,
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SqlJob  $sqlJob
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SqlJob $sqlJob)
    {

      $sqlJob->fill($request->validate([
        'title'           => 'required',
        'description'     => 'nullable',
        'connection_id'   => 'required',
        'execution_date'  => 'required',
        'script'          => 'required',
        'status'          => 'required'
      ]));

      // Create a new Job if execution_date chenges
      if( $sqlJob->isDirty('execution_date') ) {

        $sqlJob->save();

        session()->flash('flash.banner', 'Sql Job was succesfully updated, execution_date changed!');

        // Dispatch new SqlJobTaks
        SqlJobTask::dispatch($sqlJob)->delay( Carbon::parse($sqlJob->execution_date) );
      }
      else {

        $sqlJob->save();

        session()->flash('flash.banner', 'Sql Job was succesfully updated!');
      }


      return redirect()->route('sqlJobs.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SqlJob  $sqlJob
     * @return \Illuminate\Http\Response
     */
    public function destroy(SqlJob $sqlJob)
    {
      $sqlJob->update([
        'status'      => SqlJob::STATUS_CANCELLED,
      ]);
      $sqlJob->delete();
      return redirect()->route('sqlJobs.index');
    }

    public function executeNow(SqlJob $sqlJob)
    {

      $result = SqlJobService::runScript($sqlJob);

      if( $result ) {
        session()->flash('flash.banner', 'Sql script was succesfully executed.');
      }
      else {
        session()->flash('flash.bannerStyle', 'danger');
        session()->flash('flash.banner', 'Sql was executed with errors. See Sql Job detail for view errors.');
      }

      return redirect()->back();
    }
}
