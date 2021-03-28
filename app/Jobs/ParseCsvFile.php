<?php
namespace App\Jobs;

set_time_limit(0);

use App\Events\SendNotification;
use App\Models\Customer;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ParseCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;

    private $data;

    public const EACH_COMMIT = 5000;
    public const OUTPUT_SQL = true;

    private $file;

    private $customerContas;

    /**
     * Create a new job instance.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
        $this->customerContas = [];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function handle()
    {
        //load contas
        $this->loadContas();

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
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            broadcast(new SendNotification([
                'title' => 'Erro ao processar o arquivo ' . $this->file->name . ': '. $e->getMessage(),
                'body' => 'Erro ao processar o arquivo ' . $this->file->name . ': '. $e->getMessage(),
                'type' => 'error',
                'duration' => 300000,
            ]));

            throw $e;
        }
    }

    /**
     *
     */
    private function loadContas()
    {
        if (!$this->file->customer instanceof Customer || empty($this->file->customer->contas)) {
            return;
        }

        $this->customerContas = $this->file->customer->contas()->pluck('conta_id', 'nome_sha1');
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

            $date3 = new Carbon($this->data[3]); //emissao nota
            $date4 = new Carbon($this->data[4]); //data_vencimento_original
            $date5 = new Carbon($this->data[5]); //data_vencimento
            $date6 = new Carbon($this->data[6]); //competencia
            $date15 = new Carbon($this->data[15]); //data_entrada

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
                    conta_id,
                    conta_sha1,
                    counter_id,
                    customer_id
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
                    " . ($this->customerContas[sha1($this->data[7])] ?? 0). ",
                    '" . sha1($this->data[7]) . "',
                    " . (int)$xCounter . ",
                    " . $this->file->customer_id . ")";

            DB::unprepared($sql);

            if ($toImport === self::EACH_COMMIT) {
                DB::commit();
                $toImport = 0;
            }

            $xCounter++;
        }

        DB::select(DB::raw('call SumarizarVencimento("' . $this->file->sha1_checksum . '")'));

        DB::commit();
        $this->updateFileStatus('Processado');
        broadcast(new SendNotification([
            'title' => $this->file->name . ' foi processado com sucesso.',
            'body' => $this->file->name . ' foi processado com sucesso.',
            'type' => 'success',
            'duration' => 20500,
        ]));
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

            /**
             * 3 - emissao nota
             * 4 - data_vencimento_original
             * 5 - data_vencimento
             * 6 - competencia
             * 15 - data_entrada
             */

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
                        conta_id,
                        conta_sha1,
                        counter_id,
                        customer_id
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
                        " . ($this->customerContas[sha1($this->data[7])] ?? 0). ",
                        '" . sha1($this->data[7]) . "',
                        " . (int)$xCounter . ",
                        " . $this->file->customer_id . ");\n";

            $xCounter++;
        }

        $sql .= "call SumarizarVencimento(\"" . $this->file->sha1_checksum . "\");\n";

        $filename = Str::slug(explode('.', $this->file->name)[0]);
        $path = env('UPLOAD_PATH').'/'.$this->file->customer->hash.'/sqls/'.$filename.'.sql';
        Storage::put('/'.$path, $sql);
        $filePath = storage_path('app/public/'.$path);
        $command = 'mysql -h ' . env('DB_HOST') . ' -u ' . env('DB_USERNAME') . ' -p\'' . env('DB_PASSWORD') . '\' -D ' . env('DB_DATABASE') . ' < ' . $filePath;
        $this->execMysqlCommand($command);

        $this->updateFileStatus('Processado');
        broadcast(new SendNotification([
            'title' => $this->file->name . ' foi processado com sucesso.',
            'body' => $this->file->name . ' foi processado com sucesso.',
            'type' => 'success',
            'duration' => 7500,
        ]));
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

    /**
     * @param string $command
     * @return bool
     */
    private function execMysqlCommand(string $command)
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600);
        $process->run();

        return true;
    }
}
