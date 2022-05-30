<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\Matche;
use App\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function getCurrentGame($id)
    {
        return DB::table('current_games')->orderBy('created_at', 'desc')->where(['user_id1', $id])->orWhere(['user_id2', $id])->take(1)->get();
    }

    public function createBoard($size)
    {
        $board = [];

        for ($i = 0; $i < $size; $i++) {
            $board[$i] = [];
            for ($j = 0; $j < $size; $j++) {
                $board[$i][$j] = '0';
            }
        }
        return $board;
    }


    function createHeaders($size) {
        $result = '  ';
        for ($i = 0; $i < $size; $i++) {
            $result .= $i + '  ';
        }
        return $result;
    }

    function winGame()
    {
        $currentGame = DB::table('current_games')->orderBy('created_at', 'desc')->where('user_id1', Auth::user()->id)->orWhere('user_id2', Auth::user()->id)->take(1)->get();

        $coconutAmount1 = DB::table('inventories')->join('products','inventories.product_id','=','products.id')->where('user_id',$currentGame[0]->user_id1)->where('category','consumable')->where('equipped',1)->select('products.*', 'inventories.*')->get();

        $coconutAmount2 = DB::table('inventories')->join('products','inventories.product_id','=','products.id')->where('user_id',$currentGame[0]->user_id2)->where('category','consumable')->where('equipped',1)->select('products.*', 'inventories.*')->get();

        if (count($currentGame) > 0 && ($coconutAmount1[0]->amount) > 0 && ($coconutAmount2[0]->amount) > 0) {
                // dd($score[0]->score);

            $arrayPlayers = [$currentGame[0]->user_id1, $currentGame[0]->user_id2];

            $winnerKey = array_rand($arrayPlayers);

            $winner = $arrayPlayers[$winnerKey];

            if ($winnerKey == 0) {
                $loser = $arrayPlayers[1];
            } else {
                $loser = $arrayPlayers[0];
            }

            Matche::create(['user_id1'=>$winner,'user_id2'=>$loser,'winner'=>$winner,'points'=>100, 'date'=> now(), 'created_at'=>now(), 'updated_at'=>now()]);

            $scoreWinner =  Score::where('id_user', $winner)->get();
            $scoreLoser =  Score::where('id_user', $loser)->get();

            Score::where('id_user', $winner)->update(['score'=>$scoreWinner[0]->score+100]);

            if (intval($scoreLoser[0]->score) > 0) {

                Score::where('id_user', $loser)->update(['score'=>$scoreLoser[0]->score-100]);
            }

            //dd($currentGame[0]->user_id2);

            DB::table('inventories')->join('products','inventories.product_id','=','products.id')->where('user_id',$currentGame[0]->user_id1)->where('category','consumable')->where('equipped',1)->select('products.*', 'inventories.*')->decrement('amount', 1);

            DB::table('inventories')->join('products','inventories.product_id','=','products.id')->where('user_id',$currentGame[0]->user_id2)->where('category','consumable')->where('equipped',1)->select('products.*', 'inventories.*')->decrement('amount', 1);

            return back();
        }
        return back();
    }
}
