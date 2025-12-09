<?php

namespace App\Http\Controllers;

use App\Models\DefaultValue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DefaultValueController extends Controller
{
    public function index()
    {
        return Inertia::render('default-value/default-value');
    }

    public function update(Request $request)
    {
        $defaultValue = DefaultValue::first();
        $defaultValue->update($request->all());
        return redirect()->route('defaultValue')->with('success', 'Default value updated successfully');
    }
}
    