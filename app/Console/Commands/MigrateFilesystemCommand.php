<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class MigrateFilesystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filesystem:migrate {originFilesystem} {remoteFilesystem} {originPath?} {remotePath?} {--r|recursively} {--f|force-rewrite : rewrite existed files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files form origin filesystem to remote';

    /**
     * @var Storage
     */
    private $storage;

    /**
     * Create a new command instance.
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $originDisk = $this->storage::disk($this->input->getArgument('originFilesystem'));
        $remoteDisk = $this->storage::disk($this->input->getArgument('remoteFilesystem'));

        $filePaths = $originDisk->files($this->input->getArgument('originPath'), $this->input->getOption('recursively'));

        $this->output->writeln(sprintf('%s files founded', count($filePaths)));

        foreach ($filePaths as $key => $filePath) {
            $this->output->write(sprintf('%s. %s', $key + 1, $filePath));

            if ($remoteDisk->exists($filePath)) {
                if ($this->input->getOption('force-rewrite')) {
                    try {
                        $content = $originDisk->get($filePath);
                        $remoteDisk->put($filePath, $content);
                        $this->output->writeln(' (Rewrite)');
                    } catch (FileNotFoundException $e) {
                        $this->output->warning($e->getMessage());
                    }
                } else {
                    $this->output->writeln(' (Skipp. File already existed)');
                }
            } else {
                try {
                    $content = $originDisk->get($filePath);
                    $remoteDisk->put($filePath, $content);
                    $this->output->writeln(' (Ok)');
                } catch (FileNotFoundException $e) {
                    $this->output->warning($e->getMessage());
                }
            }
        }

        return 0;
    }
}
