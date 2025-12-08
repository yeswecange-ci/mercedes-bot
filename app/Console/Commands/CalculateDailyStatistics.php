<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyStatistic;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use Carbon\Carbon;

class CalculateDailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:calculate {--from= : Start date (Y-m-d)} {--to= : End date (Y-m-d)} {--force : Recalculate existing stats}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate daily statistics from conversations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily statistics calculation...');

        // Déterminer la plage de dates
        $from = $this->option('from')
            ? Carbon::parse($this->option('from'))
            : Conversation::min('started_at');

        $to = $this->option('to')
            ? Carbon::parse($this->option('to'))
            : now();

        if (!$from) {
            $this->error('No conversations found in the database.');
            return Command::FAILURE;
        }

        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        $this->info("Calculating stats from {$from->format('Y-m-d')} to {$to->format('Y-m-d')}");

        // Obtenir toutes les dates uniques où il y a eu des conversations
        $dates = Conversation::whereBetween('started_at', [$from, $to])
            ->selectRaw('DATE(started_at) as date')
            ->distinct()
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date);
            });

        $this->info("Found {$dates->count()} days with conversations");

        $progressBar = $this->output->createProgressBar($dates->count());
        $progressBar->start();

        $created = 0;
        $updated = 0;

        foreach ($dates as $date) {
            // Vérifier si les stats existent déjà
            $existing = DailyStatistic::where('date', $date->format('Y-m-d'))->first();

            if ($existing && !$this->option('force')) {
                $updated++;
                $progressBar->advance();
                continue;
            }

            // Calculer les statistiques pour cette date
            $stat = DailyStatistic::recalculateForDate($date);

            // Calculer les statistiques de menu à partir des événements
            $this->calculateMenuStats($stat, $date);

            if ($existing) {
                $updated++;
            } else {
                $created++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Statistics calculation completed!");
        $this->info("- New stats: {$created}");
        $this->info("- Updated stats: {$updated}");
        $this->info("- Total processed: {$dates->count()}");

        return Command::SUCCESS;
    }

    /**
     * Calculate menu statistics for a given date
     */
    protected function calculateMenuStats(DailyStatistic $stat, Carbon $date)
    {
        // Obtenir toutes les conversations de cette date
        $conversationIds = Conversation::whereDate('started_at', $date)->pluck('id');

        // Compter les choix de menu principal (événements avec menu_choice et user_input 1-5)
        // On ne filtre pas par menu_name car il peut être vide
        $menuChoices = ConversationEvent::whereIn('conversation_id', $conversationIds)
            ->where('event_type', 'menu_choice')
            ->whereIn('user_input', ['1', '2', '3', '4', '5'])
            ->get();

        $menuCounts = [
            'menu_vehicules_neufs' => 0,
            'menu_sav' => 0,
            'menu_reclamations' => 0,
            'menu_club_vip' => 0,
            'menu_agent' => 0,
        ];

        $mapping = [
            '1' => 'menu_vehicules_neufs',
            '2' => 'menu_sav',
            '3' => 'menu_reclamations',
            '4' => 'menu_club_vip',
            '5' => 'menu_agent',
        ];

        foreach ($menuChoices as $choice) {
            $input = $choice->user_input;
            if (isset($mapping[$input])) {
                $menuCounts[$mapping[$input]]++;
            }
        }

        // Mettre à jour les compteurs de menu
        foreach ($menuCounts as $field => $count) {
            $stat->$field = $count;
        }

        $stat->save();
    }
}
