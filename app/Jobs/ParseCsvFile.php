<?php
namespace App\Jobs;

set_time_limit(0);
use App\Models\File;
use App\Models\TrialData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParseCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;

    private $data;

    public const EACH_COMMIT = 5000;
    public const OUTPUT_SQL = false;

    private $file;

    /**
     * Create a new job instance.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->updateFileStatus('Processando');

        /**
         * ""Código",
         * "Cliente",
         * "Documento",
         * "Emissao_Nota",
         * "Data de Vencimento Original",
         * "Data de Vencimento",
         * "Competencia",
         * "Natureza Financeira",
         * "Operação",
         * "Titulo Pago",
         * "Valor_Original",
         * "Multa_Aplicada",
         * "Juros_Aplicado",
         * "Desconto_aplicado",
         * "Valor_recebido",
         * "Data de Entrada(pgto)",
         * "Empresa_emissora_titulo",
         * "Empresa_emissora_nota",
         * "ID_titulo""
         */
        $accessFile = explode("\r\n", Storage::get($this->file->path));
        $xCounter = strpos($accessFile[0], 'Data de Vencimento') > 0 ? 1 : 0;

        //removing last empty item if has
        $totalSize = empty($accessFile[count($accessFile)-1]) ? count($accessFile)-2 : count($accessFile)-1;

        //begin transaction
        if (!self::OUTPUT_SQL) {
            DB::beginTransaction();
        }

        try {
            if (self::OUTPUT_SQL) {
                $this->parseToSql($accessFile, $xCounter, $totalSize);
                return 'SQL File Created';
            }

            $this->parseToDB($accessFile, $xCounter, $totalSize);
            return 'CSV Migrated to DB';

        } catch (\Exception $e) {
            $this->updateFileStatus('Erro ao Processar');
            dump($e->getMessage(), $this->data);
        }
    }

    /**
     * @param array $accessFile
     * @param int $xCounter
     * @param int $totalSize
     * @throws \Exception
     */
    private function parseToDB($accessFile, $xCounter, $totalSize)
    {
        //remove old registers
        $this->reprocessFile();
        $toImport = 0;
        while ($xCounter <= $totalSize) {
            $toImport++;

            $this->data = str_getcsv($accessFile[$xCounter]);

            if (is_null($this->data[0])) {
                $xCounter++;
                continue;
            }

            $name = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $this->data[1]);

            $date3 = new Carbon($this->data[3]);
            $date4 = new Carbon($this->data[4]);
            $date5 = new Carbon($this->data[5]);
            $date6 = new Carbon($this->data[6]);
            $date15 = new Carbon($this->data[15]);

            $sql = "INSERT INTO trial_data (
                    codigo,
                    cliente,
                    documento,
                    emissao_nota,
                    data_vencimento_original,
                    data_vencimento,
                    competencia,
                    natureza_financeira,
                    operacao,
                    titulo_pago,
                    valor_original,
                    multa_aplicada,
                    juros_aplicada,
                    desconto_aplicado,
                    valor_recebido,
                    data_entrada,
                    emissora_titulo,
                    emissora_nota,
                    id_titulo,
                    created_at,
                    updated_at,
                    file_checksum,
                    counter_id
                    ) VALUES (
                    " . (int)$this->data[0] . ",
                    '" . $name . "',
                    '" . $this->data[2] . "',
                    '" . $date3->toDateTimeString() . "',
                    '" . $date4->toDateTimeString() . "',
                    '" . $date5->toDateTimeString() . "',
                    '" . $date6->toDateTimeString() . "',
                    '" . $this->data[7] . "',
                    '" . $this->data[8] . "',
                    '" . $this->data[9] . "',
                    '" . (float)$this->data[10] . "',
                    '" . (float)$this->data[11] . "',
                    '" . (float)$this->data[12] . "',
                    '" . (float)$this->data[13] . "',
                    '" . (float)$this->data[14] . "',
                    '" . $date15->toDateTimeString() . "',
                    '" . $this->data[16] . "',
                    '" . $this->data[17] . "',
                    " . (int)$this->data[18] . ",
                    '" . now()->toDateTimeString() . "',
                    '" . now()->toDateTimeString() . "',
                    '" . $this->file->sha1_checksum . "',
                    " . (int)$xCounter . ")";

            DB::unprepared($sql);

//            TrialData::create([
//            $dataToImport[] = [
//                'codigo' => (int)$this->data[0],
//                'cliente' => $name,
//                'documento' => $this->data[2],
//                'emissao_nota' => new Carbon($this->data[3]),
//                'data_vencimento_original' => new Carbon($this->data[4]),
//                'data_vencimento' => new Carbon($this->data[5]),
//                'competencia' => new Carbon($this->data[6]),
//                'natureza_financeira' => $this->data[7],
//                'operacao' => $this->data[8],
//                'titulo_pago' => $this->data[9],
//                'valor_original' => (float)$this->data[10],
//                'multa_aplicada' => (float)$this->data[11],
//                'juros_aplicada' => (float)$this->data[12],
//                'desconto_aplicado' => (float)$this->data[13],
//                'valor_recebido' => (float)$this->data[14],
//                'data_entrada' => new Carbon($this->data[15]),
//                'emissora_titulo' => $this->data[16],
//                'emissora_nota' => $this->data[17],
//                'id_titulo' => (int)$this->data[18],
//                'file_checksum' => $this->file->sha1_checksum,
//                'counter_id' => $xCounter,
//            ];
//
//            DB::table('trial_data')->insert($dataToImport);

//            ]);

            if ($toImport === self::EACH_COMMIT) {
                DB::commit();
                $toImport = 0;
            }

            $xCounter++;
        }

        DB::select(DB::raw('call SumarizarVencimento("' . $this->file->sha1_checksum . '")'));

        DB::commit();
        $this->updateFileStatus('Processado');
    }

    /**
     * @param array $accessFile
     * @param int $xCounter
     * @param int $totalSize
     * @throws \Exception
     */
    private function parseToSql($accessFile, $xCounter, $totalSize)
    {
        $sql = "";

        while ($xCounter < $totalSize) {
            $this->data = str_getcsv($accessFile[$xCounter]);

            if (is_null($this->data[0])) {
                $xCounter++;
                continue;
            }

            $name = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $this->data[1]);

            $sql .= "INSERT INTO trial_data (
                        codigo,
                        cliente,
                        documento,
                        emissao_nota,
                        data_vencimento_original,
                        data_vencimento,
                        competencia,
                        natureza_financeira,
                        operacao,
                        titulo_pago,
                        valor_original,
                        multa_aplicada,
                        juros_aplicada,
                        desconto_aplicado,
                        valor_recebido,
                        data_entrada,
                        emissora_titulo,
                        emissora_nota,
                        id_titulo,
                        created_at,
                        updated_at,
                        file_checksum,
                        counter_id
                        ) VALUES (
                        " . (int)$this->data[0] . ",
                        '" . $name . "',
                        '" . $this->data[2] . "',
                        '" . new Carbon($this->data[3]) . "',
                        '" . new Carbon($this->data[4]) . "',
                        '" . new Carbon($this->data[5]) . "',
                        '" . new Carbon($this->data[6]) . "',
                        '" . $this->data[7] . "',
                        '" . $this->data[8] . "',
                        '" . $this->data[9] . "',
                        '" . (float)$this->data[10] . "',
                        '" . (float)$this->data[11] . "',
                        '" . (float)$this->data[12] . "',
                        '" . (float)$this->data[13] . "',
                        '" . (float)$this->data[14] . "',
                        '" . new Carbon($this->data[15]) . "',
                        '" . $this->data[16] . "',
                        '" . $this->data[17] . "',
                        " . (int)$this->data[18] . ",
                        '" . new Carbon() . "',
                        '" . new Carbon() . "',
                        '" . $this->file->sha1_checksum . "',
                        " . (int)$xCounter . "
                        );\n";

            $xCounter++;
        }

        $filename = Str::slug(explode('.', $this->file->name)[0]);
        Storage::put('/'.env('UPLOAD_PATH').'/'.$this->file->customer->hash.'/sqls/'.$filename.'.sql', $sql);
        $this->updateFileStatus('Processado');
    }

    private function reprocessFile()
    {
        if ($this->file->trialData()->exists()) {
            $this->file->trialData()->delete();
        }

        if ($this->file->vencimentoSummaries()->exists()) {
            $this->file->vencimentoSummaries()->delete();
        }
    }

    /**
     * @param string $status
     */
    private function updateFileStatus($status)
    {
        $this->file->update([
            'status' => $status,
        ]);
    }
}
