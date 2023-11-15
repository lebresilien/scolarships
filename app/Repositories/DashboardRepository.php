<?php

namespace App\Repositories;
use App\Models\{ Transaction, Section, Inscription, Group, Classroom, Account };

class DashboardRepository
{
    
    public function statistics($account_id, $academy_id) {
        
        $account = Account::findOrFail($account_id);
        
        $count_groups_account = 0;
        $count_classrooms_account = 0;
        $count_students_account = 0;
        $count_buildings_account = 0;

        foreach($account->sections as $section) {
           
            $count_groups_account += $section->groups->count();
            
            foreach($section->groups as $group) {

                $count_classrooms_account += $group->classrooms->count();

                foreach($group->classrooms as $classroom) {

                    $count_students_account += $classroom->students()->wherePivot('academy_id', $academy_id)->get()->count();
                    
                }

            }
        }


        $inscriptions = Inscription::where('academy_id', $academy_id)->get();
        $transactions = $this->transactions($inscriptions->pluck('id'));

        return [
            "users" => $account->users->count(),
            "sections" => $account->sections->count(),
            "groups" => $count_groups_account,
            "classrooms" => $count_classrooms_account,
            "students" => $count_students_account,
            "buildings" => $account->buildings->count(),
            "transactions" => $transactions
        ];
    }

    private function transactions($inscription_id) {

        // Obtenir la date du jour
        $today = now();

        $months = collect([]);
        $days = collect([]);

        $period = \Carbon\CarbonPeriod::create(now()->subMonths(10), '1 months', $today);
        $week_period = \Carbon\CarbonPeriod::create(now()->subDays(7), '1 days', $today);
        
        foreach($period as $p) {
            $months->push($p);
        }

        foreach($week_period as $d) {
            $days->push($d);
        }

        $arr = [];
        $transactions = collect([]);
        $days_arr = [];
        $week_transactions = collect([]);

        // Récupérer le nombre de ventes pour chaque mois
        foreach($months as $month) {
            // Obtenir la date de début du mois
            $start = $month->startOfMonth()->format('Y-m-d');

            // Obtenir la date de fin du mois
            $end = $month->endOfMonth()->format('Y-m-d');

            // Obtenir le nombre de ventes
            $total = Transaction::whereBetween('created_at', [$start, $end])->whereIn('inscription_id', $inscription_id)->sum('amount');
            $transactions->push($total);

            array_push($arr, $month->format('F'));
            // Renvoyer le mois et le nombre de transactions
        };

        // Récupérer le nombre de ventes pour chaque mois
        foreach($days as $day) {

            // Obtenir de transaction journaliere
            $total = Transaction::whereDate('created_at', $day)->whereIn('inscription_id', $inscription_id)->sum('amount');
            $week_transactions->push($total);

            array_push($days_arr, $day->format('D'));
        
        };
        
        // Renvoyer les résultats
        return  [
            'months' => $arr,
            'values' => $transactions,
            'days' => $days_arr,
            'day_values' => $week_transactions
        ];
    }

}
