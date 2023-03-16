<?php

namespace App\Console\Commands;

use App\Services\Currency\GettingCourseService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Command\Command as BaseCommand;

class FlushCourses extends Command
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
     * @throws BindingResolutionException
     */
    public function handle(): int
    {
        /** @var GettingCourseService $gettingCourseService */
        $gettingCourseService = app()->make(GettingCourseService::class);
        $gettingCourseService->flush();

        return BaseCommand::SUCCESS;
    }
}
