<?php
namespace App\SegurosEnLinea\Sepomex\PostalCodes\Heuristics;

use \App\SegurosEnLinea\Sepomex\PostalCodes\Repositories\PostalCodeCreatorRepository;
use \App\SegurosEnLinea\Sepomex\PostalCodes\Repositories\TruncatePostalCodesRepository;

use \Log;
use \Exception;

class ImportFromFileHeuristic
{

	const ROW_KEYS = [
		'd_codigo',
		'd_asenta',
		'd_tipo_asenta',
		'D_mnpio',
		'd_estado',
		'd_ciudad',
		'd_CP',
		'c_estado',
		'c_oficina',
		'c_CP',
		'c_tipo_asenta',
		'c_mnpio',
		'id_asenta_cpcons',
		'd_zona',
		'c_cve_ciudad',
	];

    private $postalCodeCreatorRepository;
    private $truncatePostalCodesRepository;

	public function __construct(PostalCodeCreatorRepository $postalCodeCreatorRepository, TruncatePostalCodesRepository $truncatePostalCodesRepository    )
	{
		$this->postalCodeCreatorRepository = $postalCodeCreatorRepository;
		$this->truncatePostalCodesRepository = $truncatePostalCodesRepository;
	}

	public function __invoke(string $filename)
	{
        $fileNamePath = storage_path('sepomex' .DIRECTORY_SEPARATOR . $filename);
        Log::debug($fileNamePath);

		// if(!\Storage::exists(
		// 	$fileNamePath
		// )) {
		// 	throw new \Exception('No se encuentra del archivo: ' . $filename, 500);
		// }

		$ao = new \ArrayObject(
			array_fill_keys(self::ROW_KEYS, NULL)
		);

		$f = @fopen($fileNamePath, 'r');
		if(!$f) {
			throw new Exception('No puede abrir el archivo', 500);
		}

        # Truncate
        $r = $this->truncatePostalCodesRepository; $r();

        $creatorRepository = $this->postalCodeCreatorRepository;
		$index = 0;
		while (($row = fgetcsv($f, null, '|')) !== FALSE) {
			++$index;

			if(count($row) != 15 || $row[0] === 'd_codigo') {
				Log::info('Ignorando línea');
				continue;
			}

			$row = array_map('utf8_encode', $row);

			Log::info($row);

			$arrayData = $ao->getArrayCopy();
			for ($i=0; $i < count($row); $i++) {
				$arrayData[ self::ROW_KEYS[ $i ] ] = $row[$i];
			}

			$rawId = $creatorRepository($arrayData);

			Log::info('Registro insertado: ID: ' . $rawId);

			if($rawId <= 0) {
				throw new Exception('Ocurrió un error al insertar', 500);
			}
		}

		if(!is_null($f)) {
			fclose($f);
			$f = NULL;
		}
	}

}

/* End of file ImportFromFileHeuristic.php */
