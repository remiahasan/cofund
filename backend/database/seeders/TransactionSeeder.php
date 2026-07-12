<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backing;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(Backing::all() as $backing){

            Transaction::create([
                'user_id'=>$backing->user_id,
                'backing_id'=>$backing->id,
                'type'=>'payment',
                'amount'=>$backing->amount,
                'status'=>'success',
                'reference'=>'TRX-'.str_pad($backing->id,5,'0',STR_PAD_LEFT)
            ]);
        }
    }
}
