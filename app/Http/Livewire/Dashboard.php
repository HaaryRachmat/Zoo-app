<?php

namespace App\Http\Livewire;

use App\Models\Animal;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $nama,
        $jenis,
        $tempat_asal,
        $makanan,
        $file_name,
        $search,
        $modal = false,
        $animal_id,
        $old_file_name,
        $deleteConfirmation = false,
        $deleteId;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $updateQueryString = ['search'];

    public function openModal()
    {
        $this->modal = true;
    }
    public function closeModal()
    {
        $this->modal = false;
        $this->resetField();
    }
    public function resetField()
    {
        $this->nama = "";
        $this->jenis = "";
        $this->tempat_asal = "";
        $this->makanan = "";
        $this->file_name = null;
        $this->deleteId = null;
    }

    public function store()
    {
        // dd($this->file_name);
        $imageValidation = '';
        if ($this->file_name != $this->old_file_name) {
            $imageValidation = "required|image|mimes:jpg,jpeg,png|max:1024";
        }
        $this->validate([
            'nama' => 'required',
            'jenis' => 'required',
            'tempat_asal' => 'required',
            'makanan' => 'required',
            'file_name' => $imageValidation

        ]);

        if ($this->file_name != $this->old_file_name) {
            $fileName = $this->file_name->store('animal');
        } else {
            $fileName = $this->file_name;
        }


        $result = Animal::updateOrCreate(['id' => $this->animal_id], [
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'tempat_asal' => $this->tempat_asal,
            'makanan' => $this->makanan,
            'file_name' => $fileName
        ]);
        if ($result != "0") {
            session()->flash('message', 'Berhasil Mengupdate data');
        } else {
            session()->flash('errMessage', 'Gagal Mengupdate data');
        }
        $this->closeModal();
        $this->resetField();
    }

    public function edit($id)
    {
        $animal = Animal::find($id);
        $this->nama = $animal->nama;
        $this->jenis = $animal->jenis;
        $this->tempat_asal = $animal->tempat_asal;
        $this->makanan = $animal->makanan;
        $this->file_name = $animal->file_name;
        $this->old_file_name = $animal->file_name;
        $this->animal_id = $id;
        $this->openModal();
    }


    public function render()
    {
        if ($this->search) {
            $animals = Animal::where('tempat_asal', 'like', '%' . $this->search . '%')->paginate(5);
        } else {
            $animals = Animal::latest()->paginate(5);
        }
        // $this->animals = Animal::latest()->simplePaginate(5);
        return view(
            'livewire.dashboard',
            [
                'animals' => $animals
            ]
        );
    }

    public function openDeleteModal($id)
    {
        $this->deleteId = $id;
        $this->deleteConfirmation = true;
    }

    public function closeDeleteModal()
    {
        $this->deleteConfirmation = false;
        $this->resetField();
    }

    public function delete($id)
    {
        $animal = Animal::find($id);
        $result = $animal->delete();
        if ($result != "0") {
            session()->flash('message', 'Berhasil Mengahapus data');
        } else {
            session()->flash('errMessage', 'Gagal Mengahapus data');
        }
        $this->closeDeleteModal();
    }
}
