<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Person;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private function workspace()
    {
        return app('current_workspace');
    }

    public function index()
    {
        $clients = Client::with(['people', 'projects'])
            ->where('workspace_id', $this->workspace()->id)
            ->orderBy('name')->get();
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $data['workspace_id'] = $this->workspace()->id;
        $client = Client::create($data);

        if ($request->expectsJson()) {
            return response()->json(['id' => $client->id, 'name' => $client->name]);
        }

        return redirect()->route('clients.index')->with('success', 'Client created.');
    }

    public function show(Client $client)
    {
        abort_if($client->workspace_id !== $this->workspace()->id, 403);
        $client->load(['people', 'projects']);
        return view('clients.show', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        abort_if($client->workspace_id !== $this->workspace()->id, 403);
        $data = $request->validate(['name' => 'required|string|max:255']);
        $client->update($data);
        return redirect()->route('clients.show', $client)->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        abort_if($client->workspace_id !== $this->workspace()->id, 403);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    public function destroyPerson(\App\Models\Person $person)
    {
        $clientId = $person->client_id;
        $person->delete();
        return $clientId
            ? redirect()->route('clients.show', $clientId)->with('success', 'Contact removed.')
            : back()->with('success', 'Contact removed.');
    }

    public function storePerson(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'role'      => 'nullable|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:50',
            'client_id' => 'nullable|exists:clients,id',
        ]);
        Person::create($data);

        $redirect = $data['client_id']
            ? redirect()->route('clients.show', $data['client_id'])
            : back();

        return $redirect->with('success', 'Contact added.');
    }
}
