<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index()
    {
        // Пример начальных данных для таблицы
        $accounts = [
            ['account' => '715044',    'subscription' => 200, 'balance' => -50,  'frozen' => false],
            ['account' => '71504401',  'subscription' => 150, 'balance' => 100,  'frozen' => false],
            ['account' => '71504402',  'subscription' => 100, 'balance' => -100, 'frozen' => false],
            ['account' => '71504403',  'subscription' => 0,   'balance' => 50,   'frozen' => true],
            ['account' => '71504404',  'subscription' => 150, 'balance' => 150,  'frozen' => false],
        ];

        return view('calculator', compact('accounts'));
    }
}
