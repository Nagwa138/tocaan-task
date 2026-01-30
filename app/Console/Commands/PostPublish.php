<?php

namespace App\Console\Commands;

use App\Architecture\Repositories\Interfaces\IPostRepository;
use App\Jobs\ProcessPostJob;
use Illuminate\Console\Command;

class PostPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish ready posts';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $posts = app(IPostRepository::class)->listReadyForPublish();
        foreach ($posts as $post) {
            ProcessPostJob::dispatch($post);
        }
        $this->info('Posts published');
    }
}
