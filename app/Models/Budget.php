<?php

namespace App\Models;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'name','start_date','end_date','period','created_by'
    ];

    public static $period = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'half-yearly' =>'Half Yearly',
        'yearly' => 'Yearly',

    ];

    public function getAvailabilityDate()
    {

        $start_date = '';
        $end_date = '';
        $date = '';
        $date_formate =('M-Y');
        if(!empty($this->start_date)) {
            $start_date = date ($date_formate , strtotime($this->start_date) );
            $date = $start_date;
        }
        if(!empty($this->end_date)) {
            $end_date = date ($date_formate , strtotime($this->end_date) );
            $date .= ' - ' .$end_date.' ';
        }


        return $date;
    }

    public static function percentage($actual,$budget)
    {
        $percentage = $budget*100/$actual;
        return  number_format($percentage,2);

    }

    public static function periodKeyForDate($period, $date)
    {
        $month = (int)date('n', strtotime($date));
        if($period == 'monthly')
        {
            return date('F', strtotime($date));
        }
        if($period == 'quarterly')
        {
            if($month >= 1 && $month <= 3)
            {
                return 'Jan-Mar';
            }
            if($month >= 4 && $month <= 6)
            {
                return 'Apr-Jun';
            }
            if($month >= 7 && $month <= 9)
            {
                return 'Jul-Sep';
            }
            return 'Oct-Dec';
        }
        if($period == 'half-yearly')
        {
            return $month <= 6 ? 'Jan-Jun' : 'Jul-Dec';
        }
        if($period == 'yearly')
        {
            return 'Jan-Dec';
        }
        return null;
    }

    public static function monthRangeForDate($period, $date)
    {
        $month = (int)date('n', strtotime($date));
        if($period == 'monthly')
        {
            return [$month, $month];
        }
        if($period == 'quarterly')
        {
            if($month >= 1 && $month <= 3)
            {
                return [1, 3];
            }
            if($month >= 4 && $month <= 6)
            {
                return [4, 6];
            }
            if($month >= 7 && $month <= 9)
            {
                return [7, 9];
            }
            return [10, 12];
        }
        if($period == 'half-yearly')
        {
            return $month <= 6 ? [1, 6] : [7, 12];
        }
        if($period == 'yearly')
        {
            return [1, 12];
        }
        return [null, null];
    }

    public static function expenseActualForPeriod($creatorId, $categoryId, $period, $date)
    {
        $year = date('Y', strtotime($date));
        [$startMonth, $endMonth] = self::monthRangeForDate($period, $date);
        if(empty($startMonth) || empty($endMonth))
        {
            return 0;
        }

        $paymentAmount = Payment::where('created_by', '=', $creatorId)
            ->where('category_id', '=', $categoryId)
            ->whereRaw('YEAR(date) =?', [$year])
            ->whereRaw('MONTH(date) >=?', [$startMonth])
            ->whereRaw('MONTH(date) <=?', [$endMonth])
            ->sum('amount');

        $bills = Bill::where('created_by', '=', $creatorId)
            ->where('category_id', '=', $categoryId)
            ->whereRaw('YEAR(send_date) =?', [$year])
            ->whereRaw('MONTH(send_date) >=?', [$startMonth])
            ->whereRaw('MONTH(send_date) <=?', [$endMonth])
            ->with(['items'])
            ->get();

        $billTotalAmount = 0;
        foreach($bills as $bill)
        {
            $billTotalAmount += $bill->getTotal();
        }

        return $paymentAmount + $billTotalAmount;
    }

    public static function expenseBudgetAlert($creatorId, $categoryId, $date, $amount)
    {
        $year = date('Y', strtotime($date));
        $budgets = self::where('created_by', '=', $creatorId)->where('from', '=', $year)->get();

        foreach($budgets as $budget)
        {
            $expenseData = json_decode($budget->expense_data, true) ?? [];
            if(!isset($expenseData[$categoryId]))
            {
                continue;
            }

            $periodKey = self::periodKeyForDate($budget->period, $date);
            if(empty($periodKey))
            {
                continue;
            }

            $budgetAmount = (float)($expenseData[$categoryId][$periodKey] ?? 0);
            if($budgetAmount <= 0)
            {
                continue;
            }

            $actualAmount = self::expenseActualForPeriod($creatorId, $categoryId, $budget->period, $date);
            $previousAmount = $actualAmount - $amount;

            if($previousAmount <= $budgetAmount && $actualAmount > $budgetAmount)
            {
                return [
                    'budget' => $budget,
                    'budget_amount' => $budgetAmount,
                    'actual_amount' => $actualAmount,
                    'previous_amount' => $previousAmount,
                    'period_key' => $periodKey,
                    'year' => $year,
                ];
            }
        }

        return null;
    }
}
