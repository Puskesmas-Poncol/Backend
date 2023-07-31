<?php

namespace App\Http\Controllers;

// use App\Models\UserPP;

use App\Helpers\ResponseHelper;
use App\Models\Data;
use App\Models\Posyandu;
use App\Models\Upload;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $name = $request->query('name');
        return response()->json([
            'id' => $name
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $file = $request->file('excel_file');
            $filename = time() . $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension != "xls" && $extension != "xlsx") {
                return ResponseHelper::err("File yang dimasukkan tidak sesuai!");
            }

            $file->storeAs('uploads/files', $filename);
            $pathFile = 'app/uploads/files/' . $filename;
            $path = storage_path($pathFile);

            if ($extension === 'xls') {
                $reader = IOFactory::createReader('Xls');
            } else {
                $reader = IOFactory::createReader('Xlsx');
            }
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
            $data = $sheet->toArray();

            $dataJson = array();

            for ($i = 1; $i < count($data); $i++) {
                $jsonObject = array_combine($data[0], $data[$i]);
                $dataJson[] = $jsonObject;
            }

            $rules = [
                '*.No' => 'required',
                "*.NIK" => "required",
                "*.nama_anak" => "required",
                "*.tgl_lahir" => "required",
                "*.umur_tahun" => "required",
                "*.umur_bulan" => "required",
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

            $validator = Validator::make($dataJson, $rules);
            if ($validator->fails()) {
                Storage::delete('uploads/files/' . $filename);
                return ResponseHelper::err("Format file tidak sesuai!");
            }


            foreach ($dataJson as &$item) {
                if (is_numeric($item['TINGGI'])) {
                    $item['TINGGI'] = (int) $item['TINGGI'];
                }

                if (is_numeric($item['BERAT'])) {
                    $item['BERAT'] = (int) $item['BERAT'];
                }

                if (is_numeric($item['umur_tahun'])) {
                    $item['umur_tahun'] = (int) $item['umur_tahun'];
                }

                if (is_numeric($item['umur_bulan'])) {
                    $item['umur_bulan'] = (int) $item['umur_bulan'];
                }
            }

            $validated = $request->validate(
                [
                    'id_posyandu' => 'required|numeric|min:1|max:9',
                    'tanggal' => 'required|date',
                ]
            );

            DB::beginTransaction();
            $upload = Upload::create(
                [
                    'id_posyandu' => $validated['id_posyandu'],
                    'tanggal' => $validated['tanggal']
                ]
            );

            foreach ($dataJson as $value) {
                Data::create([
                    ...$value,
                    'id_posyandu' => $validated['id_posyandu'],
                    'id_upload' => $upload->id,
                ]);
            }

            DB::commit();

            return ResponseHelper::baseResponse('', 200, $dataJson);
        } catch (Exception $err) {
            DB::rollBack();
            return ResponseHelper::err($err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            $date = $request->query('date');
            if ($date == null) {
                $date = date('Y') . date('m');
            }
            $month = date('m',  strtotime($date));
            $year = date('Y',  strtotime($date));

            $data =  Upload::select('id', 'tanggal', 'id_posyandu', 'created_at', 'updated_at')
                ->whereIn(
                    DB::raw('(id_posyandu, created_at)'),
                    Upload::select(DB::raw('id_posyandu, MAX(created_at)'))
                        ->whereMonth('tanggal', $month)
                        ->whereYear('tanggal', $year)
                        ->groupBy('id_posyandu')
                )
                ->get();

            for ($i = 0; $i < count($data); $i++) {
                $data[$i] = Data::where('id_upload', $data[$i]['id'])->get();
            }

            $data_convert = $this->calculator($data);

            return ResponseHelper::baseResponse('Data retrieve successfully', 200, $data_convert);
        } catch (Exception $err) {
            return ResponseHelper::err($err->getMessage());
        }
    }

    function calculator($data): array
    {
        include __DIR__ . '/../../Helpers/DeviasiStunting.php';
        $data_convert = [];
        for ($i = 0; $i < count($data); $i++) {
            $posyandu = Posyandu::find($data[$i][0]['id_posyandu']);
            $value = [
                'name' => $posyandu->name,
            ];

            $true = 0;
            $false = 0;
            foreach ($data[$i] as $index) {
                if ($this->kalkulator($index)) {
                    $true++;
                } else {
                    $false++;
                }
            }
            $value['baik'] = $true;
            $value['buruk'] = $false;

            $data_convert[$i] = $value;
        }

        return $data_convert;
    }

    function kalkulator($data): bool
    {
        include __DIR__ . '/../../Helpers/DeviasiStunting.php';
        if (str_contains(strtolower($data['jenis_kelamin']), 'l')) {
            $umur = $data['umur_bulan'];
            $berat = $data['BERAT'];

            if ($berat > $stuntingLaki[$umur][3] && $berat < $stuntingLaki[$umur][5]) {
                return false;
            } else {
                return true;
            }
        } else {
            $umur = $data['umur_bulan'];
            $berat = $data['BERAT'];

            if ($berat > $stuntingPerempuan[$umur][3] && $berat < $stuntingPerempuan[$umur][5]) {
                return false;
            } else {
                return true;
            }
        }
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
