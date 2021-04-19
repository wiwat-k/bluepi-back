<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        if ($request->has('username')) {
            $res['global'] = $this->getScoreGlobalBest();
            $user = DB::table('users')->where('name', $request->get('username'))->first();
            if ($user) {
                $res['user'] = $user->id;
                $score = DB::table('score_logs')->where('user_id', $user->id)->orderBy('score')->first();
                if ($score) {
                    $res['score'] = $this->getScoreMyBest($user->id);;
                } else {
                    $res['score'] = '-';
                }
                return response()->json(['status' => 'success', 'data' => $res], 200);
            } else {
                $userId = DB::table('users')->insertGetId(['name' => $request->get('username')]);
                $res['user'] = $userId;
                $res['score'] = '-';
                return response()->json(['status' => 'success', 'data' => $res], 200);
            }
        }
    }

    public function getGlobalBest()
    {
        $score = DB::table('score_logs')->orderBy('score')->first();
        $res = null;
        if ($score) {
            $res = $score;
        } else {
            $res = '-';
        }
        return $res;
    }

    public function startGame(Request $request)
    {
        if ($request->has('user')) {
            $cardArray = [];
            $cardId = [];
            foreach (range(1, 6) as $number) {
                foreach (range(1, 2) as $row) {
                    $Id = Str::uuid();
                    $cardArray[] = [
                        'id' => $Id,
                        'number' => $number
                    ];
                    $cardId[] = ['id' => $Id];
                }
            }
            Cache::put('cards' . $request->get('user'), $cardArray);
            Cache::put('score' . $request->get('user'), 0);
            shuffle($cardId);
            return response()->json(['status' => 'success', 'data' => $cardId], 200);
        }
    }

    public function checkCard(Request $request)
    {
        if ($request->has('user')) {
            if ($request->has('card1') && $request->has('card2')) {
                $card1 = $this->getValueOfArray($request->get('user'), $request->get('card1'));
                $card2 = $this->getValueOfArray($request->get('user'), $request->get('card2'));
                if ($card1 == $card2) {
                    return response()->json(['status' => 'success', 'data' => ['result' => true, 'card1' => $card1, 'card2' => $card2]], 200);
                } else {
                    return response()->json(['status' => 'success', 'data' => ['result' => false]], 200);
                }
            }
        }
    }

    public function getValueOfArray($user = '', $Id = '')
    {
        $cardSession = Cache::get('cards' . $user);
        foreach ($cardSession as $obj) {
            if ($obj['id'] == $Id) {
                return $obj['number'];
            }
        }
        return false;
    }

    public function openCard(Request $request)
    {
        if ($request->has('card') && $request->has('user')) {
            $this->countScore($request->get('user'));
            $card = $this->getValueOfArray($request->get('user'), $request->get('card'));
            return response()->json(['status' => 'success', 'data' => ['number' => $card, 'score'=> $this->getScore($request->get('user'))]], 200);
        }
    }

    public function countScore($user)
    {
        Cache::increment('score' . $user, 1);
    }

    public function getScore($user)
    {
        return Cache::get('score' . $user);
    }

    public function saveScore(Request $request)
    {
        if ($request->has('user')) {
            if ($request->has('score')) {
                $user = DB::table('users')->where('id', $request->get('user'))->first();
                if ($user) {
                    DB::table('score_logs')->insert(['score' => $this->getScore($user->id), 'user_id' => $user->id]);
                    $global = $this->getScoreGlobalBest();
                    $score = $this->getScoreMyBest($user->id);
                    return response()->json(['status' => 'success', 'data' => ['score' => $score, 'global' => $global]], 200);
                }
            }
        }
    }

    public function getScoreGlobalBest()
    {
        $rs = DB::table('score_logs')->orderBy('score','asc')->first();
        if ($rs) {
            return $rs->score;
        } else {
            return '-';
        }
    }
    public function getScoreMyBest($userId)
    {
        $rs = DB::table('score_logs')->where('user_id', $userId)->orderBy('score','asc')->first();
        if ($rs) {
            return $rs->score;
        } else {
            return '-';
        }
    }
}
