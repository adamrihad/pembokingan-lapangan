<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index() {
        return response()->json(Field::all());
    }

    public function store(Request $request) {
        // Samakan dengan nama kolom di migration
        $request->validate([
            'nama_lapangan' => 'required', 
            'harga_per_jam' => 'required|numeric'
        ]);

        $field = Field::create($request->all());
        return response()->json(['message' => 'Lapangan berhasil ditambah', 'data' => $field], 201);
    }

    public function show($id) {
        return response()->json(Field::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $field = Field::findOrFail($id);
        $field->update($request->all());
        return response()->json(['message' => 'Lapangan berhasil diupdate', 'data' => $field]);
    }

    public function destroy($id) {
        Field::destroy($id);
        return response()->json(['message' => 'Lapangan berhasil dihapus']);
    }
}