<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\Currency\GettingCourseService;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as BaseCommand;

final class UpdateCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates all courses and set them to cache';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __invoke(): int
    {
        $attempts = 3;

        $gettingCourseService = app(GettingCourseService::class);

        while (0 !== $attempts) {
            try {
                $gettingCourseService->loadCoursesToCache();

                break;
            } catch (Exception) {
                $attempts--;
            }
        }

        return BaseCommand::SUCCESS;
    }
}
