<?php

namespace App\Http\Controllers\Connection;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Connection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return Inertia::render('Connections/Index', [
        'connections' => Connection::select(['id', 'name', 'type', 'database', 'created_at', 'updated_at'])->get()
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return Inertia::render('Connections/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $response = Connection::create($request->validate([
        'name' => 'required',
        'type' => 'required',
        'host' => 'nullable',
        'port' => 'nullable',
        'database' => 'required',
        'user' => 'nullable',
        'password' => 'nullable'
      ]));

      return redirect()->route('connections.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function show(Connection $connection)
    {
      try {

        // Validate if can't connect
        if( ! ConnectionService::testConnection($connection) ) {
          request()->session()->flash('flash.bannerStyle', 'danger');
          return redirect()->back()->with('flash.banner', "Can't connect to: {$connection->name}!");
        }

        return redirect()->back()->with('flash.banner', "Connection to: {$connection->name}, {$connection->database} is ok!");
      }
      catch(\Throwable $e) {
        request()->session()->flash('flash.bannerStyle', 'danger');
        return redirect()->back()->with('flash.banner', "Can't connect to: {$connection->name}!");
      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function edit(Connection $connection)
    {
      $connection->password = '';
      return Inertia::render('Connections/Edit', [
        'connection' => $connection
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Connection $connection)
    {
      $originalPassword = $connection->password;
      $connection->fill($request->validate([
        'name'      => 'required',
        'type'      => 'required',
        'host'      => 'nullable',
        'port'      => 'nullable',
        'database'  => 'required',
        'user'      => 'nullable',
        'password'  => 'nullable',
      ]));

      if( !$request->password ) {
        $connection->password = $originalPassword;
      }

      $connection->save();

      $request->session()->flash('flash.banner', 'Connection was succesfully updated!');

      return redirect()->route('connections.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Connection  $connection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Connection $connection)
    {
      $connection->delete();
      return redirect()->route('connections.index');
    }
}
