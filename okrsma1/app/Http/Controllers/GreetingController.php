<?php

namespace App\Http\Controllers;

use App\Models\Greeting;
use Illuminate\Http\Request;

class GreetingController extends Controller
{
    public function index()
    {
        return view('admin.greetings.index', ['greetings' => Greeting::orderBy('start_time')->get()]);
    }
    public function store(Request $r)
    {
        $d = $this->validateData($r);
        $d['enabled'] = $r->boolean('enabled');
        Greeting::create($d);
        return back()->with('success', 'Salam berhasil ditambahkan.');
    }
    public function update(Request $r, Greeting $greeting)
    {
        $d = $this->validateData($r);
        $d['enabled'] = $r->boolean('enabled');
        $greeting->update($d);
        return back()->with('success', 'Salam berhasil diperbarui.');
    }
    public function destroy(Greeting $greeting)
    {
        $greeting->delete();
        return back()->with('success', 'Salam dihapus.');
    }
    private function validateData(Request $r): array
    {
        return $r->validate(['title' => 'required|string|max:100', 'message' => 'required|string|max:1000', 'start_time' => 'required|date_format:H:i', 'end_time' => 'required|date_format:H:i', 'enabled' => 'nullable|boolean']);
    }
}
