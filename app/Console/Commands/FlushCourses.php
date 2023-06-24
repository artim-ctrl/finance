<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\Currency\GettingCourseService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as BaseCommand;

final class FlushCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command flushes all courses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        app(GettingCourseService::class)->flush();

        return BaseCommand::SUCCESS;
    }
}
