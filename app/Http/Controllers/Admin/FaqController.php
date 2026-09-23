<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faq.index', ['faq' => Faq::orderBy('urutan')->orderBy('id')->get()]);
    }

    public function create()
    {
        return view('admin.faq.form', ['faq' => new Faq(['is_aktif' => true, 'urutan' => Faq::max('urutan') + 1])]);
    }

    public function store(FaqRequest $request)
    {
        Faq::create($this->data($request));

        return redirect()->route('admin.faq.index')->with('sukses', 'Pertanyaan ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faq.form', compact('faq'));
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($this->data($request));

        return redirect()->route('admin.faq.index')->with('sukses', 'Pertanyaan diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faq.index')->with('sukses', 'Pertanyaan dihapus.');
    }

    private function data(FaqRequest $request): array
    {
        $data = $request->validated();
        $data['urutan'] ??= 0;

        return $data;
    }
}
