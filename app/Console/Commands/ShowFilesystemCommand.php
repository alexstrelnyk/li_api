<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class ShowFilesystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filesystem:files {filesystem} {path?} {--r|recursively}';

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
        $disk = $this->storage::disk($this->input->getArgument('filesystem'));

        $filePaths = $disk->files($this->input->getArgument('path'), $this->input->getOption('recursively'));

        $this->output->writeln(sprintf('%s files founded', count($filePaths)));

        foreach ($filePaths as $key => $filePath) {
            $this->output->writeLn(sprintf('%s. %s', $key + 1, $filePath));
            $this->output->writeLn($disk->url($filePath));
        }

        return 0;
    }
}
