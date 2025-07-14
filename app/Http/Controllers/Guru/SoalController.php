<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;
use App\Models\Guru;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SoalController extends Controller
{
    public function index()
    {
        $soals = Soal::with('ujian')->latest()->paginate(10);
        return view('guru.soal.index', compact('soals'));
    }

    public function create()
    {
         $guru = Guru::where('user_id', auth()->id())->firstOrFail();
        $ujians = Ujian::where('created_by', $guru->id)->get();
        return view('guru.soal.create', compact('ujians'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ujian_id' => 'required|exists:ujians,id',
            'pertanyaan' => 'required',
            'jawaban_benar' => 'nullable',
        ], [
            'ujian_id.required' => 'The Ujian field is required.',
            'ujian_id.exists' => 'The selected Ujian is invalid.',
            'pertanyaan.required' => 'The Pertanyaan field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Soal::create($validator->validated());

        return redirect()->route('guru.ujian.show', $request->ujian_id)->with('success', 'Soal created successfully.');
    }

    public function show(Soal $soal)
    {
        //
    }

    public function edit(Soal $soal)
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();
        $ujians = Ujian::where('created_by', $guru->id)->get();
        return view('guru.soal.edit', compact('soal', 'ujians'));
    }

    public function update(Request $request, Soal $soal)
    {
        $validator = Validator::make($request->all(), [
            'ujian_id' => 'required|exists:ujians,id',
            'pertanyaan' => 'required',
            'jawaban_benar' => 'nullable',
        ], [
            'ujian_id.required' => 'The Ujian field is required.',
            'ujian_id.exists' => 'The selected Ujian is invalid.',
            'pertanyaan.required' => 'The Pertanyaan field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $soal->update($validator->validated());

        return redirect()->route('guru.ujian.show', $request->ujian_id)->with('success', 'Soal updated successfully.');
    }

    public function destroy(Soal $soal)
    {
        $soal->delete();

        return back()->with('success', 'Soal deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        try {
            Excel::import(new SoalImport, $request->file('file'));
            Session::flash('success', 'Soal imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row: " . $failure->row() . ", Attribute: " . $failure->attribute() . ", Errors: " . implode(", ", $failure->errors());
            }
            Session::flash('error', implode("<br>", $errorMessages));

        } catch (\Exception $e) {
            Session::flash('error', 'Import failed: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
