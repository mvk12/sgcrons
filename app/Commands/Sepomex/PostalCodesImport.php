<?php

namespace App\Commands\Sepomex;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\SegurosEnLinea\Sepomex\PostalCodes\Heuristics\ImportFromFileHeuristic;
use App\SegurosEnLinea\Sepomex\PostalCodes\Repositories\PostalCodeCreatorRepository;
use App\SegurosEnLinea\Sepomex\PostalCodes\Repositories\TruncatePostalCodesRepository;

class PostalCodesImport extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sepomex:import:cp';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Importación de códigos postales de SEPOMEX';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $postalCodeCreatorRepository = new PostalCodeCreatorRepository();
            $truncatePostalCodesRepository = new TruncatePostalCodesRepository();
            $heuristic = new ImportFromFileHeuristic($postalCodeCreatorRepository, $truncatePostalCodesRepository);
            $d = $heuristic('CPdescarga.txt');

            $this->line(print_r($d, true));
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
