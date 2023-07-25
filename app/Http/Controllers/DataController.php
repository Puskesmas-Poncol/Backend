<?php

namespace App\Http\Controllers;

// use App\Models\UserPP;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Facades\Excel;

class UserPP implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
        ]);
    }
}


class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);
            $header = array_shift($data[0]);

            $jsonArray = [];

            foreach ($data[0] as $row) {
                $rowData = array_combine($header, $row);
                $jsonArray[] = $rowData;
            }

            $data = json_decode(json_encode($jsonArray), true);
            $rules = [
                '*.No' => 'required',
                "*.NIK" => "required",
                "*.nama_anak" => "required",
                "*.tgl_lahir" => "required",
                "*.umur_tahun" => "required|integer",
                "*.umur_bulan" => "required|integer",
                "*.jenis_kelamin" => "required",
                "*.nama_ortu" => "required",
                "*.nik_ortu" => "required",
                "*.hp_ortu" => "required",
                "*.PKM" => "required",
                "*.KEL" => "required",
                "*.POSY" => "required",
                "*.RT" => "required",
                "*.RW" => "required",
                "*.ALAMAT" => "required",
                "*.TANGGALUKUR" => "required",
                "*.TINGGI" => "required|numeric",
                "*.BERAT" => "required|numeric",
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return ResponseHelper::err("Format tidak sesuai");
            }

            return ResponseHelper::baseResponse("", 200, $data);
        } catch (Exception $err) {
            return ResponseHelper::err($err->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
