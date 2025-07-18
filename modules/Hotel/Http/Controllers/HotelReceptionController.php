<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\HotelFloor;
use Modules\Hotel\Models\HotelRent;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\User;

class HotelReceptionController extends Controller
{
	public function index()
	{
		$rooms = $this->getRooms();

		if (request()->ajax()) {
			return response()->json([
				'success' => true,
				'rooms'   => $rooms,
			], 200);
		}
		$floors = HotelFloor::where('active', true)
                ->where('establishment_id',auth()->user()->establishment_id)
				->orderBy('description')
				->get();

		$roomStatus = HotelRoom::$status;

        $userType = auth()->user()->type;
		$establishmentId = auth()->user()->establishment_id;
        $establishments = Establishment::select('id','description')->get();

		return view('hotel::rooms.reception', compact('rooms', 'floors', 'roomStatus','userType','establishmentId','establishments'));
	}

    /**
     * Busqueda avanzada de cuartos.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function  searchRooms(Request $request ){

        $rooms = HotelRoom::with('category', 'floor', 'rates')
            ->where('establishment_id',auth()->user()->establishment_id);

        if ($request->has('hotel_floor_id') && !empty($request->hotel_floor_id)) {
            $rooms->where('hotel_floor_id', $request->hotel_floor_id);
        }
        if ($request->has('hotel_status_room') && !empty($request->hotel_status_room)) {
            $rooms->where('status',  $request->hotel_status_room);
        }
        if ($request->has('hotel_name_room') && !empty($request->hotel_name_room)) {
            $rooms->where('name','LIKE',  "%{$request->hotel_name_room}%");
        }
        $rooms =  $rooms->orderBy('name')->get()->each(function ($room) {
            if ($room->status === 'OCUPADO') {
                $rent = HotelRent::where('hotel_room_id', $room->id)
                    ->orderBy('id', 'DESC')
                    ->first();
                $room->rent = $rent;
            } else {
                $room->rent = [];
            }

            return $room;
        });

        return response()->json([
            'success' => true,
            'rooms'   => $rooms,
        ], 200);
    }
    /**
     * Devuelve informacion de cuartos disponibles
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|\Modules\Hotel\Models\HotelRoom[]
     */
    private function getRooms()
    {
        $rooms = HotelRoom::with('category', 'floor', 'rates', 'establishment')
            ->where('establishment_id',auth()->user()->establishment_id);

        if (request('hotel_floor_id')) {
            $rooms->where('hotel_floor_id', request('hotel_floor_id'));
        }
        if (request('status')) {
            $rooms->where('status', request('status'));
        }

        $rooms->orderBy('name');
        return $rooms->get()->each(function ($room) {
            if ($room->status === 'OCUPADO') {
                $rent = HotelRent::where('hotel_room_id', $room->id)
                    ->orderBy('id', 'DESC')
                    ->first();
                $room->rent = $rent;
            } else {
                $room->rent = [];
            }

            return $room;
        });
    }

    public function getItem($id)
    {
        $rent = HotelRent::findOrFail($id);

        $item = $rent->items->where('type', 'HAB')->where('payment_status', 'PAID')->first();
        $item_debt = $rent->items->where('type', 'HAB')->where('payment_status', 'DEBT')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'item' => $item,
                'item_debt' => $item_debt
            ],
            'message'   => "Datos encontrados",
        ], 200);
    }

    public function changeUserEstablishment(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $user->establishment_id = $request->establishment_id;
        $user->save();

        return response()->json([
            'success' => true,
            'message'   => "Establecimiento actualizado con éxito",
        ], 200);
    }
}
