<?php

namespace App\Actions\File;

use App\Models\Customer;
use App\Models\File;
use App\Helpers\TextFormatting;
use App\Http\Traits\Actions\ModelActionBase;
use App\Http\Traits\Actions\ResponseMessage;
use App\Jobs\ParseCsvFile;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @method bool execute(Customer $customer, array $data = [])
 * @property Customer $actionRecord
 */
class Upload
{
    use ModelActionBase;
    use ResponseMessage;

    /**
     * @param array $data
     */
    protected function setParameters(array $data): void
    {
        $this->data = [
            'file' => $data['file'] ?? '',
            'checksum' => '',
        ];
    }

    /**
     * @return bool|array
     * @throws Exception
     */
    protected function main()
    {
        try {
            $this->verifyCustomer();
            $this->validateInputs();
            $this->checksum();
            $path = $this->uploadFile();
            $fileReg = $this->register($path);

            //run import job
            //ParseCsvFile::dispatch($fileReg);

            return $this->responseSuccess('Arquivo enviado com sucesso, iniciando processo de registro dos dados...');
        } catch (Exception $exception) {
            return $this->responseFailure('error', $exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function checksum()
    {
        $this->data['checksum'] = sha1_file($this->data['file']);
        $file = $this->actionRecord
            ->files()
            ->checksum($this->data['checksum'])
            ->exists();

        if ($file) {
            throw new Exception('Esse arquivo já foi upado');
        }
    }

    /**
     * @param string $path
     * @return File
     */
    private function register(string $path)
    {
        $this->actionRecord->files()->create([
            'name' => $this->data['file']->getClientOriginalName(),
            'size' => $this->data['file']->getSize(),
            'path' => $path,
            'sha1_checksum' => $this->data['checksum'],
        ]);

        return File::checksum($this->data['checksum'])->first();
    }

    /**
     * @return false
     */
    private function uploadFile()
    {
        $path = '/'.env('UPLOAD_PATH').'/'.$this->actionRecord->hash;
        $filename = explode(".", $this->data['file']->getClientOriginalName());
        $newFileName = Str::slug($filename[0]).'.'.$filename[1];

        $upload = $this->data['file']->storeAs($path, $newFileName, 'public');

        if ($upload !== false) {
            return $upload;
        }

        return false;
    }


    /**
     * @throws Exception
     */
    private function verifyCustomer()
    {
        if (empty($this->actionRecord)) {
            throw new Exception('Não foi possível encontrar o cliente selecionado');
        }
    }

    /**
     * @throws Exception
     */
    private function validateInputs()
    {
        $validator = Validator::make(
            $this->data,
            [
                'file' => 'required|mimes:csv,txt'
            ]
        );

        if ($validator->fails()) {
            throw new Exception(
                TextFormatting::getValidatorString($validator)
            );
        }
    }
}
