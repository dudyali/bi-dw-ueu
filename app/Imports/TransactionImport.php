<?php

namespace App\Imports;

Use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TransactionImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    private $id_uploaded_file;
    private $id_channel;

    public function __construct($id_uploaded_file, $id_channel)
    {
        $this->id_uploaded_file = $id_uploaded_file;
        $this->id_channel = $id_channel;
    }

    public function startRow(): int {
        return 3;
    }
     
    public function batchSize(): int {
        return 500;
    }

    public function chunkSize(): int {
        return 500;
    }

    public function model(array $row)
    {
        if (!is_null($row[0])) {
            $district_code = substr($row[7], 4, 3);
            $village_code = substr($row[7], 7, 3);
    
            $district = Kecamatan::where('district_code', $district_code)->first();
            $district_id = null;
            if ($district) {
                $district_id = $district->id;
            }
    
            $village_id = null;
            if (!is_null($district_id)) {
                $village = Kelurahan::where('district_id', $district_id)->where('village_code', $village_code)->first();
                if ($village) {
                    $village_id = $village->id;
                }
            }
    
            return new Transaction([
                'district_id' => $district_id,
                'village_id' => $village_id,
                'id_uploaded_file' => $this->id_uploaded_file,
                'id_channel' => $this->id_channel,
                'nomor' => $row[0],
                'tg_tx' => $row[1],
                'jm_tx' => $row[2],
                'no_seq' => $row[3],
                'no_trx_bank' => $row[4],
                'no_trx_pemda' => $row[5],
                'kd_pemda' => $row[6],
                'nop' => $row[7],
                'tahun' => $row[8],
                'nama_wp' => $row[9],
                'pokok_pajak' => $row[10],
                'denda' => $row[11],
                'potongan' => $row[12],
                'admin' => $row[13],
                'total' => $row[14],
                'chnl' => $row[15],
                'kd_kantor' => $row[16],
                'user' => $row[17],
            ]);
        }
    }
}
